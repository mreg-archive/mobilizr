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

namespace Mreg;

use mreg\Exception;

/**
 * Legacy code from mreg....
 *
 * 
 * Mobilize using mail, sms or pdf:s. The Mobilizr directory must be in the
 * include path.
 * @uses \Mobilizr\Mobilizr
 * @uses \Mobilizr\Template
 * @uses \Mobilizr\Strategy
 * @param string $tmplId
 * @param array $strategies Collection of strategy objects
 * @param array $targets
 * @param array $globalVals
 * @return array Execution report
 * @throws Exception If template can not be found
 */
function mobilize(
    $tmplId,
    array $strategies,
    array $targets,
    array $globalVals = null
) {
    assert('is_string($tmplId)');

    $mobilizr = new \Mobilizr\Mobilizr(\Mobilizr\Mobilizr::SILENT_ERRORS);

    $tmpl = new TwigTemplate();
    $tmpl->setAuthUser(getAuthUser());
    $tmpl->name = $tmplId;
    if ( $tmpl->find() == 0 ) {
        throw new Exception('mobilize(): Kan ej hitta mall');
    }
    $mobilizr->setTemplate($tmpl);

    if ( is_array($globalVals) ) {
        $mobilizr->addGlobalVals($globalVals);
    }

    foreach ($strategies as $strategy) {
        $mobilizr->addStrategy($strategy);
    }

    foreach ($targets as $target) {
        $mobilizr->addTarget($target);
    }

    $mobilizr->send();

    return $mobilizr->getReport();
}


/**
 * Create an Mreg mail strategy
 * @return \Mobilizr\Mail
 */
function createMailStrategy()
{
    // TODO att skicka mail är brutet, har tagit bort mitt lösenord
    // väntar på en sendmail wrapper...
    require_once('swift_required.php');
    $carrier = new \Mobilizr\Carrier_Smtp(
        array(
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'encrypt' => 'ssl',
            'user' => 'hannes.forsgard@gmail.com',
            'pswd' => ''
        )
    );
    $mstrategy = new \Mobilizr\Strategy_Mail($carrier);
    $mstrategy->setFrom('hannes.forsgard@gmail.com', 'Mreg Test');
    return $mstrategy;
}


/**
 * Create an Mreg pdf strategy
 * @param \Mobilizr\PdfMailbox &$mailbox
 * @return \Mobilizr\Pdf
 */
function createPdfStrategy(&$mailbox)
{
    $mailbox = new \Mobilizr\Carrier_PdfMailbox();
    $strategy = new \Mobilizr\Strategy_Pdf($mailbox);
    return $strategy;
}
