<?php

declare(strict_types=1);

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Manipulate palette
PaletteManipulator::create()
    ->addLegend('be_email_legend', 'global_legend')
    ->addField(['address_popup_settings'], 'be_email_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings');

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['address_popup_settings'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings'],
    'inputType' => 'select',
    'options'   => ['select_members_and_users', 'select_users_only', 'select_members_only'],
    'reference' => &$GLOBALS['TL_LANG']['tl_settings']['address_popup_settings_reference'],
    'default'   => 'select_members_and_users',
    'eval'      => ['tl_class' => 'clr'],
];
