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

namespace Markocupic\BeEmail\EventListener\ContaoHooks;

use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\Input;

/**
 * @Hook("executePreActions")
 */
class ExecutePreActions
{
    /**
     * @param string $strAction
     */
    public function __invoke($strAction = ''): void
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

            $pattern = strtolower(trim((string) Input::post('pattern')));

            if (\strlen($pattern) > 1 && $blnShowUserAddresses) {
                $result = Database::getInstance()
                    ->query("SELECT * FROM tl_user WHERE email != '' AND email NOT LIKE '".$pattern."' AND name LIKE '%".$pattern."%' OR email LIKE '%".$pattern."%' ORDER BY name LIMIT 0,10")
                ;

                while ($result->next()) {
                    if (strtolower($result->email) === $pattern) {
                        continue;
                    }
                    $arrEmail[$result->email] = [
                        'label' => $result->name,
                        'value' => strtolower((string) $result->email),
                    ];
                }
            }

            if ($blnShowMemberAddresses) {
                $result = Database::getInstance()
                    ->query("SELECT * FROM tl_member WHERE email != '' AND email NOT LIKE '".$pattern."' AND CONCAT(firstname, ' ', lastname) LIKE '%".$pattern."%' OR email LIKE '%".$pattern."%' ORDER BY lastname LIMIT 0,10")
                ;

                while ($result->next()) {
                    if (strtolower($result->email) === $pattern) {
                        continue;
                    }
                    $arrEmail[$result->email] = [
                        'label' => trim($result->firstname.' '.$result->lastname),
                        'value' => strtolower((string) $result->email),
                    ];
                }
            }
            $arrMail = [];

            foreach ($arrEmail as $arrItem) {
                $arrMail[] = $arrItem;
            }

            $json = ['data' => $arrMail];

            // Send to the browser
            echo json_encode($json);
            exit();
        }
    }
}
