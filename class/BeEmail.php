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
        if ($strAction === 'loadEmailAddresses')
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

        // Send address book to the browser
        if ($strAction === 'loadData')
        {
            // Load language file
            Controller::loadLanguageFile('tl_be_email');

            // Get template object
            $objTemplate = new BackendTemplate('be_email_address_book');

            // userBox
            $arrRows = $this->getUserRows();
            $objTemplate->userAddresses = implode('', $arrRows);
            $objTemplate->countUserAddresses = count($arrRows);

            // memberBox
            $arrRows = $this->getMemberRows();
            $objTemplate->memberAddresses = implode('', $arrRows);
            $objTemplate->countMemberAddresses = count($arrRows);
            $objTemplate->lblEntriesFound = $GLOBALS['TL_LANG']['tl_be_email']['entriesFound'];

            // Placeholders
            $objTemplate->lbl_searchForName = $GLOBALS['TL_LANG']['tl_be_email']['searchForName'];
            $objTemplate->lbl_users = $GLOBALS['TL_LANG']['tl_be_email']['users']['0'];
            $objTemplate->lbl_members = $GLOBALS['TL_LANG']['tl_be_email']['members']['0'];

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

            $json['lang'] = $GLOBALS['TL_LANG']['tl_be_email'];

            // Parse template
            $json['content'] = $objTemplate->parse();

            // Send it to the browser
            echo json_encode($json);
            exit();
        }
    }

    /**
     * @return array
     */
    protected function getMemberRows()
    {
        $result = Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ? ORDER BY lastname')->execute('');
        $i = 0;
        $arrRows = array();
        while ($result->next())
        {
            $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
            $strName = $result->firstname . " " . $result->lastname;
            $arrRows[] = sprintf('<tr class="col_0 %s" data-name="%s" data-email=""><td><a href="#" onclick="ContaoBeEmail.sendmail(\'%s\', this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td class="col_1">%s</td><td class="col_2">%s</td></tr>', $oddOrEven, $strName, $result->email, $strName, $result->email);
            $i++;
        }
        return $arrRows;
    }

    /**
     * @return array
     */
    protected function getUserRows()
    {
        $result = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY name')->execute('');
        $i = 0;
        $arrRows = array();
        while ($row = $result->fetchAssoc())
        {
            $arrEmailAddresses = array(
                trim($row['email']),
                trim($row['alternate_email']),
                trim($row['alternate_email_2'])
            );

            // Remove double entries and filter empty values
            $arrEmailAddresses = array_filter(array_unique($arrEmailAddresses));
            $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
            $strName = $row['name'];
            $arrRows[] = sprintf('<tr class="%s" data-name="%s" data-email=""><td><a href="#" onclick="ContaoBeEmail.sendmail(\'%s\', this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td>%s</td><td>%s</td></tr>', $oddOrEven, $strName, implode('; ', $arrEmailAddresses), $strName, implode('; ', $arrEmailAddresses));
            $i++;
        }
        return $arrRows;
    }
}
