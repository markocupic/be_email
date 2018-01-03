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

namespace Markocupic\BeEmail;

use Contao\Config;
use Contao\Database;
use Contao\Controller;
use Contao\BackendTemplate;
use Contao\Input;

/**
 * Class BeEmail
 * @package Markocupic\BeEmail
 */
class BeEmail
{

    /**
     * @param string $strAction
     */
    public function executePreActions($strAction = '')
    {


        // Send email addresses as string to the server
        if ($strAction === 'getEmailAddresses')
        {
            // Output
            $json = array();

            // userBox
            $arrEmail = [];


            $mode = Config::get('address_popup_settings') ?: '';

            if ($mode === 'select_members_and_users' || $mode === 'select_users_only')
            {
                $result = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY email')->execute('');
                while ($result->next())
                {
                    $arrEmail[] = $result->email;
                    if ($result->alternate_email != '')
                    {
                        $arrEmail[] = $result->alternate_email;
                    }

                    if ($result->alternate_email_2 != '')
                    {
                        $arrEmail[] = $result->alternate_email_2;
                    }
                }
            }

            if ($mode === 'select_members_and_users' || $mode === 'select_members_only')
            {
                $result = Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ?')->execute('');
                while ($result->next())
                {
                    $arrEmail[] = $result->email;
                }
            }


            $arrEmail = array_unique($arrEmail);
            $arrEmail = array_filter($arrEmail);

            $json['emailString'] = implode(',', $arrEmail);

            // Send it to the browser
            echo(json_encode($json));
            exit();

        }
        // Send language file to the browser
        if ($strAction === 'loadBeEmailLangFile')
        {
            // Output
            $json = array();

            // Load language file
            Controller::loadLanguageFile('tl_be_email');
            $json['lang'] = $GLOBALS['TL_LANG']['tl_be_email'];

            // Send it to the browser
            echo html_entity_decode(json_encode($json));
            exit();

        }

        // Send address book to the browser
        if ($strAction === 'openBeEmailAddressBook')
        {
            $objTemplate = new BackendTemplate('be_email_address_book');

            // userBox
            $result = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY name')->execute('');
            $userRows = '';
            $i = 0;
            while ($row = $result->fetchAssoc())
            {
                $arrEmailAddresses = array(
                    trim($row['email']),
                    trim($row['alternate_email']),
                    trim($row['alternate_email_2'])
                );

                // Remove double entries and filter empty values
                $arrEmailAddresses = array_filter(array_unique($arrEmailAddresses));
                $formInput = Input::post('formInput');
                $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
                $userRows .= sprintf('<tr class="%s"><td><a href="#" onclick="ContaoBeEmail.sendmail(%s, %s, this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td>%s</td><td>%s</td></tr>', $oddOrEven, "'" . implode('; ', $arrEmailAddresses) . "'", "'" . $formInput . "'", $row['name'], implode('; ', $arrEmailAddresses));
                $i++;
            }
            $objTemplate->userAddresses = $userRows;


            // memberBox
            $result = Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ? ORDER BY lastname')->execute('');
            $memberRows = '';
            $i = 0;
            while ($result->next())
            {
                $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
                $memberRows .= sprintf('<tr class="col_0 %s"><td><a href="#" onclick="ContaoBeEmail.sendmail(%s, %s, this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td class="col_1">%s</td><td class="col_2">%s</td></tr>', $oddOrEven, "'" . $result->email . "'", "'" . $formInput . "'", $result->firstname . " " . $result->lastname, $result->email);
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

            // Output
            $json = array();

            // Load language file
            Controller::loadLanguageFile('tl_be_email');
            $json['lang'] = $GLOBALS['TL_LANG']['tl_be_email'];

            // Parse template
            $json['content'] = $objTemplate->parse();

            // Send it to the browser
            echo json_encode($json);
            exit();
        }

    }
}