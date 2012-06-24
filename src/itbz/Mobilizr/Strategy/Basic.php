<?php
/**
 * This file is part of Mobilizr.
 *
 * Mobilizr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mobilizr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mobilizr.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @copyright Copyright (c) 2011, Hannes Forsgård
 * @license http://www.gnu.org/licenses/ GNU Public License
 *
 * @package Mobilizr
 */
namespace Mobilizr;


/**
 * Mobilizr strategy interface. Each strategy object represents one Mobilizr node
 * (a letter, sms, mail, etc). It handles a target, a title and a body.
 * @package Mobilizr
 */
interface Strategy_Basic
{

    /**
     * Set carrier
     * @param Carrier_Basic $carrier
     * @return void
     */
    public function setCarrier(Carrier_Basic $carrier);
    
    
    /**
     * Get carrier (used in logging)
     * @return Carrier
     */
    public function getCarrier();


    /**
     * Set node target
     * @param Target $target
     * @return bool TRUE if target is accepted, FALSE otherwise
     */
    public function setTarget(Target $target);


    /**
     * Get string describing addressee (used in logging)
     * @return string
     */
    public function getAddressee();


    /**
     * Set node title
     * @param string $title
     * @return void
     */
    public function setTitle($title);


    /**
     * Set node message
     * @param string $msg
     * @return void
     */
    public function setMessage($msg);

    
    /**
     * Get economc cost of sending node
     * @return float
     */
    public function getCost();


    /**
     * Clear node for reuse
     * @return void
     */
    public function clear();


    /**
     * Send node
     * @throws MobilizrException if unable to send message
     * @return void
     */
    public function send();

}
