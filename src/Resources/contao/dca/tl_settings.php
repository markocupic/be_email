<?php

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Manipulate palette
PaletteManipulator::create()
	->addLegend('be_email_legend', 'global_legend')
	->addField(array('address_popup_settings'), 'be_email_legend', PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_settings');

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['address_popup_settings'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings'],
	'inputType' => 'select',
	'options'   => array('select_members_and_users', 'select_users_only', 'select_members_only'),
	'reference' => &$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings_reference'],
	'default'   => 'select_members_and_users',
	'eval'      => array('tl_class' => 'clr')
);
