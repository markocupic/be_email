<?php

/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */

// Manipulate palette
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('be_email_legend', 'global_legend')
    ->addField(array('address_popup_settings'), 'be_email_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings');


/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['address_popup_settings'] = array
(
    'label'	=>	&$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings'],
    'inputType'	=> 'select',
    'options'	=>  array('select_members_and_users','select_users_only','select_members_only'),
    'reference' =>  &$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings_reference'],
    'default'	=>	'select_members_and_users',
    'eval'		=>	array('tl_class'=>'clr')
);
