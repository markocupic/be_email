<?php

/**
 * Class tl_be_email
 */
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

        $this->import('Database');
        $this->import('BackendUser', 'User');

        // Load language-file
        $this->loadLanguageFile('tl_settings');
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

        $strValue = Input::stripSlashes($strValue);
        //$strValue = Input::decodeEntities($strValue);
        $strValue = Input::xssClean($strValue, true);
        //$strValue = Input::stripTags($strValue);
        return $strValue;
    }


    /**
     * onload_callback
     */
    public function onLoadCbCheckPermission()
    {

        // each user can only see his own emails
        if (Input::get('act') == '' || Input::get('id') == '')
        {
            return;
        }

        $db = $this->Database->prepare('SELECT pid FROM tl_be_email WHERE id=?')->execute(Input::get('id'));
        if ($db->pid != $this->User->id)
        {
            $this->redirect($this->getReferer());
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
        $subject = Input::post('subject');
        $email->replyTo($fromMail);
        $email->from = $fromMail;
        $email->subject = $subject;
        $email->html = base64_decode($_POST['content']);

        // Save attachment
        $db = $this->Database->prepare('SELECT * FROM tl_be_email WHERE id=?')->execute(Input::get('id'));

        // Attachment
        if ($db->addAttachment)
        {
            $arrFiles = StringUtil::deserialize($db->attachment, true);
            foreach ($arrFiles as $uuid)
            {
                $objFile = FilesModel::findByUuid($uuid);
                if ($objFile !== null)
                {
                    if (file_exists(TL_ROOT . '/' . $objFile->path))
                    {
                        $email->attachFile(TL_ROOT . '/' . $objFile->path);
                    }
                }
            }
        }

        // Cc
        $cc_recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsCc'), 'recipientsCc'));
        if (count($cc_recipients))
        {
            $email->sendCc($cc_recipients);
        }

        // Bcc
        $bcc_recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsBcc'), 'recipientsBcc'));
        if (count($bcc_recipients))
        {
            $email->sendBcc($bcc_recipients);
        }

        // To
        $recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsTo'), 'recipientsTo'));
        if (count($recipients))
        {
            $email->sendTo($recipients);
        }


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