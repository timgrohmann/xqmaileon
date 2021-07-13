<?php

namespace PrestaShop\Module\XQMaileon\Model;

use DateTime;

class AbandonedCart
{

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var int
     */
    public $id_customer;

    /**
     * @var DateTime
     */
    public $date_added;

    public function __construct($sqlResultArray)
    {
        $this->id_cart = (int) $sqlResultArray['id_cart'];
        $this->id_customer = (int) $sqlResultArray['id_customer'];
        $this->date_added = DateTime::createFromFormat('Y-d-m H:i:s', $sqlResultArray['date_add']);
    }
}
