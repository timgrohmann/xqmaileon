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

use PrestaShop\Module\XQMaileon\Mapper\AddressMapper;
use PrestaShop\Module\XQMaileon\Mapper\OptInPermissionMapper;
use PrestaShop\Module\XQMaileon\Mapper\ProductItemMapper;
use PrestaShop\Module\XQMaileon\Mapper\ValidDateMapper;
use PrestaShop\Module\XQMaileon\Model\Transaction\OrderConfirmationTransaction;

class OrderConfirmationTransactionService extends AbstractTransactionService
{

    public function sendConfirmation(\Order $order): bool
    {
        $customer = new \Customer($order->id_customer);

        $invoiceAdress = new \Address($order->id_address_invoice);
        $deliveryAddress = new \Address($order->id_address_delivery);

        error_log(json_encode($order));

        $content = [
            'order' => [
                'id' => $order->reference,
                'date' => ValidDateMapper::validDateOrNull($order->date_add),
                'status' => 'created',
                'estimated_delivery_date' => ValidDateMapper::validDateOrNull($order->delivery_date),
                'items' => ProductItemMapper::mapArray($order->getCartProducts()),
                'total' => (float) $order->total_paid,
                'total_no_shipping' => $order->total_paid - $order->total_shipping,
                'total_tax' => $order->total_paid - $order->total_paid_tax_excl,
                'total_fees' => $order->total_shipping + $order->total_wrapping,
                'fees' => [
                    'shipping' => $order->total_shipping,
                    'wrapping' => $order->total_wrapping
                ],
                'currency' => (new \Currency($order->id_currency))->symbol,
            ],
            'payment' => [
                'method' => [
                    'id' => $order->module,
                    'name' => $order->payment
                ],
                'due_date' => ValidDateMapper::validDateOrNull($order->invoice_date)
            ],
            'billing.address' => AddressMapper::mapToArray($invoiceAdress),
            'shipping.address' => AddressMapper::mapToArray($deliveryAddress),
            'customer' => [
                'fullname' => $customer->firstname . ' ' . $customer->lastname,
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'id' => (string) $customer->id,
            ],
        ];

        $shippings = $order->getShipping();
        if (count($shippings) > 0) {
            $shipping = $shippings[0];
            $content['shipping.service'] = [
                'id' => $shipping['id_carrier'],
                'name' => $shipping['carrier_name'],
                'url' => $shipping['url'],
                'tracking_code' => $shipping['tracking_number']
            ];
            if (file_exists(_PS_SHIP_IMG_DIR_ . $shipping['id_carrier'] . '.jpg')) {
                $content['shipping.service']['image_url'] =
                    \Tools::getMediaServer(_THEME_SHIP_DIR_ . $shipping['id_carrier'] . '.jpg')
                    . _THEME_SHIP_DIR_ . $shipping['id_carrier'] . '.jpg';
            }
        }



        $transaction = new OrderConfirmationTransaction($this->transactionService, $this->contactService, $customer, (new OptInPermissionMapper())->getOrderConfNewPermission());
        return $transaction->send($content);
    }
}
