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
 * Mobilizr target interface. Each target object represents a message receiver.
 * @package Mobilizr
 */
interface Target
{

    /**
     * Get target address (for snail mail)
     * @return string
     */
    public function getAddress();


    /**
     * Get target mobile (for sms)
     * @return string
     */
    public function getMobile();


    /**
     * Get target mail
     * @return string
     */
    public function getMail();


    /**
     * Get target name
     * @return string
     */
    public function getName();

}