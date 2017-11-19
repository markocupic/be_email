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
    'config'   => array(
        'sql'                => array(
            'keys' => array(
                'id'  => 'primary',
                'pid' => 'index',
            )
        ),
        'ptable'             => 'tl_user',
        'dataContainer'      => 'Table',
        'enableVersioning'   => false,
        'doNotDeleteRecords' => false,
        'onload_callback'    => array(

            array(
                'tl_be_email',
                'onLoadCbSetPID'
            ),
            array(
                'tl_be_email',
                'onLoadCbCheckPermission'
            ),
        ),
        'onsubmit_callback'  => array(
            array(
                'tl_be_email',
                'onSubmitCbUpdateEntry'
            ),
            array(
                'tl_be_email',
                'onSubmitCbSendEmail'
            )
        ),
        'ondelete_callback'  => array(
            array(
                'tl_be_email',
                'onDeleteCallback'
            )
        )
    ),
    // Buttons callback
    'edit' =>array(
        'buttons_callback' => array(array('tl_be_email', 'buttonsCallback'))
    ),
    // List
    'list'     => array(
        'sorting'           => array(
            'fields' => array('tstamp DESC'),
            'filter' => array(
                array(
                    'pid=?',
                    \BackendUser::getInstance()->id
                )
            )
        ),
        'label'             => array(
            'fields' => array(
                'subject',
                'recipientsTo'
            ),
            'format' => '%s  <span style="color:#b3b3b3; padding-left:3px;">(%s)</span>'
        ),
        'global_operations' => array(
            'all' => array(
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations'        => array(
            'edit'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'delete' => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_be_email']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_be_email']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array(
        'default' => '{recipients_legend:hide},recipientsTo,recipientsCc,recipientsBcc;{message_legend},subject,content;{attachment_legend},uploaded_files;'
    ),

    // Fields
    'fields'   => array(
        'id'             => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'            => array(
            'default'    => \BackendUser::getInstance()->id,
            'foreignKey' => 'tl_user.username',
            'relation'   => array(
                'type' => 'belongsTo',
                'load' => 'eager'
            ),
            'eval'       => array('doNotShow' => true),
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ),
        'tstamp'         => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'recipientsTo'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsTo'],
            'search'    => true,
            'sorting'   => true,
            'filter'    => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'mandatory'      => true,
                //'class'          => 'address_textarea_to flext growme',
                'doNotSaveEmpty' => true
            ),
            'sql'       => "text NOT NULL"

        ),
        'recipientsCc'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsCc'],
            'search'    => true,
            'sorting'   => true,
            'filter'    => true,
            'inputType' => 'textarea',
            'eval'      => array(
                //'class'          => 'address_textarea_cc flext growme',
                'doNotSaveEmpty' => true
            ),
            'sql'       => "text NOT NULL"
        ),
        'recipientsBcc'  => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_be_email']['recipientsBcc'],
            'search'    => true,
            'sorting'   => true,
            'filter'    => true,
            'inputType' => 'textarea',
            'eval'      => array(
                //'class'          => 'address_textarea_bcc flext growme',
                'doNotSaveEmpty' => true
            ),
            'sql'       => "text NOT NULL"
        ),
        'subject'        => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_be_email']['subject'],
            'search'    => true,
            'sorting'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'      => true,
                'doNotSaveEmpty' => true,
                'style'          => ' width:95%; '
            ),
            'sql'       => "text NOT NULL"
        ),
        'content'        => array(
            'label'         => &$GLOBALS['TL_LANG']['tl_be_email']['content'],
            'search'        => true,
            'sorting'       => true,
            'filter'        => true,
            'inputType'     => 'textarea',
            'load_callback' => array(
                array(
                    'tl_be_email',
                    'base64decode'
                )
            ),

            'eval'          => array(
                'decodeEntities' => false,
                'preserveTags'   => true,
                'allowHtml'      => true,
                'mandatory'      => true,
                //'class'          => 'content flext growme',
                'doNotSaveEmpty' => true,
                'style'          => ' width:95%; ',
                //'rte' => 'tinyNews',
                'helpwizard'     => true,
            ),
            'sql'           => "longtext NOT NULL"
        ),
        'attachment'     => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_be_email']['attachment'],
            'inputType' => 'text',
            'eval'      => array('doNotShow' => true),
            'sql'       => "text NOT NULL"
        ),
        'uploaded_files' => array(
            'input_field_callback' => array(
                'tl_be_email',
                'generateFileUploadField'
            ),
            'eval'                 => array('doNotShow' => true),
            'sql'                  => "text NOT NULL"
        )
    )
);


class tl_be_email extends Backend
{

