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
 * Carrier interface.
 * 
 * Carrier objects are responsible for actually sending messages.
 * 
 * @package Mobilizr
 */
interface Carrier_Basic
{
    /**
     * Send a message
     * 
     * @param mixed $message Message to send
     * @param string $address Only needed if address is not compiled into a
     * complex message object
     * 
     * @return void
     */
    public function send($message, $address = '');
}
