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

// Add the fields to the palettes
PaletteManipulator::create()
    ->addField(array('alternate_email', 'alternate_email_2'), 'email', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_user')
    ->applyToPalette('admin', 'tl_user')
;

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
