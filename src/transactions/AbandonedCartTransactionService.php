<?php

namespace PrestaShop\Module\XQMaileon\Transactions;

use PrestaShop\Module\XQMaileon\Model\AbandonedCart;
use PrestaShop\Module\XQMaileon\Model\Transaction\AbandonedCartTransaction;

class AbandonedCartTransactionService extends AbstractTransactionService
{

    const ABANDONED_CART_NOTIFIED_TABLE_NAME = 'xqm_abandoned_cart_notified';

    public function notify()
    {
        $carts = $this->findAbandonedCarts();
        foreach ($carts as $cart) {
            return $this->notifyOneCart($cart);
        }
        #$not_not
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
    public function findAbandonedCarts()
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
            AND DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= r.date_add
        ';

        $db = \Db::getInstance();
        $carts = $db->query($sql);
        $all_carts = array();
        while ($fetch = $db->nextRow($carts)) {
            $all_carts[] = new AbandonedCart($fetch);
        }
        return $all_carts;
    }

    public function notifyOneCart(AbandonedCart $abandonedCart)
    {
        $customerForCart = new \Customer($abandonedCart->id_customer);

        # do not process cart if customer does not exist (anymore)
        if (empty($customerForCart->email)) return;

        # only send abandoned cart notification if customer has opted in to emails
        # TODO: Make configurable
        if ($customerForCart->optin || true) {
            $cart = new \Cart($abandonedCart->id_cart);
            $products = $cart->getProducts();
            $trans = new AbandonedCartTransaction($this->transactionService, $customerForCart);
            $trans->send(
                [
                    'order.id' => 'order.id',
                    'order.date' => time(),
                    'order.status' => 'order.status',
                    'order.items' => array('1', '2'),
                    'order.total' => 130.99,
                    'order.total_tax' => 20.23,
                    'order.total_fees' => 0.6,
                    'order.currency' => '€'
                ]
            );
            return $products;
        }
    }
}
