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

namespace Markocupic\BeEmail;

use Markocupic\BeEmail\Model\BeEmailModel;
use Markocupic\BeEmail\Widget\Backend\EmailTagWidget;

// Backend modules
$GLOBALS['BE_MOD']['email']['tl_be_email'] = [
    'tables' => ['tl_be_email'],
];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_be_email'] = BeEmailModel::class;

/*
 * Backend form widgets
 */
$GLOBALS['BE_FFL'][EmailTagWidget::TYPE] = EmailTagWidget::class;
