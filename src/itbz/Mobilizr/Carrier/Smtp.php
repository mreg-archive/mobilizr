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
 * Send mail using smtp
 * @uses Swift
 * @package Mobilizr
 */
class Carrier_Smtp implements Carrier_Mail
{

    /**
     * Default connection settings
     * @var array $default
     */
    static private $default = array(
        'host' => '',
        'port' => '',
        'encrypt' => '',
        'user' => '',
        'pswd' => ''
    );


    /**
     * Swift mailer object
     * @var \Swift_Mailer $mailer
     */
    private $mailer;


    /**
     * Construct SMPT mailer.
     * @uses \Swift_SmtpTransport
     * @uses \Swift_Mailer
     * @param array $settings valid keys: host, port, encrypt, user, pswd
     */
    public function __construct(array $settings=null){
        if ( is_null($settings) ) $settings = array();
        $settings = array_merge(self::$default, $settings);
        $transport = \Swift_SmtpTransport::newInstance($settings['host'], $settings['port'], $settings['encrypt']);
        $transport->setUsername($settings['user']);
        $transport->setPassword($settings['pswd']);
        $this->mailer = \Swift_Mailer::newInstance($transport);
    }


    /**
     * Send Swift SMPT message
     * @uses \Swift_Message
     * @param \Swift_Message $message Message to send
     * @param string $address Not used for SMPT
     * @return void
     */
    public function send($message, $address=''){
        assert('is_a($message, "\Swift_Message")');
        $this->mailer->send($message);
    }

}
