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
 * Generic Mobilizr exception
 * 
 * @package Mobilizr
 */
class Exception extends \Exception
{
    /**
     * Errors array
     *
     * @var array
     */
    private $errors;

    /**
     * Generic Mobilizr exception
     * 
     * @param string $message
     * @param array $errors
     */
    public function __construct($message, array $errors = null)
    {
        assert('is_string($message)');
        if (is_null($errors)) {
            $errors = array();
        }
        $this->errors = $errors;

        return parent::__construct($message);
    }

    /**
     * Get array of strings describing errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
