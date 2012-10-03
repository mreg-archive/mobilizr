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
 * Mailbox for receiving PDFs.
 *
 * If more strategies require to use of mailboxes it might be an idéa to
 * put the basic functionalilty in a generic class. Everything exept
 * fetchAll() is generic already.
 *
 * NOTE: The PhPdf parent directory must be in the include path.
 *
 * @package Mobilizr
 * 
 * @todo PdfMerger should be injected as a dependency
 */
class Carrier_PdfMailbox implements \Iterator, Carrier_Basic
{
    /**
     * Internal mailbox representation
     * 
     * @var array
     */
    private $box = array();

    /**
     * Send a message
     * 
     * @param string $pdf
     * @param string $address Not used
     * 
     * @return void
     */
    public function send($pdf, $address = '')
    {
        assert('is_string($pdf)');
        $this->box[] = $pdf;
    }

    /**
     * TRUE if mailbox contains mail, FALSE otherwise
     * 
     * @return bool
     */
    public function hasMail()
    {
        return !empty($this->box);
    }

    /**
     * Fetch one mail
     * 
     * @return mixed
     */
    public function fetch()
    {
        return array_pop($this->box);
    }

    /**
     * Fetch all mails in mailbox, concatenated.
     * 
     * @return string The PDF in a binary string
     */
    public function fetchAll()
    {
        $merger = new \PhPdf\Merger();

        foreach ($this->box as $mail) {
            $merger->add($mail);
        }

        $this->box = array();
        return $merger->get();
    }

    /**
     * Iterator interface
     * 
     * @return void
     */
    public function rewind()
    {
        reset($this->box);
    }

    /**
     * Iterator interface
     * 
     * @return mixed
     */
    public function current()
    {
        return current($this->box);
    }

    /**
     * Iterator interface
     * 
     * @return int
     */
    public function key()
    {
        return key($this->box);
    }

    /**
     * Iterator interface
     * 
     * @return void
     */
    public function next()
    {
        next($this->box);
    }

    /**
     * Iterator interface
     * 
     * @return bool
     */
    public function valid()
    {
        return !!current($this->box);
    }
}
