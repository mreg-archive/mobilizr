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
 * Mobilizr template interface
 * 
 * @package Mobilizr
 */
interface Template
{
    /**
     * Compile loaded template using $values
     * 
     * @param array $values
     * 
     * @return string
     */
    public function doCompile(array $values);

    /**
     * Return template title
     * 
     * @return string
     */
    public function getTmplTitle();
}
