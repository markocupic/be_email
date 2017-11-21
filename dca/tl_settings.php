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
 
 
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{be_email_legend:hide},address_popup_settings;';


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

