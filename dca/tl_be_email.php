<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright  Marko Cupic 2012
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
 */

$GLOBALS['TL_DCA']['tl_be_email'] = array(
    // Config
    'config' => array(
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
                'pid' => 'index',
            )
        ),
        'ptable' => 'tl_user',
        'dataContainer' => 'Table',
        'enableVersioning' => false,
        'doNotDeleteRecords' => false,
        'oncreate_callback' => array(
            array(
                'tl_be_email',
                'onCreateCallback'
            ),
        ),
        'oncopy_callback' => array(
            array(
                'tl_be_email',
                'onCopyCallback'
            ),
        ),
        'onload_callback' => array(
            array(
                'tl_be_email',
                'setPalette'
            ),
            array(
                'tl_be_email',
                'onLoadCbCheckPermission'
            ),
        ),
        'onsubmit_callback' => array(
            array(
                'tl_be_email',
                'onSubmitCbSendEmail'
            )
        )
    ),
    // Buttons callback
    'edit' => array(
        'buttons_callback' => array(array('tl_be_email', 'buttonsCallback'))
    ),
    // List
    'list' => array(
        'sorting' => array(
            'fields' => array('tstamp DESC'),
            'filter' => array(
                array(
                    'pid=?',
                    \BackendUser::getInstance()->id
                )
            ),
            'panelLayout' => 'filter;search,limit',

        ),
        'label' => array(
            'fields' => array(
                'subject',
                'recipientsTo'
            ),
            //'format' => '%s  <span style="color:#b3b3b3; padding-left:3px;">(%s)</span>',
            'label_callback' => array('tl_be_email', 'labelCallback'),
        ),
        'global_operations' => array(
            'all' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations' => array(
            'edit' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            )
        )
    ),
    // Palettes
    'palettes' => array(
        '__selector__' => array('addAttachment'),
        'sentEmail' => 'summary',
        'default' => '{recipients_legend:hide},recipientsTo,recipientsCc,recipientsBcc;{message_legend},subject,content;{attachment_legend},addAttachment;'
    ),
    // Subpalettes
    'subpalettes' => array
    (
        'addAttachment' => 'attachment',
    ),
    // Fields
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array(
            'default' => \BackendUser::getInstance()->id,
            'foreignKey' => 'tl_user.username',
            'relation' => array(
                'type' => 'belongsTo',
                'load' => 'eager'
            ),
            'eval' => array('doNotShow' => true),
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'recipientsTo' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsTo'],
            'exclude' => false,
            'search' => true,
            'sorting' => true,
            'filter' => false,
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'doNotSaveEmpty' => true
            ),
            'sql' => "text NOT NULL"
        ),
        'recipientsCc' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsCc'],
            'exclude' => false,
            'search' => true,
            'sorting' => true,
            'filter' => false,
            'inputType' => 'text',
            'eval' => array(),
            'sql' => "text NOT NULL"
        ),
        'recipientsBcc' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsBcc'],
            'exclude' => false,
            'search' => true,
            'sorting' => true,
            'filter' => false,
            'inputType' => 'text',
            'eval' => array(),
            'sql' => "text NOT NULL"
        ),
        'subject' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['subject'],
            'exclude' => false,
            'search' => true,
            'sorting' => true,
            'filter' => false,
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'doNotSaveEmpty' => true,
                'style' => ' width:95%; '
            ),
            'sql' => "text NOT NULL"
        ),
        'content' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['content'],
            'exclude' => false,
            'search' => true,
            'sorting' => true,
            'filter' => false,
            'inputType' => 'textarea',
            'load_callback' => array(
                array(
                    'tl_be_email',
                    'base64decode'
                )
            ),

            'eval' => array(
                'decodeEntities' => false,
                'preserveTags' => true,
                'allowHtml' => true,
                'mandatory' => true,
                'doNotSaveEmpty' => true,
                'style' => ' width:95%; ',
            ),
            'sql' => "longtext NOT NULL"
        ),
        'addAttachment' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['addAttachment'],
            'exclude' => false,
            'inputType' => 'checkbox',
            'eval' => array('doNotShow' => true, 'submitOnChange' => true),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'attachment' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['attachment'],
            'exclude' => false,
            'inputType' => 'fileTree',
            'eval' => array('multiple' => true, 'fieldType' => 'checkbox', 'files' => true, 'mandatory' => true),
            'sql' => "blob NULL",
        ),
        'emailNotSent' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['emailNotSent'],
            'filter' => true,
            'sql' => "char(1) NOT NULL default ''"
        ),
        'summary' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_be_email']['summary'],
            'exclude' => false,
            'inputType' => 'textarea',
            'input_field_callback' =>
                array(
                    'tl_be_email', 'generateSummary',
                ),
            'eval' => array(
                'doNotShow' => true,
                'doNotCopy' => true,
            ),
            'sql' => "longtext NOT NULL"
        ),
    )
);


