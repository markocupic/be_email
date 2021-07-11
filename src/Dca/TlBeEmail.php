<?php

declare(strict_types=1);



namespace Markocupic\BeEmail\Dca;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\DC_Table;
use Contao\Email;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Markocupic\BeEmail\Model\BeEmailModel;

/**
 * Class TlBeEmail
 * @package Markocupic\BeEmail\Dca
 */
class TlBeEmail extends Backend
{

    public function __construct()
    {
        if (Input::post('copyAndResendEmail') !== null)
        {
            $objSource = Database::getInstance()
                ->prepare('SELECT * FROM tl_be_email WHERE id=?')
                ->execute(Input::get('id'))
            ;

            if (BackendUser::getInstance()->id !== $objSource->pid)
            {
                throw new AccessDeniedException('You are not allowed to this action.');
            }

            $set = $objSource->row();
            $set['tstamp'] = 0;
            $set['emailNotSent'] = '1';
            unset($set['id']);

            $objInsertStmt = Database::getInstance()
                ->prepare('INSERT INTO tl_be_email %s')
                ->set($set)
                ->execute()
            ;

            if ($objInsertStmt->affectedRows)
            {
                $request = Environment::get('request');
                $redirect = preg_replace('/id=([\d]+)/', 'id=' . $objInsertStmt->insertId, $request);
                unset($_POST);
                Controller::redirect($redirect);
            }
        }

        if ($_POST['content'])
        {
            $strValue = $this->cleanPost($_POST['content']);
            $_POST['content'] = base64_encode($strValue);
        }

        // Load language-file
        Controller::loadLanguageFile('tl_settings');

        parent::__construct();
    }

    /**
     * @param \Contao\DataContainer $dc
     */
    public function setPalette(DataContainer $dc)
    {
        $db = Database::getInstance()
            ->prepare('SELECT * FROM tl_be_email WHERE id=?')
            ->limit(1)
            ->execute($dc->id)
        ;

        if (!$db->emailNotSent)
        {
            $GLOBALS['TL_DCA']['tl_be_email']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_be_email']['palettes']['sentEmail'];
        }
    }

    /**
     * @param $row
     * @return string
     */
    public function labelCallback($row)
    {
        $class = $row['emailNotSent'] ? 'email-not-sent' : 'email-sent';

        $project = $row['emailNotSent'] ? sprintf('<span class="project">[%s]</span>', $GLOBALS['TL_LANG']['tl_be_email']['emailNotSent'][0]) : '';

        return sprintf(
            '<p class="%s"><span class="date">%s</span> %s<br><span class="subject"><strong>%s</strong></span><br><span class="to">TO: %s</span></p>',
            $class,
            Date::parse('d.m.Y H:m', $row['tstamp']),
            $project,
            $row['subject'],
            StringUtil::substr($row['recipientsTo'], 50, ' ...')
        );
    }

    /**
     * load callback for the content field
     * the content is stored base64_encoded in the database
     * @param $strValue
     * @param $objUser
     * @param null $objDCA
     * @return string
     */
    public function base64decode(?string $strValue)
    {
        if (base64_decode((string)$strValue, true))
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
        $strValue = Input::xssClean($strValue, true);
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

        $db = Database::getInstance()
            ->prepare('SELECT pid FROM tl_be_email WHERE id=?')
            ->execute(Input::get('id')
            );

        if ($db->pid != BackendUser::getInstance()->id)
        {
            Controller::redirect($this->getReferer());
        }
    }

