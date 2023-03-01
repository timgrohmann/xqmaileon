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

namespace PrestaShop\Module\XQMaileon\Model\Transaction;

use de\xqueue\maileon\api\client\transactions\DataType;

class OrderConfirmationTransaction extends AbstractTransaction
{
    public function getTypeDescription()
    {
        return [
            'order.id!' => DataType::$STRING,
            'order.date!' => DataType::$TIMESTAMP,
            'order.status!' => DataType::$STRING,
            'order.estimated_delivery_date' => DataType::$DATE,
            'order.items!' => DataType::$JSON,
            'order.product_ids' => DataType::$STRING,
            'order.categories' => DataType::$STRING,
            'order.brands' => DataType::$STRING,
            'order.total!' => DataType::$DOUBLE,
            'order.total_no_shipping' => DataType::$DOUBLE,
            'order.total_tax!' => DataType::$DOUBLE,
            'order.total_fees!' => DataType::$DOUBLE,
            'order.total_refunds' => DataType::$DOUBLE,
            'order.fees' => DataType::$JSON,
            'order.currency!' => DataType::$STRING,
            'payment.method.id' => DataType::$STRING,
            'payment.method.name' => DataType::$STRING,
            'payment.method.url' => DataType::$STRING,
            'payment.method.image_url' => DataType::$STRING,
            'payment.due_date' => DataType::$DATE,
            'payment.status' => DataType::$STRING,
            'discount.code' => DataType::$STRING,
            'discount.total' => DataType::$DOUBLE,
            'discount.rules' => DataType::$JSON,
            'discount.rules_string' => DataType::$STRING,
            'customer.salutation' => DataType::$STRING,
            'customer.fullname' => DataType::$STRING,
            'customer.firstname' => DataType::$STRING,
            'customer.lastname' => DataType::$STRING,
            'customer.id' => DataType::$STRING,
            'billing.address.salutation' => DataType::$STRING,
            'billing.address.firstname' => DataType::$STRING,
            'billing.address.lastname' => DataType::$STRING,
            'billing.address.street' => DataType::$STRING,
            'billing.address.zip' => DataType::$STRING,
            'billing.address.city' => DataType::$STRING,
            'billing.address.region' => DataType::$STRING,
            'billing.address.country' => DataType::$STRING,
            'shipping.address.salutation' => DataType::$STRING,
            'shipping.address.firstname' => DataType::$STRING,
            'shipping.address.lastname' => DataType::$STRING,
            'shipping.address.street' => DataType::$STRING,
            'shipping.address.zip' => DataType::$STRING,
            'shipping.address.city' => DataType::$STRING,
            'shipping.address.region' => DataType::$STRING,
            'shipping.address.country' => DataType::$STRING,
            'shipping.service.id' => DataType::$STRING,
            'shipping.service.name' => DataType::$STRING,
            'shipping.service.url' => DataType::$STRING,
            'shipping.service.image_url' => DataType::$STRING,
            'shipping.service.tracking_code' => DataType::$STRING,
            'shipping.service.tracking_url' => DataType::$STRING,
            'shipping.status' => DataType::$STRING,
        ];
    }
}
