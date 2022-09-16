<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2022 XQueue GmbH
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *  @author    XQueue GmbH
 *  @copyright 2013-2022 XQueue
 *  @license   MIT
 */

namespace PrestaShop\Module\XQMaileon\Transactions;

use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Mapper\OptInPermissionMapper;
use PrestaShop\Module\XQMaileon\Model\AbandonedCart;
use PrestaShop\Module\XQMaileon\Model\Transaction\AbandonedCartTransaction;

class AbandonedCartTransactionService extends AbstractTransactionService implements CronTransactionServiceInterface
{

    const ABANDONED_CART_NOTIFIED_TABLE_NAME = 'xqm_abandoned_cart_notified';

    public function trigger(): array
    {
        $timer = \Configuration::get(ConfigOptions::XQMAILEON_ABANDONED_TIME);
        if (empty($timer) || empty((int) $timer)) {
            return ['error' => 'Timer not set', 'timer' => $timer];
        }
        $carts = $this->findAbandonedCarts((int) $timer);
        $successCount = 0;
        $failCount = 0;
        foreach ($carts as $cart) {
            $res = $this->notifyOneCart($cart);
            if ($res) {
                $successCount++;
                $this->setCartNotified($cart);
            } else {
                $failCount++;
            }
        }
        return [
            'succeeded' => $successCount,
            'failed' => $failCount,
            'timer' => $timer
        ];
    }

    public static function installDatabase(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::ABANDONED_CART_NOTIFIED_TABLE_NAME . ' (
            id_notification int(11) NOT NULL AUTO_INCREMENT,
            id_cart int(11) NOT NULL,
            date_notified DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_notification)
        ) DEFAULT CHARSET=utf8';
        return \Db::getInstance()->execute($sql);
    }

    public static function uninstallDatabase(): bool
    {
        $sql = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::ABANDONED_CART_NOTIFIED_TABLE_NAME;
        return \Db::getInstance()->execute($sql);
    }

    /**
     * @return AbandonedCart[]
     */
    private function findAbandonedCarts(int $timer)
    {
        # selects newest cart the customer created but not ordered
        # also does not select cart if customer ordered another card afterwars
        # does not select carts where a notification has already been sent and logged to ABANDONED_CART_NOTIFIED_TABLE_NAME table
        $sql = '
        WITH ranked_carts AS (
            SELECT c.*, ROW_NUMBER() OVER (PARTITION BY id_customer ORDER BY date_add DESC) AS rn
            FROM ' . _DB_PREFIX_ . 'cart c
        )
        SELECT r.id_cart,
            r.id_customer,
            r.date_add
            FROM ranked_carts r
            LEFT JOIN ' . _DB_PREFIX_ . 'orders o
            ON (o.id_cart = r.id_cart)
            LEFT JOIN ' . _DB_PREFIX_ . self::ABANDONED_CART_NOTIFIED_TABLE_NAME . ' n
            ON (n.id_cart = r.id_cart)
            WHERE r.rn = 1
            AND o.id_order IS NULL
            AND n.id_notification IS NULL
            AND DATE_SUB(NOW(), INTERVAL 2 DAY) <= r.date_add
            AND DATE_SUB(NOW(), INTERVAL ' . ((string) $timer) . ' MINUTE) > r.date_add
        ';

        $db = \Db::getInstance();
        $carts = $db->query($sql);
        $all_carts = array();
        while ($fetch = $db->nextRow($carts)) {
            $all_carts[] = new AbandonedCart($fetch);
        }
        return $all_carts;
    }

    /**
     * @return bool Returns true if the abandoned cart notification has been sent successfully
     */
    private function notifyOneCart(AbandonedCart $abandonedCart): bool
    {
        $customerForCart = new \Customer($abandonedCart->id_customer);

        # do not process cart if customer does not exist (anymore)
        if (empty($customerForCart->email)) {
            return false;
        }

        # only send abandoned cart notification if customer has opted in to emails or
        # double opt in was not configured in module settings

        if ($customerForCart->optin || !(new OptInPermissionMapper())->getCurrentHasDoubleOptIn()) {
            $cart = new \Cart($abandonedCart->id_cart);
            $cartSummary = $cart->getRawSummaryDetails(\Context::getContext()->language->id);
            $trans = new AbandonedCartTransaction($this->transactionService, $this->contactService, $customerForCart, (new OptInPermissionMapper())->getAbandonedCartNewPermission());
            return $trans->send(
                [
                    'cart' => [
                        'id' => (string) $cart->id,
                        'date' => $cart->date_add,
                        'items' => $cartSummary['products'],
                        'total' => $cartSummary['total_price'],
                        'total_no_shipping' => $cartSummary['total_price'] - $cartSummary['total_shipping'],
                        'total_tax' => $cartSummary['total_tax'],
                        'total_fees' => $cartSummary['total_wrapping'] + $cartSummary['total_shipping'],
                        'fees' => [
                            'shipping' => $cartSummary['total_shipping'],
                            'wrapping' => $cartSummary['total_wrapping']
                        ],
                        'currency' => (new \Currency($cart->id_currency))->symbol,
                    ],
                    'discount' => [
                        'total' => (string) $cartSummary['total_discounts'],
                        'rules' => $cartSummary['discounts']
                    ],
                    'customer' => [
                        'fullname' => $customerForCart->firstname . ' ' . $customerForCart->lastname,
                        'firstname' => $customerForCart->firstname,
                        'lastname' => $customerForCart->lastname,
                        'id' => (string) $customerForCart->id,
                    ]
                ]
            );
        } else {
            trigger_error('Cannot send transaction to customer without opt-in.');
        }
        return false;
    }

    private function setCartNotified(AbandonedCart $abandonedCart)
    {
        \Db::getInstance()->insert(self::ABANDONED_CART_NOTIFIED_TABLE_NAME, [
            'id_cart' => $abandonedCart->id_cart
        ]);
    }
}
