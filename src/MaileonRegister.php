<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2023 XQueue GmbH
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
 *  @copyright 2013-2023 XQueue
 *  @license   MIT
 */

namespace PrestaShop\Module\XQMaileon;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Mapper\CustomerContactMapper;
use PrestaShop\Module\XQMaileon\Mapper\OptInPermissionMapper;

class MaileonRegister
{
    /**
     * @var string
     */
    private $api_key;

    /**
     * @var ContactsService
     */
    private $contactsService;

    /**
     * @var OptInPermissionMapper
     */
    private $permissionMapper;

    /**
     * @var string
     */
    private $BASE_URI = 'https://api.maileon.com/1.0';

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;

        $config = [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' => \Configuration::get(ConfigOptions::XQMAILEON_API_KEY),
        ];
        $this->contactsService = new ContactsService($config);
        $this->permissionMapper = new OptInPermissionMapper();
    }

    public function addContact(\Customer $customer)
    {
        $contact = CustomerContactMapper::map($customer);
        try {
            $result = $this->contactsService->createContact(
                $contact,
                SynchronizationMode::$UPDATE,
                'Prestashop Plugin',
                null,
                $this->permissionMapper->getCurrentHasDoubleOptIn(),
                $this->permissionMapper->getCurrentHasDoubleOptInPlus(),
                \Configuration::get(ConfigOptions::XQMAILEON_DOI_KEY)
            );

            return $result->isSuccess();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function addEmail(string $mail): bool
    {
        $contact = new Contact(null, $mail, Permission::$SOI);
        try {
            $result = $this->contactsService->createContact(
                $contact,
                SynchronizationMode::$UPDATE,
                'Prestashop Plugin Startseite',
                null,
                $this->permissionMapper->getCurrentHasDoubleOptIn(),
                $this->permissionMapper->getCurrentHasDoubleOptInPlus(),
                \Configuration::get(ConfigOptions::XQMAILEON_DOI_KEY)
            );

            return $result->isSuccess();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateCustomerEmail(\Customer $customer, string $oldMail)
    {
        $this->contactsService->updateContactByEmail($oldMail, CustomerContactMapper::map($customer));
    }

    public function updateContact(\Customer $customer)
    {
        $contact = CustomerContactMapper::map($customer);
        $this->contactsService->updateContact($contact);
    }

    public function removeContact(\Customer $customer)
    {
        $this->contactsService->deleteContactByEmail($customer->email);
    }
}
