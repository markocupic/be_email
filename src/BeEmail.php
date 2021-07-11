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

namespace Markocupic\BeEmail;

use Contao\Config;
use Contao\Database;
use Contao\Controller;
use Contao\BackendTemplate;

/**
 * Class BeEmail
 * @package Markocupic\BeEmail
 */
class BeEmail
{

    /**
     * Ajax
     * @param string $strAction
     */
    public function executePreActions($strAction = '')
    {
        $blnShowUserAddresses = false;
        $blnShowMemberAddresses = false;
        switch (Config::get('address_popup_settings'))
        {
            case 'select_users_only' :
                $blnShowUserAddresses = true;
                break;
            case 'select_members_only' :
                $blnShowMemberAddresses = true;
                break;
            default:
                $blnShowUserAddresses = true;
                $blnShowMemberAddresses = true;
        }

        // Send email addresses as string to the server
        // Used for awesomeplete
        if ($strAction === 'loadEmailAddresses')
        {
            $json = array();

            $arrEmail = [];

            if ($blnShowUserAddresses)
            {
                $result = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY email')->execute('');
                while ($result->next())
                {
                    $arrEmail[] = sprintf('%s <%s>', $result->name,$result->email);
                    if ($result->alternate_email != '')
                    {
                        $arrEmail[] = sprintf('%s <%s>', $result->name,$result->alternate_email);
                    }

                    if ($result->alternate_email_2 != '')
                    {
                        $arrEmail[] = sprintf('%s <%s>', $result->name,$result->alternate_email_2);
                    }
                }
            }

            if ($blnShowMemberAddresses)
            {
                $result = Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ?')->execute('');
                while ($result->next())
                {
                    $arrName = array_filter([$result->firstname, $result->lastname]);
                    $arrEmail[] = sprintf('%s <%s>', implode(' ', $arrName), $result->name,$result->email);
                }
            }

            $arrEmail =  array_filter(array_unique($arrEmail));

            $json['emailString'] = implode(',', $arrEmail);

            // Send to the browser
            echo json_encode($json);
            exit();
        }

        // Send address book to the browser
        if ($strAction === 'loadData')
        {
            // Load language file
            Controller::loadLanguageFile('tl_be_email');

            // Get template object
            $objTemplate = new BackendTemplate('be_email_address_book');

            $objTemplate->showUserAddresses = $blnShowUserAddresses;
            $objTemplate->showMemberAddresses = $blnShowMemberAddresses;

            if ($blnShowUserAddresses)
            {
                // userBox
                $arrRows = $this->getUserRows('tl_user');
                $objTemplate->userAddresses = implode('', $arrRows);
                $objTemplate->countUserAddresses = count($arrRows);
            }

            if ($blnShowMemberAddresses)
            {
                // memberBox
                $arrRows = $this->getUserRows('tl_member');
                $objTemplate->memberAddresses = implode('', $arrRows);
                $objTemplate->countMemberAddresses = count($arrRows);
            }

            // Labels
            $objTemplate->lbl_searchForName = $GLOBALS['TL_LANG']['tl_be_email']['searchForName'];
            $objTemplate->lbl_users = $GLOBALS['TL_LANG']['tl_be_email']['users']['0'];
            $objTemplate->lbl_members = $GLOBALS['TL_LANG']['tl_be_email']['members']['0'];
            $objTemplate->lbl_entriesFound = $GLOBALS['TL_LANG']['tl_be_email']['entriesFound'];

            $json = array();
            $json['lang'] = $GLOBALS['TL_LANG']['tl_be_email'];

            // Parse template
            $json['content'] = $objTemplate->parse();

            // Send to the browser
            echo json_encode($json);
            exit();
        }
    }

    /**
     * @param $strTable (can be tl_user or tl_member)
     * @return array
     */
    protected function getUserRows($strTable)
    {
        if ($strTable === 'tl_user')
        {
            $result = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY name')->execute('');
        }
        else
        {
            $result = Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ? ORDER BY lastname')->execute('');
        }

        $i = 0;
        $arrRows = array();

        while ($row = $result->fetchAssoc())
        {
            $objPartial = new BackendTemplate('be_email_address_book_partial');
            $objPartial->setData($row);

            // Row class
            $objPartial->dataRowClass = $i % 2 == 0 ? 'odd' : 'even';

            // Email
            if ($strTable === 'tl_user')
            {
                $arrEmailAddresses = array(
                    trim($row['email']),
                    trim($row['alternate_email']),
                    trim($row['alternate_email_2'])
                );
                // Remove double entries and filter empty values
                $arrEmailAddresses = array_filter(array_unique($arrEmailAddresses));
                $objPartial->dataEmail = implode('; ', $arrEmailAddresses);
            }
            else
            {
                $objPartial->dataEmail = $row['email'];
            }

            // Fullname
            if ($strTable === 'tl_user')
            {
                $objPartial->dataFullname = $row['name'];
            }
            else
            {
                $objPartial->dataFullname = trim($row['firstname'] . ' ' . $row['lastname']);
            }

            $arrRows[] = $objPartial->parse();

            $i++;
        }
        return $arrRows;
    }
}
