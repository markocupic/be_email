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
use Contao\Input;

/**
 * Class BeEmail.
 */
class BeEmail
{
    /**
     * Ajax.
     *
     * @param string $strAction
     */
    public function executePreActions($strAction = '')
    {
        $blnShowUserAddresses = false;
        $blnShowMemberAddresses = false;

        switch (Config::get('address_popup_settings')) {
            case 'select_users_only':
                $blnShowUserAddresses = true;
                break;

            case 'select_members_only':
                $blnShowMemberAddresses = true;
                break;

            default:
                $blnShowUserAddresses = true;
                $blnShowMemberAddresses = true;
        }

        // Send email-addresses to the server
        if ('loadEmailList' === $strAction) {
            $arrEmail = [];

            $pattern = Input::post('pattern');

            if (\strlen($pattern) > 1 && $blnShowUserAddresses) {
                $result = Database::getInstance()
                    ->query("SELECT * FROM tl_user WHERE email != '' AND CONCAT(email, ' ', name) LIKE '%".$pattern."%' ORDER BY name LIMIT 0,10")
                ;

                while ($result->next()) {
                    $arrEmail[$result->email] = [
                        'label' => $result->name,
                        'email' => $result->email,
                    ];
                }
            }

            if ($blnShowMemberAddresses) {
                $result = Database::getInstance()
                    ->query("SELECT * FROM tl_member WHERE email != '' AND CONCAT(email, ' ', firstname, ' ', lastname) LIKE '%".$pattern."%' ORDER BY lastname LIMIT 0,10")
                ;

                while ($result->next()) {
                    $arrEmail[$result->email] = [
                        'label' => trim($result->firstname.' '.$result->lastname),
                        'email' => $result->email,
                    ];
                }
            }
            $arrMail = [];

            foreach ($arrEmail as $arrItem) {
                $arrMail[] = $arrItem;
            }

            $json = ['emailList' => $arrMail];

            // Send to the browser
            echo json_encode($json);
            exit();
        }
    }
}