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
 * SMS base strategy
 * @package Mobilizr
 */
class Strategy_Sms implements Strategy_Basic
{

    /**
     * Cost per sms..
     * @var float $unitCost
     */
    private $unitCost = 0.35;


    /**
     * Message to send
     * @var string $message
     */
    private $message;


    /**
     * Number of target
     * @var string $toNr
     */
    private $toNr;


    /**
     * Name of target
     * @var string $toName
     */
    private $toName;


    /**
     * SMS carrier object
     * @var Carrier $carrier
     */
    private $carrier;


    /**
     * Construct and set carrier
     * @param Carrier_Sms $carrier
     */
    public function __construct(Carrier_Sms $carrier){
        $this->setCarrier($carrier);
    }


    /**
     * Set carrier
     * @param Carrier_Basic $carrier
     * @return void
     */
    public function setCarrier(Carrier_Basic $carrier){
        $this->carrier = $carrier;
    }
    
    
    /**
     * Get carrier (used in logging)
     * @return Carrier
     */
    public function getCarrier(){
        return $this->carrier;
    }


    /**
     * Send sms
     * @throws Exception if unable to send message
     * @see sendSms()
     * @return void
     */
    public function send(){
        assert('isset($this->message) /* setMessage() called? */');
        assert('isset($this->toNr) /* setTarget() called? */');

        $msg = strip_tags($this->message);
        //remove new lines and multiple spaces
        $msg = preg_replace("/[\\n\\r ]+/", ' ', $msg);
        //remove html entities
        $msg = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $msg);

        if ( !$this->carrier->send($msg, $this->toNr) ) {
            throw new Exception('Unexpected SMS error');
        }
    }


    /**
     * Set node target. $target->getMobile() must return a non-empty string
     * for $target to be accepted.
     * @param Target $target
     * @return bool TRUE if target is accepted, FALSE otherwise
     */
    public function setTarget(Target $target){
        $this->toNr = $target->getMobile();
        assert('is_string($this->toNr) /* $target->getMobile() returns string? */');
        if ( empty($this->toNr) ) return false;
        $this->toName = $target->getName();
        return true;
    }


    /**
     * Get string describing addressee (used in logging)
     * @return string
     */
    public function getAddressee(){
        assert('isset($this->toNr) /* setTarget() called? */');
        return "{$this->toName} <{$this->toNr}>";
    }    


    /**
     * Set node title
     * NOTE: Sms does not render title.
     * @param string $title
     * @return void
     */
    public function setTitle($title){
        assert('is_string($title)');
    }


    /**
     * Set node message
     * @param string $msg
     * @return void
     */
    public function setMessage($msg){
        assert('is_string($msg)');
        $this->message = $msg;
    }


    /**
     * Clear node for reuse
     * @return void
     */
    public function clear(){
        unset($this->toNr, $this->toName, $this->message);
    }


    /**
     * UnitCost per sms, text > 160 characters counts multiple times
     * @return float
     */
    public function getCost(){
        return ceil(strlen($this->message)/160) * $this->unitCost;
    }


    /**
     * Set new cost per sms. Defaults to 0.35.
     * @param float $cost
     * @return void
     */
    public function setUnitCost($cost){
        assert('is_float($cost)');
        $this->unitCost = $cost;
    }

}
