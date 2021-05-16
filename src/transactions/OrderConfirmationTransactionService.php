<?php

namespace PrestaShop\Module\XQMaileon\Transactions;

use PrestaShop\Module\XQMaileon\Mapper\AddressMapper;
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
                'date' => $order->date_add,
                'status' => 'created',
                'estimated_delivery_date' => $order->delivery_date,
                'items' => $order->getCartProducts(),
                'total' => floatval($order->total_paid),
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
                'due_date' => $order->invoice_date
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



        $transaction = new OrderConfirmationTransaction($this->transactionService, $customer);
        return $transaction->send($content);
    }
}
