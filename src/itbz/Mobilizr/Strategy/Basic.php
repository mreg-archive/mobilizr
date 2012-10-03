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
 * Mobilizr strategy interface.
 * 
 * Each strategy object represents one Mobilizr node (a letter, sms, mail, etc).
 * It handles a target, a title and a body.
 * 
 * @package Mobilizr
 */
interface Strategy_Basic
{
    /**
     * Set carrier
     * 
     * @param Carrier_Basic $carrier
     * 
     * @return void
     */
    public function setCarrier(Carrier_Basic $carrier);

    /**
     * Get carrier (used in logging)
     * 
     * @return Carrier
     */
    public function getCarrier();

    /**
     * Set node target
     * 
     * @param Target $target
     * 
     * @return bool TRUE if target is accepted, FALSE otherwise
     */
    public function setTarget(Target $target);

    /**
     * Get string describing addressee (used in logging)
     * 
     * @return string
     */
    public function getAddressee();

    /**
     * Set node title
     * 
     * @param string $title
     * 
     * @return void
     */
    public function setTitle($title);

    /**
     * Set node message
     * 
     * @param string $msg
     * 
     * @return void
     */
    public function setMessage($msg);

    /**
     * Get economc cost of sending node
     * 
     * @return float
     */
    public function getCost();

    /**
     * Clear node for reuse
     * 
     * @return void
     */
    public function clear();

    /**
     * Send node
     * 
     * @return void
     * 
     * @throws MobilizrException if unable to send message
     */
    public function send();
}
