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
            $objTemplate = new \BackendTemplate('be_email_address_book');

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
                $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
                $userRows .= sprintf('<tr class="%s"><td><a href="#" onclick="ContaoBeEmail.sendmail(%s, %s, this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td>%s</td><td>%s</td></tr>', $oddOrEven, "'" . implode('; ', $arrEmailAddresses) . "'", "'" . $formInput . "'", $row['name'], $row['email']);
                $i++;
            }
            $objTemplate->userAddresses = $userRows;


            // memberBox
            $result = \Database::getInstance()->prepare('SELECT * FROM tl_member WHERE email != ? ORDER BY lastname')->execute('');
            $memberRows = '';
            $i = 0;
            while ($result->next())
            {
                $oddOrEven = $i % 2 == 0 ? 'odd' : 'even';
                $memberRows .= sprintf('<tr class="%s"><td><a href="#" onclick="ContaoBeEmail.sendmail(%s, %s, this); return false"><img src="../system/modules/be_email/assets/email.svg" class="select-address-icon"></a></td><td>%s</td><td>%s</td></tr>', $oddOrEven, "'" . $result->email . "'", "'" . $formInput . "'", $result->firstname . " " . $result->lastname, $result->email);
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