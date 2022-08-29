<?php

declare(strict_types=1);

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

namespace Markocupic\BeEmail\EventListener\ContaoHooks;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\System;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("executePreActions")
 */
class ExecutePreActions
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContaoFramework $framework, Connection $connection, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->connection = $connection;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $strAction
     */
    public function __invoke($strAction = ''): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $blnShowUserAddresses = false;
        $blnShowMemberAddresses = false;

        $configAdapter = $this->framework->getAdapter(Config::class);

        switch ($configAdapter->get('address_popup_settings')) {
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
            $arrItems = [];

            $strPattern = strtolower(trim($request->request->get('pattern', '')));

            if (\strlen($strPattern) && $blnShowUserAddresses) {
                $stmt = $this->connection->prepare('SELECT * FROM tl_user t WHERE t.email LIKE :pattern OR t.name LIKE :pattern ORDER BY t.name LIMIT 0,10');
                $stmt->bindValue(':pattern', '%'.$strPattern.'%', \PDO::PARAM_STR);
                $stmt->execute();

                while (false !== ($result = $stmt->fetch(\PDO::FETCH_OBJ))) {
                    $arrItems[$result->email] = [
                        'label' => $result->name,
                        'value' => strtolower((string) $result->email),
                    ];
                }
            }

            if (\strlen($strPattern) && $blnShowMemberAddresses) {
                $stmt = $this->connection->prepare("SELECT * FROM tl_member t WHERE t.email LIKE :pattern OR CONCAT(t.firstname, ' ', t.lastname) LIKE :pattern ORDER BY t.lastname, t.firstname LIMIT 0,10");
                $stmt->bindValue(':pattern', '%'.$strPattern.'%', \PDO::PARAM_STR);
                $stmt->execute();

                while (false !== ($result = $stmt->fetch(\PDO::FETCH_OBJ))) {
                    $arrItems[$result->email] = [
                        'label' => trim($result->firstname.' '.$result->lastname),
                        'value' => strtolower((string) $result->email),
                    ];
                }
            }

            // Remove associative keys and limit items
            $arrEmail = [];
            $i = 0;

            $systemAdapter = $this->framework->getAdapter(System::class);
            $limit = $systemAdapter->getContainer()
                ->getParameter('markocupic_be_email.suggestions_list_max_length')
            ;

            foreach ($arrItems as $arrItem) {
                if ($i === $limit) {
                    break;
                }
                $arrEmail[] = $arrItem;
                ++$i;
            }

            // Send data to the browser
            echo json_encode(['data' => $arrEmail]);
            exit();
        }
    }
}
