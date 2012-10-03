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
 * Mobilizr target interface
 * 
 * Each target object represents a message receiver.
 * 
 * @package Mobilizr
 */
interface Target
{
    /**
     * Get target address (for snail mail)
     * 
     * @return string
     */
    public function getAddress();

    /**
     * Get target mobile (for sms)
     * 
     * @return string
     */
    public function getMobile();

    /**
     * Get target mail
     * 
     * @return string
     */
    public function getMail();

    /**
     * Get target name
     * 
     * @return string
     */
    public function getName();
}
