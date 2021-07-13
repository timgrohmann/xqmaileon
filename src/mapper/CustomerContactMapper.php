<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2021 XQueue GmbH
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
 */

namespace PrestaShop\Module\XQMaileon\Mapper;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\transactions\ContactReference;

class CustomerContactMapper
{

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
                StandardContactField::$GENDER => self::mapGender($customer->id_gender),
                StandardContactField::$ORGANIZATION => $customer->company
            )
        );

        # handle address

        $addresses = $customer->getAddresses(\Context::getContext()->language->id);

        if (count($addresses) >= 1) {
            $address = $addresses[0];
            $contact->standard_fields[StandardContactField::$ADDRESS] = $address['address1'];
            if (!empty($address['address2'])) {
                $contact->standard_fields[StandardContactField::$ADDRESS] .= ' ' . $address['address2'];
            }
            $contact->standard_fields[StandardContactField::$CITY] = $address['city'];
            $contact->standard_fields[StandardContactField::$ZIP] = $address['postcode'];
        }

        return $contact;
    }

    /**
     * @return ContactReference
     */
    public static function mapToContactReference(\Customer $customer)
    {
        $contact = new ContactReference();
        $contact->email = $customer->email;

        return $contact;
    }

    /**
     * @return Permission
     */
    public static function mapPermission(bool $optin)
    {
        $goalPermission = (new OptInPermissionMapper())->getCurrentPermission();
        return $optin ? $goalPermission : Permission::$NONE;
    }

    public static function mapGender($genderId): string
    {
        switch ($genderId) {
            case 1:
                return 'm';
            case 2:
                return 'f';
            default:
                return '';
        }
    }
}
