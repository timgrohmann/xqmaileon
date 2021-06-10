<?php

namespace PrestaShop\Module\XQMaileon;

use Configuration;
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

        $config = array(
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' =>  \Configuration::get(ConfigOptions::XQMAILEON_API_KEY)
        );
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
                "Prestashop Plugin",
                null,
                $this->permissionMapper->getCurrentHasDoubleOptIn(),
                $this->permissionMapper->getCurrentHasDoubleOptInPlus(),
                Configuration::get(ConfigOptions::XQMAILEON_DOI_KEY)
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
                "Prestashop Plugin Startseite",
                null,
                $this->permissionMapper->getCurrentHasDoubleOptIn(),
                $this->permissionMapper->getCurrentHasDoubleOptInPlus(),
                Configuration::get(ConfigOptions::XQMAILEON_DOI_KEY)
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
