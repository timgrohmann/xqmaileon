<?php

namespace PrestaShop\Module\XQMaileon;

use Configuration;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Mapper\CustomerContactMapper;
use PrestaShop\Module\XQMaileon\Mapper\OptInPermissionMapper;

class MaileonRegister {

    private string $api_key;

    private ContactsService $contactsService;
    private OptInPermissionMapper $permissionMapper;

    private string $BASE_URL = 'http://localhost:5555';

    public function __construct(string $api_key) {
        $this->api_key = $api_key;

        $config = array(
            'BASE_URI' => 'http://localhost:5555',
            'API_KEY' => 'XX-XXXX-XX'
        );
        $this->contactsService = new ContactsService($config);
        $this->permissionMapper = new OptInPermissionMapper();
    }

    public function addContact(\Customer $customer)
    {
        $contact = CustomerContactMapper::map($customer);
        try {
            $this->contactsService->createContact(
                $contact,
                SynchronizationMode::$UPDATE,
                "Prestashop Plugin",
                null,
                $this->permissionMapper->getCurrentHasDoubleOptIn(),
                $this->permissionMapper->getCurrentHasDoubleOptInPlus(),
                Configuration::get(ConfigOptions::XQMAILEON_DOI_KEY)
            );
        } catch (\Throwable $th) {
        }
    }

    public function updateContact(\Customer $customer) {
        $contact = CustomerContactMapper::map($customer);
        $this->contactsService->updateContact($contact);
    }

    public function removeContact(\Customer $customer)
    {
        # TODO remove customer
    }
}