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

use Contao\BackendUser;
use Contao\DataContainer;
use Contao\Input;
use Markocupic\BeEmail\Dca\TlBeEmail;
use function count;

$GLOBALS['TL_DCA']['tl_be_email'] = [
    // Config
    'config'      => [
        'sql'                => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
        'ptable'             => 'tl_user',
        'dataContainer'      => 'Table',
        'enableVersioning'   => false,
        'doNotDeleteRecords' => false,
        'oncreate_callback'  => [
            [
                TlBeEmail::class,
                'onCreateCallback',
            ],
        ],
        'oncopy_callback'    => [
            [
                TlBeEmail::class,
                'onCopyCallback',
            ],
        ],
        'onload_callback'    => [
            [
                TlBeEmail::class,
                'setPalette',
            ],
            [
                TlBeEmail::class,
                'onLoadCbCheckPermission',
            ],
        ],
        'onsubmit_callback'  => [
            [
                TlBeEmail::class,
                'onSubmitCbSendEmail',
            ],
        ],
    ],
    // Buttons callback
    'edit'        => [
        'buttons_callback' => [[TlBeEmail::class, 'buttonsCallback']],
    ],
    // List
    'list'        => [
        'sorting'           => [
            'fields'      => ['tstamp DESC'],
            'filter'      => [
                [
                    'pid=?',
                    BackendUser::getInstance()->id,
                ],
            ],
            'mode'        => DataContainer::MODE_UNSORTED,
            'panelLayout' => 'filter;search,limit',
        ],
        'label'             => [
            'fields'         => [
                'subject',
                'recipientsTo',
            ],
            'label_callback' => [TlBeEmail::class, 'labelCallback'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_be_email']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\')) return false; Backend.getScrollOffset();"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    // Palettes
    'palettes'    => [
        '__selector__' => ['addAttachment'],
        'sentEmail'    => 'summary',
        'default'      => '{recipients_legend:hide},recipientsTo,recipientsCc,recipientsBcc;{message_legend},subject,content;{attachment_legend},addAttachment',
    ],
    // Subpalettes
    'subpalettes' => [
        'addAttachment' => 'attachment',
    ],
    // Fields
    'fields'      => [
        'id'            => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'           => [
            'default'    => BackendUser::getInstance()->id,
            'foreignKey' => 'tl_user.username',
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
            'eval'       => ['doNotShow' => true],
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'        => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'recipientsTo'  => [
            'exclude'   => false,
            'search'    => true,
            'sorting'   => true,
            'filter'    => false,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'doNotSaveEmpty' => true],
            'sql'       => 'text NULL',
        ],
        'recipientsCc'  => [
            'exclude'   => false,
            'search'    => true,
            'sorting'   => true,
            'filter'    => false,
            'inputType' => 'text',
            'sql'       => 'text NULL',
        ],
        'recipientsBcc' => [
            'exclude'   => false,
            'search'    => true,
            'sorting'   => true,
            'filter'    => false,
            'inputType' => 'text',
            'sql'       => 'text NULL',
        ],
        'subject'       => [
            'exclude'   => false,
            'search'    => true,
            'sorting'   => true,
            'filter'    => false,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'doNotSaveEmpty' => true],
            'sql'       => 'text NULL',
        ],
        'content'       => [
            'exclude'       => false,
            'search'        => true,
            'sorting'       => true,
            'filter'        => false,
            'inputType'     => 'textarea',
            'load_callback' => [
                [TlBeEmail::class, 'base64decode'],
            ],
            'eval'          => ['decodeEntities' => false, 'preserveTags' => true, 'allowHtml' => true, 'mandatory' => true, 'doNotSaveEmpty' => true],
            'sql'           => 'longtext NULL',
        ],
        'addAttachment' => [
            'exclude'   => false,
            'inputType' => 'checkbox',
            'eval'      => ['doNotShow' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'attachment'    => [
            'exclude'   => false,
            'inputType' => 'fileTree',
            'eval'      => ['multiple' => true, 'fieldType' => 'checkbox', 'files' => true, 'mandatory' => true],
            'sql'       => 'blob NULL',
        ],
        'emailNotSent'  => [
            'filter' => true,
            'sql'    => "char(1) NOT NULL default ''",
        ],
        'summary'       => [
            'exclude'              => false,
            'inputType'            => 'textarea',
            'input_field_callback' => [TlBeEmail::class, 'generateSummary'],
            'eval'                 => ['doNotShow' => true, 'doNotCopy' => true],
            'sql'                  => 'longtext NULL',
        ],
    ],
];

if (isset($_GET) && 5 === count($_GET) && 'tl_be_email' === Input::get('do') && 'edit' === Input::get('act') && isset($_GET['id'])) {
    $GLOBALS['TL_DCA']['tl_be_email']['fields']['recipientsTo']['inputType'] = 'email_tag';
    $GLOBALS['TL_DCA']['tl_be_email']['fields']['recipientsCc']['inputType'] = 'email_tag';
    $GLOBALS['TL_DCA']['tl_be_email']['fields']['recipientsBcc']['inputType'] = 'email_tag';
}
