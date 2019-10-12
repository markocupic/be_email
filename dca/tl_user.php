<?php

/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */

// Add additional fields to tl_user
$GLOBALS['TL_DCA']['tl_user']['fields']['alternate_email'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['alternate_email'],
    'search'    => true,
    'exclude'   => true,
    'sorting'   => true,
    'flag'      => 1,
    'inputType' => 'text',
    'eval'      => array('mandatory' => false, 'rgxp' => 'email', 'tl_class' => 'clr'),
    'sql'       => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_user']['fields']['alternate_email_2'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['alternate_email_2'],
    'search'    => true,
    'exclude'   => true,
    'sorting'   => true,
    'flag'      => 1,
    'inputType' => 'text',
    'eval'      => array('mandatory' => false, 'rgxp' => 'email', 'tl_class' => 'clr'),
    'sql'       => "varchar(255) NOT NULL default ''"
);

// Add the fields to the palettes
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField(array('alternate_email', 'alternate_email_2'), 'email', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_user')
    ->applyToPalette('admin', 'tl_user');
