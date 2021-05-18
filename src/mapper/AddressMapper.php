<?php

namespace PrestaShop\Module\XQMaileon\Mapper;

class AddressMapper
{

    public static function mapToArray(\Address $address)
    {
        return [
            'firstname' => $address->firstname,
            'lastname' => $address->lastname,
            'street' => $address->address1 . ($address->address2 ? ' ' . $address->address2 : ''),
            'zip' => $address->postcode,
            'city' => $address->city,
            'country' => $address->country,
        ];
    }
}
