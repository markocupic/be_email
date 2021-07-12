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

namespace Markocupic\BeEmail;

use Contao\Input;
use Markocupic\BeEmail\Model\BeEmailModel;
use Markocupic\BeEmail\Widget\Backend\EmailToWidget;

// Backend modules
$GLOBALS['BE_MOD']['email']['tl_be_email'] = array
(
	'tables' => array('tl_be_email'),
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_be_email'] = BeEmailModel::class;

/**
 * Backend form widgets
 */
$GLOBALS['BE_FFL'][EmailToWidget::TYPE] = EmailToWidget::class;
