<?php
/**
 * This file is part of the Mobilizr package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package Mobilizr
 */

namespace Mobilizr;

/**
 * Package to send custom messages to multiple targets using multiple channels
 *
 * Load Mobilizr with a template. The template is the basic message that will
 * be sent to all targets. See \Mobilizr\Template.
 *
 * Load targets. For each target template will be compiled using target as
 * key - value carrier.
 *
 * Load any number of strategies. On send Mobilizr will try to match targets
 * with strategies, sending the compiled message with the first matching
 * strategy. See \Mobilizr\Strategy.
 *
 * Mobilizr requires PHP >= 5.3
 *
 * @package Mobilizr
 *
 * @todo Use monolog to keep track of sent messages and errors
 */
class Mobilizr
{
    /**
     * Loud error reporting flag
     */
    const LOUD_ERRORS = 1;

    /**
     * Silent error reporting flag
     */
    const SILENT_ERRORS = 2;

    /**
     * Filled with strategy objects during prepare()
     * 
     * @var array
     */
    private $connections = array();

    /**
     * Message ready to send flag
     * 
     * TRUE if prepare has been runned an no changes has been made
     * 
     * @var bool
     */
    private $readyToSend = false;

    /**
     * Template to build and send to targets
     * 
     * @var Template
     */
    private $template;

    /**
     * Subjects to mobilize
     * 
     * @var array
     */
    private $targets = array();

    /**
     * Additional values compiled in template for all targets
     * 
     * @var array
     */
    private $globalVals = array();

    /**
     * Prototype objects to create connections from during prepare()
     * 
     * @var array
     */
    private $strategyPrototypes = array();

    /**
     * List of strings describing error states
     * 
     * @var array
     */
    private $errors = array();

    /**
     * List of strings describing successfully sent messages
     * 
     * @var array
     */
    private $log = array();

    /**
     * Construct and set error reporting
     * 
     * @see setErrorReporting()
     * 
     * @param int $reporting
     */
    public function __construct($reporting = self::LOUD_ERRORS)
    {
        $this->setErrorReporting($reporting);
    }

    /**
     * Set error reporting
     * 
     * self::LOUD_ERRORS forces exceptions to be thrown when a message does not
     * send. self::SILENT_ERRORS supress the exceptinos, the same information
     * may be accessed via getReport().
     * 
     * @see getReport()
     * 
     * @param int $reporting
     * 
     * @return void
     */
    public function setErrorReporting($reporting)
    {
        assert('is_int($reporting)');
        assert('$reporting==self::LOUD_ERRORS || $reporting==self::SILENT_ERRORS');
        $this->reporting = $reporting;
    }

    /**
     * Load template
     * 
     * @param Template $template
     * 
     * @return void
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
        $this->readyToSend = false;
    }

    /**
     * Load target
     * 
     * @param Target $target
     * 
     * @return void
     */
    public function addTarget(Target $target)
    {
        $this->targets[] = $target;
        $this->readyToSend = false;
    }

    /**
     * Get loaded targets
     * 
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Clear loeaded targets
     * 
     * @return void
     */
    public function clearTargets()
    {
        $this->targets = array();
        $this->readyToSend = false;
    }

    /**
     * Add additional values compiled in template for all targets
     * 
     * @param array
     */
    public function addGlobalVals(array $vals)
    {
        $this->globalVals = array_merge($this->globalVals, $vals);
        $this->readyToSend = false;
    }

    /**
     * Get loaded global values
     * 
     * @return array
     */
    public function getGlobalVals()
    {
        return $this->globalVals;
    }

    /**
     * Clear global template values
     * 
     * @return void
     */
    public function clearGlobalVals()
    {
        $this->globalVals = array();
        $this->readyToSend = false;
    }

    /**
     * Add a strategy prototype
     * 
     * The order of added strategies matters, for each target the first matching
     * strategy will be used.
     * 
     * @param Strategy_Basic $strategy
     * 
     * @return void
     */
    public function addStrategy(Strategy_Basic $strategy)
    {
        $this->strategyPrototypes[] = $strategy;
        $this->readyToSend = false;
    }

    /**
     * Clear loaded strategies
     * 
     * @return void
     */
    public function clearStrategies()
    {
        $this->strategyPrototypes = array();
        $this->readyToSend = false;
    }

    /**
     * Reset object
     * 
     * @return void
     */
    public function clear()
    {
        $this->clearTargets();
        $this->clearStrategies();
        $this->clearGlobalVals();
        $this->connections = array();
        $this->errors = array();
        $this->log = array();
    }

    /**
     * Create connections. For each target:
     *
     * - Compile template using target
     * - Iterate over strategies until one is accepted
     * - Set compiled text to strategy.
     *
     * @return void
     */
    private function prepare()
    {
        assert('isset($this->template) /* setTemplate() called? */');

        foreach ($this->targets as $target) {
            $values = $this->globalVals;
            $values['target'] = $target;
            $message = $this->template->doCompile($values);

            $targetHasStrategy = false;
            foreach ($this->strategyPrototypes as $prototype) {
                $accepted = $prototype->setTarget($target);
                assert('is_bool($accepted) /* setTarget() returns boolean? */');
                if ($accepted) {
                    $prototype->setMessage($message);
                    $prototype->setTitle($this->template->getTmplTitle());
                    $this->connections[] = clone $prototype;
                    $targetHasStrategy = true;
                    break;
                }
            }
            if (!$targetHasStrategy) {
                $this->errors[] = "Unable to find communication strategy for target '{$target->getName()}'";
            }

        }

        $this->readyToSend = true;
    }

    /**
     * Send all connections
     * 
     * @return void
     * 
     * @throws Exception if unable to send any connection (successful connections are always sent).
     */
    public function send()
    {
        if (!$this->readyToSend) {
            $this->prepare();
        }

        foreach ($this->connections as $conn) {
            $class = get_class($conn->getCarrier());
            $addresse = $conn->getAddressee();
            assert('is_string($addresse) /* getAddressee() returns string? */');
            try {
                $conn->send();
                $this->log[] = "Message sent to '{$addresse}' using '{$class}'";
            } catch (MobilizrException $e) {
                $str = "'{$e->getMessage()}' for '{$addresse}' using '{$class}'";
                $this->errors[] = $str;
            }
        }

        if (!empty($this->errors) && $this->reporting == self::LOUD_ERRORS) {
            throw new Exception('Some or all connections were not sent', $this->errors);
        }
    }

    /**
     * Get sum cost for all connections
     * 
     * @return float
     */
    public function getCost()
    {
        if (!$this->readyToSend) {
            $this->prepare();
        }
        $sum = 0;
        foreach ($this->connections as $conn) {
            $connCost = $conn->getCost();
            assert('is_float($connCost) /* getCost() returns float? */');
            $sum += $connCost;
        }

        return $sum;
    }

    /**
     * Get execution report
     * 
     * @return array
     */
    public function getReport()
    {
        return array(
            'cost' => $this->getCost(),
            'errors' => $this->errors,
            'success' => $this->log
        );
    }
}
