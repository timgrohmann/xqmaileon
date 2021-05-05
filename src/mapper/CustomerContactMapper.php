<?php

namespace PrestaShop\Module\XQMaileon\Mapper;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;

use PrestaShop\PrestaShop\Adapter\Entity\Context;

class CustomerContactMapper {

    /**
     * @return Contact
     */
    public static function map(\Customer $customer)
    {
        $contact = new Contact(
            null,
            $customer->email,
            CustomerContactMapper::mapPermission(!empty($customer->optin)),
            $customer->id,
            false,
            array(
                StandardContactField::$FIRSTNAME => $customer->firstname,
                StandardContactField::$LASTNAME => $customer->lastname,
                StandardContactField::$FULLNAME => $customer->firstname . ' ' . $customer->lastname,
                # TODO: how to represent gender in Maileon
                StandardContactField::$GENDER => $customer->id_gender,
                StandardContactField::$ORGANIZATION => $customer->company
            )
        );

        # handle address

        $addresses = $customer->getAddresses(Context::getContext()->language->id);

        if (count($addresses) >= 1) {
            $address = $addresses[0];
            $contact->standard_fields[StandardContactField::$ADDRESS] = $address['address1'];
            if(!empty($address['address2'])) {
                $contact->standard_fields[StandardContactField::$ADDRESS] .= ' ' . $address['address2'];
            }
            $contact->standard_fields[StandardContactField::$CITY] = $address['city'];
            $contact->standard_fields[StandardContactField::$ZIP] = $address['postcode'];
        }

        return $contact;
    }

    /**
     * @return Permission
     */
    public static function mapPermission(bool $optin) {
        $goalPermission = (new OptInPermissionMapper())->getCurrentPermission();
        return $optin ? $goalPermission : Permission::$NONE;
    }
}