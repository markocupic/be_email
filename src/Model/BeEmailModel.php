<?php

declare(strict_types=1);

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

namespace Markocupic\BeEmail\Model;

use Contao\Model;

/**
 * Reads and writes tl_be_email.
 */
class BeEmailModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_be_email';
}
