<?php

namespace PrestaShop\Module\XQMaileon\Model\Transaction;

use de\xqueue\maileon\api\client\transactions\AttributeType;
use de\xqueue\maileon\api\client\transactions\Transaction;
use de\xqueue\maileon\api\client\transactions\TransactionsService;
use de\xqueue\maileon\api\client\transactions\TransactionType;
use PrestaShop\Module\XQMaileon\Mapper\CustomerContactMapper;

abstract class AbstractTransaction
{

    const TRANSACTION_PREFIX = 'prestashop_';
    const TRANSACTION_SUFFIX = '_1.0';

    private TransactionsService $transactionService;
    protected \Customer $customer;

    public function __construct(TransactionsService $transactionService, \Customer $customer)
    {
        $this->transactionService = $transactionService;
        $this->customer = $customer;
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
        $underscored = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return self::TRANSACTION_PREFIX . $underscored . self::TRANSACTION_SUFFIX;
    }

    /**
     * @return array associative array of key => type. prepend key with '!' to make explicit
     */
    protected abstract function getTypeDescription();

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
            if (substr($name, -1) == '!') {
                $name = substr($name, 0, -1);
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
        $maileonTransaction = new Transaction();
        $maileonTransaction->typeName = $this->getTypeName();
        $maileonTransaction->contact = CustomerContactMapper::mapToContactReference($this->customer);
        $maileonTransaction->content = $content;

        $res = $this->transactionService->createTransactions(array($maileonTransaction));

        return $res->isSuccess();
    }
}
