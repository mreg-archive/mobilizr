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
 * Sending pdfs requires a mailbox.
 * 
 * @package Mobilizr
 *
 * @todo HtmlConverter should be injected as a dependency
 */
class Strategy_Pdf implements Strategy_Basic
{
    /**
     * Object receiving sent PDFs
     * 
     * @var Carrier
     */
    private $carrier;

    /**
     * Message
     * 
     * @var string
     */
    private $msg;

    /**
     * Message addressee
     * 
     * @var string
     */
    private $addressee;

    /**
     * Construct and set mailbox
     * 
     * @param Carrier_PdfMailbox $carrier
     */
    public function __construct(Carrier_PdfMailbox $carrier)
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
     * Get carrier (used in logging)
     * 
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Set node address
     * 
     * $target->getAddress() must return a non-empty string for $target to be
     * accepted. NOTE: PrintPdf does not render addresses. It is up to template
     * to print address on paper.
     * 
     * @param Target $target
     * 
     * @return bool TRUE if message is accepted, FALSE otherwise
     */
    public function setTarget(Target $target)
    {
        $address = $target->getAddress();
        assert('is_string($address) /* $target->getAdress() returns string? */');

        if (empty($address)) {
            return false;
        }

        $this->addressee = $target->getName();
        assert('is_string($this->addressee) /* $target->getName() returns string? */');

        return true;
    }

    /**
     * Get string describing addressee (used in logging)
     * 
     * @return string
     */
    public function getAddressee()
    {
        assert('isset($this->addressee) /* setTarget() called? */');
        return $this->addressee;
    }

    /**
     * Set node title
     * 
     * NOTE: PrintPdf does not render title.
     * 
     * @param string $title
     * 
     * @return void
     */
    public function setTitle($title)
    {
        assert('is_string($title)');
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
        unset($this->msg, $this->addressee);
    }

    /**
     * Create PDF and send to mailbox
     * 
     * @return void
     */
    public function send()
    {
        assert('isset($this->msg) /* setMessage() called? */');
        assert('isset($this->addressee) /* setTarget() called? */');
        $pdf = new \PhPdf\HtmlConverter($this->msg);
        $pdf->setHeader('[page]([topage])', 'right');
        $this->carrier->send($pdf->get());
    }
}