    public function __construct()
    {

        if ($_POST['content'])
        {
            $strValue = $this->cleanPost($_POST['content']);
            $_POST['content'] = base64_encode($strValue);
        }
        parent::__construct();

        $this->import('Files');
        $this->import('Database');
        $this->import('BackendUser', 'User');

        // Load language-file
        $this->loadLanguageFile('tl_settings');

        // attachment-dir
        new Folder($GLOBALS['TL_CONFIG']['uploadPath'] . '/be_email');

        // define the upload-directory for attachments
        define('BE_EMAIL_UPLOAD_DIR', $GLOBALS['TL_CONFIG']['uploadPath'] . '/be_email');

        // open the popup for the address selection
        if (\Input::get('mode') == 'addAddresses')
        {
            $this->buildPopUp();
            exit;
        }

    }




    // onload callback
    public function onLoadCbSetPID()
    {

        //set the pid of a new dataset (=tl_user->id)
        if (\Input::get('act') == 'edit')
        {
            $result = $this->Database->prepare('UPDATE tl_be_email SET pid=? WHERE id=? AND pid=? AND tstamp=?')->execute($this->User->id, \Input::get('id'), '', '0');
        }
    }


    /**
     * load callback for the content field
     * the content is stored base64_encoded in the database
     * @param $strValue
     * @param $objUser
     * @param null $objDCA
     * @return string
     */
    public function base64decode($strValue)
    {

        if (base64_decode($strValue, true))
        {
            return base64_decode($strValue);
        }
        return $strValue;
    }


    /**
     * @param $strValue
     * @return mixed
     */
    protected function cleanPost($strValue)
    {

        $strValue = \Input::stripSlashes($strValue);
        //$strValue = \Input::decodeEntities($strValue);
        $strValue = \Input::xssClean($strValue, true);
        //$strValue = \Input::stripTags($strValue);
        return $strValue;
    }


