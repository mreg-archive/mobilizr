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
 * Mailbox for receiving PDFs.
 *
 * If more strategies require to use of mailboxes it might be an idéa to
 * put the basic functionalilty in a generic class. Everything exept
 * fetchAll() is generic already.
 *
 * NOTE: The PhPdf parent directory must be in the include path.
 *
 * @uses \PhPdf\Merger
 * @package Mobilizr
 */
class Carrier_PdfMailbox implements \Iterator, Carrier_Basic
{

    /**
     * Internal mailbox representation
     * @var array $box
     */
    private $box = array();


    /**
     * Send a message.
     * @param string $pdf
     * @param string $address Not used
     * @return void
     */
    public function send($pdf, $address=''){
        assert('is_string($pdf)');
        $this->box[] = $pdf;
    }


    /**
     * TRUE if mailbox contains mail, FALSE otherwise
     * @return bool
     */
    public function hasMail(){
        return !empty($this->box);
    }


    /**
     * Fetch one mail
     * @return mixed
     */
    public function fetch(){
        return array_pop($this->box);
    }


    /**
     * Fetch all mails in mailbox, concatenated.
     * @uses \PhPdf\Merger
     * @return string The PDF in a binary string
     */
    public function fetchAll(){
        $merger = new \PhPdf\Merger();

        foreach ( $this->box as $mail ) {
            $merger->add($mail);
        }

        $this->box = array();
        return $merger->get();
    }


    /**
     * Iterator interface
     * @return void
     */
    function rewind() {
        reset($this->box);
    }


    /**
     * Iterator interface
     * @return mixed
     */
    function current() {
        return current($this->box);
    }


    /**
     * Iterator interface
     * @return int
     */
    function key() {
        return key($this->box);
    }


    /**
     * Iterator interface
     * @return void
     */
    function next() {
        next($this->box);
    }


    /**
     * Iterator interface
     * @return bool
     */
    function valid() {
        return !!current($this->box);
    }

}
