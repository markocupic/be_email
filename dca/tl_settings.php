<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright  Marko Cupic 2017
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
 */


use Contao\CoreBundle\DataContainer\PaletteManipulator;


PaletteManipulator::create()
    ->addLegend('be_email_legend', 'global_legend')
    ->addField(array('address_popup_settings'), 'be_email_legend', PaletteManipulator::POSITION_APPEND)
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