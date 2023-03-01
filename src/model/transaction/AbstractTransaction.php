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

namespace PrestaShop\Module\XQMaileon\Model\Transaction;

use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\transactions\AttributeType;
use de\xqueue\maileon\api\client\transactions\Transaction;
use de\xqueue\maileon\api\client\transactions\TransactionsService;
use de\xqueue\maileon\api\client\transactions\TransactionType;
use PrestaShop\Module\XQMaileon\Mapper\CustomerContactMapper;

abstract class AbstractTransaction
{
    const TRANSACTION_PREFIX = 'prestashop_';
    const TRANSACTION_SUFFIX = '_1.0';

    /**
     * @var TransactionsService
     */
    private $transactionService;

    /**
     * @var ContactsService
     */
    private $contactService;

    /**
     * @var \Customer
     */
    protected $customer;

    /**
     * @var Permission
     */
    private $permission;

    public function __construct(TransactionsService $transactionService, ContactsService $contactService, \Customer $customer, Permission $permission)
    {
        $this->transactionService = $transactionService;
        $this->contactService = $contactService;
        $this->customer = $customer;
        $this->permission = $permission;
    }

    /**
     * Takes the class name of the instanciated class and converts it to snake_case
     * to conform to Maileon's transaction type naming convention. Also adds prefix
     * and suffix.
     *
     * Example: AbandonedCartTransaction => prestashop_abandoned_cart_transaction_1.0
     *
     * @return string Formatted name
     */
    public function getTypeName()
    {
        $fqn = explode('\\', get_class($this));
        $name = array_pop($fqn);
        $underscored = \Tools::strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        return self::TRANSACTION_PREFIX . $underscored . self::TRANSACTION_SUFFIX;
    }

    /**
     * @return array associative array of key => type. prepend key with '!' to make explicit
     */
    abstract protected function getTypeDescription();

    /**
     * Maps 'useful' type description to maileon description
     *
     * @return TransactionType
     */
    public function getTransactionType()
    {
        $desc = $this->getTypeDescription();

        $transactionType = new TransactionType(null, $this->getTypeName());
        foreach ($desc as $name => $type) {
            $required = false;
            if (\Tools::substr($name, -1) == '!') {
                $name = \Tools::substr($name, 0, -1);
                $required = true;
            }
            $transactionType->attributes[] =
                new AttributeType(null, $name, $type, $required);
        }

        return $transactionType;
    }

    private function checkExistsOrCreate(): bool
    {
        $exists = $this->transactionService->getTransactionType($this->getTypeName())->isSuccess();
        if ($exists) {
            return true;
        }

        $result = $this->transactionService->createTransactionType($this->getTransactionType());

        return $result->isSuccess();
    }

    public function send($content): bool
    {
        if (!$this->checkExistsOrCreate()) {
            error_log('Transaction type ' . $this->getTypeName() . ' cannot be created.');

            return false;
        }

        $contact = CustomerContactMapper::map($this->customer);
        $contact->permission = $this->permission;

        $this->contactService->createContact($contact, SynchronizationMode::$IGNORE);

        $maileonTransaction = new Transaction();
        $maileonTransaction->typeName = $this->getTypeName();
        $maileonTransaction->contact = CustomerContactMapper::mapToContactReference($this->customer);
        $maileonTransaction->content = $content;

        $res = $this->transactionService->createTransactions([$maileonTransaction]);

        return $res->isSuccess();
    }
}
