<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @copyright  Marko Cupic 2012
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
 */


// Add additional fields to tl_user
$GLOBALS['TL_DCA']['tl_user']['fields']['alternate_email'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['alternate_email'],
    'search' => true,
    'exclude' => true,
    'sorting' => true,
    'flag' => 1,
    'inputType' => 'text',
    'eval' => array('mandatory' => false, 'rgxp' => 'email', 'tl_class' => 'w50'),
    'sql' => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_user']['fields']['alternate_email_2'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['alternate_email_2'],
    'search' => true,
    'exclude' => true,
    'sorting' => true,
    'flag' => 1,
    'inputType' => 'text',
    'eval' => array('mandatory' => false, 'rgxp' => 'email', 'tl_class' => 'w50'),
    'sql' => "varchar(255) NOT NULL default ''"
);

// Add the fields to the palettes
foreach ($GLOBALS['TL_DCA']['tl_user']['palettes'] as $key => $value) {
    if ($key != '__selector__')
    {
        $GLOBALS['TL_DCA']['tl_user']['palettes'][$key] = str_replace('email', 'email,alternate_email,alternate_email_2', $GLOBALS['TL_DCA']['tl_user']['palettes'][$key]);
    }
}
