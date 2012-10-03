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
 * Send mail using Swift
 * 
 * @package Mobilizr
 *
 * @todo Swift should be dependency injected
 */
class Strategy_Mail implements Strategy_Basic
{
    /**
     * Mail from (address, name)
     * 
     * @var array
     */
    private $from;

    /**
     * Mail reply to (address, name)
     * 
     * @var array
     */
    private $replyTo;

    /**
     * Mail return path (where bounces should go) (address, name)
     * 
     * @var array
     */
    private $returnPath;

    /**
     * Mail to address
     * 
     * @var string
     */
    private $toMail;

    /**
     * Mail to name
     * 
     * @var string
     */
    private $toName;

    /**
     * Mail subject
     * 
     * @var string
     */
    private $subj;

    /**
     * Mail message
     * 
     * @var string
     */
    private $msg;

    /**
     * Attachments to send with each mail
     * 
     * @var array
     */
    private $attachments = array();

    /**
     * Construct and set carrier
     * 
     * @param Carrier_Mail $carrier
     */
    public function __construct(Carrier_Mail $carrier)
    {
        $this->setCarrier($carrier);
    }

    /**
     * Set carrier
     * 
     * @param Carrier_Basic $carrier
     * 
     * @return void
     */
    public function setCarrier(Carrier_Basic $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * Get carrier
     * 
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Set node title
     * 
     * @param string $title
     * 
     * @return void
     */
    public function setTitle($title)
    {
        assert('is_string($title)');
        $this->subj = $title;
    }

    /**
     * Set node message
     * 
     * @param string $msg HTML content will be parsed.
     * 
     * @return void
     */
    public function setMessage($msg)
    {
        assert('is_string($msg)');
        $this->msg = $msg;
    }

    /**
     * Set node address
     * 
     * $target->getMail() must return a non-empty string for $target to be
     * accepted.
     * 
     * @param Target $target
     * 
     * @return bool TRUE if message is accepted, FALSE otherwise
     */
    public function setTarget(Target $target)
    {
        $this->toMail = $target->getMail();
        assert('is_string($this->toMail) /* $target->getMail() returns string? */');
        if (empty($this->toMail)) {
            return false;
        }
        $this->toName = $target->getName();

        return true;
    }

    /**
     * Get string describing addressee (used in logging)
     * 
     * @return string
     */
    public function getAddressee()
    {
        assert('isset($this->toMail) /* setTarget() called? */');

        return "{$this->toName} <{$this->toMail}>";
    }

    /**
     * Get economc cost of sending node
     * 
     * @return float
     */
    public function getCost()
    {
        return 0.0;
    }

    /**
     * Clear node for reuse
     * 
     * @return void
     */
    public function clear()
    {
        unset($this->toMail, $this->toName, $this->subj, $this->msg);
    }

    /**
     * Send mail
     * 
     * @return void
     * 
     * @throws Exception if unable to send message
     */
    public function send()
    {
        assert('isset($this->msg) /* setMessage() called? */');
        assert('isset($this->subj) /* setTitle() called? */');
        assert('isset($this->toMail) /* setTarget() called? */');
        assert('isset($this->from) /* setFrom() called? */');

        try {
            $message = \Swift_Message::newInstance();
            $message->setSubject($this->subj);
            $message->setFrom($this->from);
            $message->setTo(array($this->toMail => $this->toName));
            $message->setBody(strip_tags($this->msg));
            $message->addPart($this->msg, 'text/html');

            if (isset($this->replyTo)) {
                $message->setReplyTo($this->replyTo);
            }
            if (isset($this->returnPath)) {
                $message->setReturnPath($this->returnPath);
            }

            foreach ($this->attachments as $attachment) {
                $obj = \Swift_Attachment::newInstance($attachment[0], $attachment[1], $attachment[2]);
                $message->attach($obj);
            }

            $this->carrier->send($message);
        } catch (\Swift_SwiftException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Add an attachment to mail
     * 
     * @param string $data Raw data
     * @param string $fname File name
     * @param string $ctype Content type
     * 
     * @return void
     */
    public function attach($data, $fname, $ctype)
    {
        assert('is_string($data)');
        assert('is_string($fname)');
        assert('is_string($ctype)');
        $this->attachments[] = array($data, $fname, $ctype);
    }

    /**
     * Set from address (only one address is supported)
     * 
     * @param string $mail
     * @param string $name
     * 
     * @return void
     */
    public function setFrom($mail, $name = '')
    {
        assert('is_string($mail)');
        assert('is_string($name)');
        if (!empty($name)) {
            $this->from[$mail] = $name;
        } else {
            $this->from = array($mail);
        }
    }

    /**
     * Set reply to address
     * 
     * @param string $mail
     * @param string $name
     * 
     * @return void
     */
    public function setReplyTo($mail, $name = '')
    {
        assert('is_string($mail)');
        assert('is_string($name)');
        if (!empty($name)) {
            $this->replyTo[$mail] = $name;
        } else {
            $this->replyTo = array($mail);
        }
    }

    /**
     * Set return path address
     * 
     * @param string $mail
     * @param string $name
     * 
     * @return void
     */
    public function setReturnPath($mail, $name = '')
    {
        assert('is_string($mail)');
        assert('is_string($name)');
        if (!empty($name)) {
            $this->returnPath[$mail] = $name;
        } else {
            $this->returnPath = array($mail);
        }
    }
}
