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
 * Send mail using smtp
 * 
 * @package Mobilizr
 *
 * @todo Swift should be dependency injected
 */
class Carrier_Smtp implements Carrier_Mail
{
    /**
     * Default connection settings
     * 
     * @var array
     */
    private static $default = array(
        'host' => '',
        'port' => '',
        'encrypt' => '',
        'user' => '',
        'pswd' => ''
    );

    /**
     * Swift mailer object
     * 
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Construct SMPT mailer.
     * 
     * @param array $settings valid keys: host, port, encrypt, user, pswd
     */
    public function __construct(array $settings = null)
    {
        if (is_null($settings)) {
            $settings = array();
        }
        $settings = array_merge(self::$default, $settings);
        $transport = \Swift_SmtpTransport::newInstance($settings['host'], $settings['port'], $settings['encrypt']);
        $transport->setUsername($settings['user']);
        $transport->setPassword($settings['pswd']);
        $this->mailer = \Swift_Mailer::newInstance($transport);
    }

    /**
     * Send Swift SMPT message
     * 
     * @param \Swift_Message $message Message to send
     * @param string $address Not used for SMPT
     * 
     * @return void
     */
    public function send($message, $address = '')
    {
        assert('is_a($message, "\Swift_Message")');
        $this->mailer->send($message);
    }
}