    /**
     * onsubmit_callback
     * send email
     */
    public function onSubmitCbSendEmail(DataContainer $dc)
    {
        // the save-button is a fileupload-button
        if (!isset($_POST['save']))
        {
            return;
        }

        $objEmail = new Email();
        $fromMail = BackendUser::getInstance()->email;
        $subject = Input::post('subject');
        $objEmail->replyTo($fromMail);
        $objEmail->from = $fromMail;
        $objEmail->subject = $subject;
        $objEmail->text = base64_decode($_POST['content']);
        $objEmail->html = nl2br(base64_decode($_POST['content']));

        // Save attachment
        $db = Database::getInstance()
            ->prepare('SELECT * FROM tl_be_email WHERE id=?')
            ->execute(Input::get('id'))
        ;

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
                        $objEmail->attachFile(TL_ROOT . '/' . $objFile->path);
                    }
                }
            }
        }

        // Cc
        $cc_recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsCc'), 'recipientsCc'));
        if (count($cc_recipients))
        {
            $objEmail->sendCc($cc_recipients);
        }

        // Bcc
        $bcc_recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsBcc'), 'recipientsBcc'));
        if (count($bcc_recipients))
        {
            $objEmail->sendBcc($bcc_recipients);
        }

        // To
        $recipients = array_unique($this->validateEmailAddresses(Input::post('recipientsTo'), 'recipientsTo'));

        if (count($recipients))
        {
            // Update model
            $beEmailModel = BeEmailModel::findByPk(Input::get('id'));
            if ($beEmailModel !== null)
            {
                $beEmailModel->recipientsTo = implode(',', $recipients);
                $beEmailModel->recipientsCc = implode(',', $cc_recipients);
                $beEmailModel->recipientsBcc = implode(',', $bcc_recipients);
                $beEmailModel->tstamp = time();

                // HOOK: add custom logic
                if (isset($GLOBALS['TL_HOOKS']['beEmailBeforeSend']) && \is_array($GLOBALS['TL_HOOKS']['beEmailBeforeSend']))
                {
                    foreach ($GLOBALS['TL_HOOKS']['beEmailBeforeSend'] as $callback)
                    {
                        // !Important - Parameters $objEmail and $beEmailModel should be passed by reference in the function declaration.
                        static::importStatic($callback[0])->{$callback[1]}($objEmail, $beEmailModel, $dc);
                    }
                }

                $objEmail->sendTo($beEmailModel->recipientsTo);
                $beEmailModel->emailNotSent = '';

                $beEmailModel->save();
            }

            $request = Environment::get('request');
            $redirect = preg_replace('/&act=edit&id=([\d]+)/', '', $request);
            Message::addInfo($GLOBALS['TL_LANG']['tl_be_email']['confirmMessageHasBeenSent']);
            unset($_POST);

            Controller::redirect($redirect);
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
            Database::getInstance()
                ->prepare('UPDATE tl_be_email %s WHERE id=?')
                ->set($set)
                ->execute(Input::get('id'))
            ;

            Input::setPost($field, '');
            return $arrEmailAddresses;
        }

        $arrEmailAddresses = array();
        preg_match_all('/\w[-._\w]*\w@\w[-._\w]*\w\.\w{2,6}/i', $strAddresses, $arrEmailAddresses);

        // remove doubble entries
        $arrEmailAddresses = array_unique($arrEmailAddresses[0]);

        // update the db
        $set = array($field => implode('; ', $arrEmailAddresses));
        Database::getInstance()
            ->prepare('UPDATE tl_be_email %s WHERE id=?')
            ->set($set)
            ->execute(Input::get('id'))
        ;

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
        $db = Database::getInstance()
            ->prepare('SELECT * FROM tl_be_email WHERE id=?')
            ->limit(1)
            ->execute($dc->id)
        ;

        if ($db->emailNotSent)
        {
            // Disable buttons
            unset($arrButtons['saveNclose']);
            unset($arrButtons['saveNcreate']);
            unset($arrButtons['saveNduplicate']);
            $arrButtons['save'] = '<button name="save" id="save" class="tl_submit" accesskey="c">' . $GLOBALS['TL_LANG']['tl_be_email']['send_email'] . '</button>';
        }
        else
        {
            unset($arrButtons['save']);
            unset($arrButtons['saveNcreate']);
            unset($arrButtons['saveNduplicate']);
            unset($arrButtons['saveNback']);
            $arrButtons['saveNclose'] = '<button type="submit" name="saveNclose" id="saveNclose" class="tl_submit" accesskey="c">' . $GLOBALS['TL_LANG']['tl_be_email']['closeEditView'] . '</button>';
            $arrButtons['copyAndResendEmail'] = '<button type="submit" name="copyAndResendEmail" id="copyAndResendEmail" class="tl_submit" accesskey="r">' . $GLOBALS['TL_LANG']['tl_be_email']['copyAndResendEmail'] . '</button>';
        }

        return $arrButtons;
    }

    /**
     * oncreate_callback
     * param $strTable
     * @param $id
     * @param $arrSet
     * @param \Contao\DC_Table $dc
     */
    public function onCreateCallback($strTable, $id, $arrSet, DC_Table $dc)
    {
        $GLOBALS['TL_DCA']['tl_be_email']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_be_email']['palettes']['sentEmail'];

        Database::getInstance()
            ->prepare('UPDATE tl_be_email SET emailNotSent=? WHERE id=?')
            ->execute('1', $id)
        ;
    }

    /**
     * oncopy_callback
     * @param $dc
     */
    public function onCopyCallback($id, DC_Table $dc)
    {
        $GLOBALS['TL_DCA']['tl_be_email']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_be_email']['palettes']['sentEmail'];

        Database::getInstance()
            ->prepare('UPDATE tl_be_email SET emailNotSent=? WHERE id=?')
            ->execute('1', $id)
        ;
    }

    /**
     * @return string
     */
    public function generateSummary(DC_Table $dc, $label)
    {
        $db = Database::getInstance()
            ->prepare('SELECT * FROM tl_be_email WHERE id=?')
            ->limit(1)->execute($dc->id)
        ;
        $objTemplate = new BackendTemplate('be_email_summary');
        $objTemplate->to = $db->recipientsTo;
        $objTemplate->cc = $db->recipientsCc;
        $objTemplate->bcc = $db->recipientsBcc;
        $objTemplate->subject = $db->subject;
        $objTemplate->tstampe = $db->tstamp;
        $objTemplate->text = nl2br($this->base64decode($db->content));

        // Labels
        $objTemplate->labelSubject = $GLOBALS['TL_LANG']['tl_be_email']['subject']['0'];
        $objTemplate->labelText = $GLOBALS['TL_LANG']['tl_be_email']['content']['0'];

        return $objTemplate->parse();
    }
}
