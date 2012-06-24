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
 * Sending pdfs requires a mailbox.
 * @uses Carrier_PdfMailbox
 * @uses \PhPdf\HtmlConverter
 * @package Mobilizr
 */
class Strategy_Pdf implements Strategy_Basic
{

    /**
     * Object receiving sent PDFs
     * @var Carrier $carrier
     */
    private $carrier;


    /**
     * Message
     * @var string $msg
     */
    private $msg;


    /**
     * Message addressee
     * @var string $addressee
     */
    private $addressee;


    /**
     * Construct and set mailbox
     * @param Carrier_PdfMailbox $carrier
     */
    public function __construct(Carrier_PdfMailbox $carrier){
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
     * Set node address. $target->getAddress() must return a non-empty string
     * for $target to be accepted.
     * NOTE: PrintPdf does not render addresses. It is up to template to print
     * address on paper. 
     * @param Target $target
     * @return bool TRUE if message is accepted, FALSE otherwise
     */
    public function setTarget(Target $target){
        $address = $target->getAddress();
        assert('is_string($address) /* $target->getAdress() returns string? */');
        if ( empty($address) ) return false;
        $this->addressee = $target->getName();
        assert('is_string($this->addressee) /* $target->getName() returns string? */');
        return true;
    }


    /**
     * Get string describing addressee (used in logging)
     * @return string
     */
    public function getAddressee(){
        assert('isset($this->addressee) /* setTarget() called? */');
        return $this->addressee;
    }    


    /**
     * Set node title
     * NOTE: PrintPdf does not render title.
     * @param string $title
     * @return void
     */
    public function setTitle($title){
        assert('is_string($title)');
    }


    /**
     * Set node message
     * @param string $msg HTML content will be parsed.
     * @return void
     */
    public function setMessage($msg){
        assert('is_string($msg)');
        $this->msg = $msg;
    }

    
    /**
     * Get economc cost of sending node
     * @return float
     */
    public function getCost(){
        return 0.0;
    }


    /**
     * Clear node for reuse
     * @return void
     */
    public function clear(){
        unset($this->msg, $this->addressee);
    }


    /**
     * Create PDF and send to mailbox
     * @uses \PhPdf\HtmlConverter
     * @return void
     */
    public function send(){
        assert('isset($this->msg) /* setMessage() called? */');
        assert('isset($this->addressee) /* setTarget() called? */');
        $pdf = new \PhPdf\HtmlConverter($this->msg);
        $pdf->setHeader('[page]([topage])', 'right');
        $this->carrier->send($pdf->get());
    }

}
