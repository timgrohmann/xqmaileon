<?php

namespace PrestaShop\Module\XQMaileon\Model;

use DateTime;

class AbandonedCart
{

    public int $id_cart;
    public int $id_customer;
    public DateTime $date_added;

    public function __construct($sqlResultArray)
    {
        $this->id_cart = intval($sqlResultArray['id_cart']);
        $this->id_customer = intval($sqlResultArray['id_customer']);
        $this->date_added = DateTime::createFromFormat('Y-d-m H:i:s', $sqlResultArray['date_add']);
    }
}
