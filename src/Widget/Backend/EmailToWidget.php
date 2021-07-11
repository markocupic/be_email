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

namespace Markocupic\BeEmail\Widget\Backend;

use Contao\BackendTemplate;
use Contao\StringUtil;
use Contao\Widget;

class EmailToWidget extends Widget
{
    public const TYPE = 'email_to';

    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var bool
     */
    protected $blnForAttribute = true;

    /**
     * @var string
     */
    protected $strTemplate = 'be_widget';

    public function generate(): string
    {
        $GLOBALS['TL_JAVASCRIPT'][] = 'assets/contao-component-vue-js/vue/dist/vue.min.js|static';
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/markocupicbeemail/vue/vue-email-tag-input.js';

        $widget = new BackendTemplate('email_to_widget');

        return sprintf(
            $widget->parse(),
            $this->id,
            $this->name,
            $this->id,
            ($this->class ? ' '.$this->class : ''),
            StringUtil::specialchars($this->value),
            $this->id,
            StringUtil::specialchars($this->value),
        );
    }
}
