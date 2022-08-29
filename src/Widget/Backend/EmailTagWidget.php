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

namespace Markocupic\BeEmail\Widget\Backend;

use Contao\BackendTemplate;
use Contao\StringUtil;
use Contao\Widget;

class EmailTagWidget extends Widget
{
    public const TYPE = 'email_tag';

    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var bool
     */
    protected $blnForAttribute = true;

    public function __construct($arrAttributes = null)
    {
        parent::__construct($arrAttributes);
    }

    /**
     * @var string
     */
    protected $strTemplate = 'be_widget';

    public function generate(): string
    {
        $widget = new BackendTemplate('email_tag_widget');

        return sprintf(
            $widget->parse(),
            $this->id,
            ($this->class ? ' '.$this->class : ''),
            $this->name,
            $this->id,
            StringUtil::specialchars($this->value),
            $this->id,
            StringUtil::specialchars($this->value),
        );
    }
}
