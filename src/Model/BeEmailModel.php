<?php

/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */


namespace Markocupic\BeEmail\Model;



use Contao\Model;

/**
 * Reads and writes tl_be_email
 */
class BeEmailModel extends Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_be_email';



}
