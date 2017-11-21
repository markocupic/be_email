<?php
/**
 * Created by PhpStorm.
 * User: Marko
 * Date: 20.11.2017
 * Time: 21:59
 */

namespace Markocupic\BeEmail;


class BeEmail
{


    public function executePreActions($strAction = '')
    {
        if ($strAction === 'openBeEmailAddressBook')
        {
            $objTemplate = new \BackendTemplate('be_email_popup');

            // userBox
            $result = \Database::getInstance()->prepare('SELECT * FROM tl_user WHERE email != ? ORDER BY name')->execute('');
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
                $formInput = \Input::post('formInput');
                $userRows .= "<tr class=\"" . ($i % 2 == 0 ? 'odd' : 'even') . "\"><td><a href=\"#\" onclick=\"ContaoBeEmail.removeElement(this); ContaoBeEmail.sendmail('" . implode('; ', $arrEmailAddresses) . "', '" . $formInput . "'); return false;\"><img src=\"../system/modules/be_email/assets/email.svg\" class=\"select-address-icon\"></a></td><td>" . $row['name'] . "</td><td>" . $row['email'] . "</td></tr>\r\n";
                $i++;
            }
            $objTemplate->userAddresses = $userRows;


            // memberBox
            $result = \Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ? ORDER BY lastname')->execute('');
            $memberRows = '';
            $i = 0;
            while ($result->next())
            {
                $memberRows .= "<tr class=\"" . ($i % 2 == 0 ? 'odd' : 'even') . "\"><td class=\"col_0\"><a href=\"#\" onclick=\"ContaoBeEmail.removeElement(this); ContaoBeEmail.sendmail('" . $result->email . "'); return false;\"><img src=\"../system/modules/be_email/assets/email.svg\" class=\"select-address-icon\"></a></td><td class=\"col_1\">" . $result->firstname . ' ' . $result->lastname . "</td><td class=\"col_2\">" . $result->email . "</td></tr>\r\n";
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
            echo json_encode(array('content' => $objTemplate->parse()));
            exit();
        }

    }
}