    /**
     * onload_callback
     */
    public function onLoadCbCheckPermission()
    {

        // each user can only see his own emails
        if (\Input::get('act') == '' or \Input::get('id') == '')
        {
            return;
        }
        $db = $this->Database->prepare('SELECT pid FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));
        if ($db->pid != $this->User->id)
        {
            $this->redirect('contao/main.php?do=tl_be_email');
        }
    }


    /**
     * onsubmit_callback
     */
    public function onSubmitCbUpdateEntry()
    {

        if ($_FILES['file']['tmp_name'] != '')
        {
            // Fileupload
            $fileKey = md5(microtime() . $_FILES['file']['tmp_name']);
            if ($this->Files->move_uploaded_file($_FILES['file']['tmp_name'], BE_EMAIL_UPLOAD_DIR . '/' . $fileKey))
            {
                // chmod
                $this->Files->chmod(BE_EMAIL_UPLOAD_DIR . '/' . $fileKey, 0644);
                $db = $this->Database->prepare('SELECT attachment FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));
                $arrFiles = array();
                if ($db->attachment != '')
                {
                    $arrFiles = unserialize($db->attachment);
                }
                //store key and filename in tl_be_email
                $arrFiles[$fileKey] = utf8_romanize($_FILES['file']['name']);
                $db = $this->Database->prepare('UPDATE tl_be_email SET attachment=? WHERE id=?')->execute(serialize($arrFiles), \Input::get('id'));
            }
        }


        // if attached files should be deleted
        if (\Input::post('attachment'))
        {
            $arrPost = is_array(\Input::post('attachment')) ? \Input::post('attachment') : array(\Input::post('attachment'));

            // get the attachment-array from tl_be_email
            $db = $this->Database->prepare('SELECT attachment FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));
            $arrFiles = unserialize($db->attachment);

            // delete the files from the server
            foreach ($arrPost as $fileKey)
            {
                if (is_file(TL_ROOT . '/' . BE_EMAIL_UPLOAD_DIR . '/' . $fileKey))
                {
                    $this->Files->delete(BE_EMAIL_UPLOAD_DIR . '/' . $fileKey);
                }
                unset($arrFiles[$fileKey]);
            }

            // rebuild the new attachment-array
            $newArrFiles = array();
            foreach ($arrFiles as $filekey => $filename)
            {
                if (strlen($arrFiles[$filekey]))
                {
                    $newArrFiles[$filekey] = $filename;
                }
            }
            //serialize the new attachment-array and save it into the db
            $db = $this->Database->prepare('UPDATE tl_be_email SET attachment=? WHERE id=?')->execute(serialize($newArrFiles), \Input::get('id'));
        }

    }


    /**
     * onsubmit_callback
     * send email
     */
    public function onSubmitCbSendEmail()
    {
        // the save-button is a fileupload-button
        if (!isset($_POST['saveNclose']))
        {
            return;
        }

        $email = new Email();
        $fromMail = $this->User->email;
        $subject = \Input::post('subject');
        $email->replyTo($fromMail);
        $email->from = $fromMail;
        $email->subject = $subject;
        $email->html = base64_decode($_POST['content']);

        //save attachment
        $arrFiles = array();
        $db = $this->Database->prepare('SELECT attachment FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));

        // Attachment
        if ($db->attachment != '')
        {
            $arrFiles = unserialize($db->attachment);
            foreach ($arrFiles as $filekey => $filename)
            {
                if (file_exists(TL_ROOT . '/' . BE_EMAIL_UPLOAD_DIR . '/' . $filekey))
                {
                    $this->Files->copy(BE_EMAIL_UPLOAD_DIR . '/' . $filekey, 'system/tmp/' . $filename);
                    $email->attachFile(TL_ROOT . '/system/tmp/' . $filename);
                }
            }
        }

        // Cc
        $cc_recipients = array_unique($this->validateEmailAddresses(\Input::post('recipientsCc'), 'recipientsCc'));
        if (count($cc_recipients))
        {
            $email->sendCc($cc_recipients);
        }

        // Bcc
        $bcc_recipients = array_unique($this->validateEmailAddresses(\Input::post('recipientsBcc'), 'recipientsBcc'));
        if (count($bcc_recipients))
        {
            $email->sendBcc($bcc_recipients);
        }

        // To
        $recipients = array_unique($this->validateEmailAddresses(\Input::post('recipientsTo'), 'recipientsTo'));
        if (count($recipients))
        {
            $email->sendTo($recipients);
        }

        // Delete attachment from server
        foreach ($arrFiles as $filekey => $filename)
        {
            // delete file in the tmp-folder
            if (is_file(TL_ROOT . '/system/tmp/' . $filename))
            {
                $this->Files->delete('/system/tmp/' . $filename);
            }
        }
    }


    /**
     * ondelete_callback
     */
    public function onDeleteCallback()
    {

        $db = $this->Database->prepare('SELECT attachment FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));
        $arrFiles = unserialize($db->attachment);
        if (is_array($arrFiles) && count($arrFiles) > 0)
        {
            foreach ($arrFiles as $filekey => $filename)
            {
                if (is_file(TL_ROOT . '/' . BE_EMAIL_UPLOAD_DIR . '/' . $filekey))
                {
                    $this->Files->delete(BE_EMAIL_UPLOAD_DIR . '/' . $filekey);
                }
            }
        }
    }


    /**
     * build popup
     */
    public function buildPopUp()
    {

        $objTemplate = new BackendTemplate('be_email_popup');

        // userBox
        $query = 'SELECT name, email, alternate_email, alternate_email_2 FROM tl_user WHERE email != "" ORDER BY name';
        $result = $this->Database->execute($query);
        $userRows = '';
        $i = 0;
        while ($row = $result->fetchAssoc())
        {
            $arrEmailAddresses = array(
                trim($row['email']),
                trim($row['alternate_email']),
                trim($row['alternate_email_2'])
            );
            // remove double entries and filter empty values
            $arrEmailAddresses = array_filter(array_unique($arrEmailAddresses));
            $userRows .= "<tr class=\"" . ($i % 2 == 0 ? 'odd' : 'even') . "\"><td><a href=\"JavaScript:removeElement(this);\" onclick=\"removeElement(this); sendmail('" . implode('; ', $arrEmailAddresses) . "'); return false;\"><img src=\"../system/modules/be_email/assets/email.svg\" class=\"select-address-icon\"></a></td><td>" . $row['name'] . "</td><td>" . $row['email'] . "</td></tr>\r\n";
            $i++;
        }
        $objTemplate->userAddresses = $userRows;


        // memberBox
        $query = 'SELECT firstname, lastname, email FROM tl_member WHERE email != "" ORDER BY lastname';
        $result = $this->Database->execute($query);
        $memberRows = '';
        $i = 0;
        while ($result->next())
        {
            $memberRows .= "<tr class=\"" . ($i % 2 == 0 ? 'odd' : 'even') . "\"><td class=\"col_0\"><a href=\"JavaScript:removeElement(this);\" onclick=\"removeElement(this); sendmail('" . $result->email . "'); return false;\"><img src=\"../system/modules/be_email/assets/email.svg\" class=\"select-address-icon\"></a></td><td class=\"col_1\">" . $result->firstname . ' ' . $result->lastname . "</td><td class=\"col_2\">" . $result->email . "</td></tr>\r\n";
            $i++;
        }
        $objTemplate->memberAddresses = $memberRows;

        switch ($GLOBALS['TL_CONFIG']['address_popup_settings'])
        {
            case 'select_users_only' :
                $objTemplate->showUsersAddresses = true;
                break;
            case 'select_members_only' :
                $objTemplate->showMembersAddresses = true;
                break;
            default:
                $objTemplate->showUsersAddresses = true;
                $objTemplate->showMembersAddresses = true;
        }

        // output
        echo $objTemplate->parse();
    }


    /**
     * input_field callback
     * @return string
     */
    public function generateFileUploadField()
    {

        // generate the attached files listing
        $objDb = $this->Database->prepare('SELECT attachment FROM tl_be_email WHERE id=?')->execute(\Input::get('id'));
        $strAttachments = '';
        if ($objDb->attachment != '' && count(unserialize($objDb->attachment)) > 0)
        {
            $arrFiles = unserialize($objDb->attachment);
            $arrOptions = array();
            foreach ($arrFiles as $fileKey => $fileName)
            {
                $file = new File(BE_EMAIL_UPLOAD_DIR . '/' . $fileKey);
                $arrOptions[] = array(
                    'value' => $fileKey,
                    'label' => specialchars($fileName . ' [' . $this->getReadableSize($file->size) . ']')
                );
            }
            if (count($arrFiles))
            {
                $widget = new FormCheckBox();
                $widget->name = 'attachment';
                $widget->class = 'attachmentList';
                $widget->options = serialize($arrOptions);
                $widget->addSubmit = true;
                $widget->slabel = $GLOBALS['TL_LANG']['tl_be_email']['removeAttachments'][0];
                $strAttachments .= '<h3><label>' . $GLOBALS['TL_LANG']['tl_be_email']['emailAttachments'][0] . '</label></h3>';
                $strAttachments .= $widget->generate();
            }
        }

        // generate the fileupload form field
        $widget = new FormFileUpload();
        $widget->id = 'fileupload';
        $widget->name = 'file';
        $widget->class = 'tl_upload_file';
        $widget->label = $GLOBALS['TL_LANG']['tl_be_email']['fileupload'][0];
        $widget->slabel = 'upload';
        $widget->addSubmit = true;
        $widget->maxlength = $GLOBALS['TL_CONFIG']['maxFileSize'];
        $strFileuploader = $widget->generateLabel() . $widget->generate();

        // return the html
        $html = '<div id="attachmentBox" class="widget attachment-box">%s%s</div>';
        return sprintf($html, $strAttachments, $strFileuploader);
    }


    /**
     * @param string $strAddresses
     * @param $field
     * @return array
     */
    private function validateEmailAddresses($strAddresses = '', $field)
    {

        $arrEmailAddresses = array();
        $strAddresses = trim(strtolower($strAddresses));
        if ($strAddresses == '')
        {
            $set = array($field => '');
            // update the db
            $this->Database->prepare('UPDATE tl_be_email %s WHERE id=?')->set($set)->execute(Input::get('id'));
            Input::setPost($field, '');
            return $arrEmailAddresses;
        }

        $arrEmailAddresses = array();
        preg_match_all('/\w[-._\w]*\w@\w[-._\w]*\w\.\w{2,6}/i', $strAddresses, $arrEmailAddresses);

        // remove doubble entries
        $arrEmailAddresses = array_unique($arrEmailAddresses[0]);

        // update the db
        $set = array($field => implode('; ', $arrEmailAddresses));
        $this->Database->prepare('UPDATE tl_be_email %s WHERE id=?')->set($set)->execute(Input::get('id'));
        Input::setPost($field, implode('; ', $arrEmailAddresses));

        return $arrEmailAddresses;
    }


    /**
     * parseBackendTemplateHook
     * @param $strContent
     * @param $strTemplate
     * @return mixed
     */
    public function parseBackendTemplateHook($strContent, $strTemplate)
    {

        if (\Input::get('do') == 'tl_be_email' && \Input::get('act') == 'edit' && TL_MODE == 'BE')
        {
            // set the enctype for fileuploads
            $strContent = str_replace('application/x-www-form-urlencoded', 'multipart/form-data', $strContent);

        }

        return $strContent;
    }

    /**
     * buttons_callback
     * @param $arrButtons
     * @param DC_Table $dc
     * @return mixed
     */
    public function buttonsCallback($arrButtons, DC_Table $dc)
    {
        // Disable buttons
        unset($arrButtons['saveNcreate']);
        unset($arrButtons['saveNduplicate']);
        unset($arrButtons['saveNback']);
        $arrButtons['saveNclose'] = '<button type="submit" name="saveNclose" id="saveNclose" class="tl_submit" accesskey="c">' . $GLOBALS['TL_LANG']['tl_be_email']['send_email'] . '</button>';

        return $arrButtons;
    }
}