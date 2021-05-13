<?php

namespace PrestaShop\Module\XQMaileon\Model\Transaction;

use de\xqueue\maileon\api\client\transactions\DataType;

class AbandonedCartTransaction extends AbstractTransaction
{

    public function getTypeDescription()
    {
        return [
            'cart.id' => DataType::$STRING,
            'cart.date' => DataType::$TIMESTAMP,
            'cart.items' => DataType::$JSON,
            'cart.product_ids' => DataType::$STRING,
            'cart.categories' => DataType::$STRING,
            'cart.brands' => DataType::$STRING,
            'cart.total' => DataType::$FLOAT,
            'cart.total_no_shipping' => DataType::$FLOAT,
            'cart.total_tax' => DataType::$FLOAT,
            'cart.total_fees' => DataType::$FLOAT,
            'cart.total_refunds' => DataType::$FLOAT,
            'cart.fees' => DataType::$JSON,
            'cart.refunds' => DataType::$JSON,
            'cart.currency' => DataType::$STRING,
            'discount.code' => DataType::$STRING,
            'discount.total' => DataType::$STRING,
            'discount.rules' => DataType::$JSON,
            'discount.rules_string' => DataType::$STRING,
            'customer.salutation' => DataType::$STRING,
            'customer.fullname' => DataType::$STRING,
            'customer.firstname' => DataType::$STRING,
            'customer.lastname' => DataType::$STRING,
            'customer.id' => DataType::$STRING,
            'generic.string_1' => DataType::$STRING,
            'generic.string_2' => DataType::$STRING,
            'generic.string_3' => DataType::$STRING,
            'generic.string_4' => DataType::$STRING,
            'generic.string_5' => DataType::$STRING,
            'generic.string_6' => DataType::$STRING,
            'generic.string_7' => DataType::$STRING,
            'generic.string_8' => DataType::$STRING,
            'generic.string_9' => DataType::$STRING,
            'generic.string_10' => DataType::$STRING,
            'generic.double_1' => DataType::$DOUBLE,
            'generic.double_2' => DataType::$DOUBLE,
            'generic.double_3' => DataType::$DOUBLE,
            'generic.double_4' => DataType::$DOUBLE,
            'generic.double_5' => DataType::$DOUBLE,
            'generic.integer_1' => DataType::$INTEGER,
            'generic.integer_2' => DataType::$INTEGER,
            'generic.integer_3' => DataType::$INTEGER,
            'generic.integer_4' => DataType::$INTEGER,
            'generic.integer_5' => DataType::$INTEGER,
            'generic.boolean_1' => DataType::$BOOLEAN,
            'generic.boolean_2' => DataType::$BOOLEAN,
            'generic.boolean_3' => DataType::$BOOLEAN,
            'generic.boolean_4' => DataType::$BOOLEAN,
            'generic.boolean_5' => DataType::$BOOLEAN,
            'generic.date_1' => DataType::$DATE,
            'generic.date_2' => DataType::$DATE,
            'generic.date_3' => DataType::$DATE,
            'generic.timestamp_1' => DataType::$TIMESTAMP,
            'generic.timestamp_2' => DataType::$TIMESTAMP,
            'generic.timestamp_3' => DataType::$TIMESTAMP,
            'generic.json_1' => DataType::$JSON,
            'generic.json_2' => DataType::$JSON,
            'generic.json_3' => DataType::$JSON
        ];
    }
}
