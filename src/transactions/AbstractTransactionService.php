<?php

namespace PrestaShop\Module\XQMaileon\Transactions;

use de\xqueue\maileon\api\client\transactions\TransactionsService;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;

abstract class AbstractTransactionService {

    protected TransactionsService $transactionService;

    public function __construct()
    {
        $config = array(
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' =>  \Configuration::get(ConfigOptions::XQMAILEON_API_KEY)
        );
        $this->transactionService = new TransactionsService($config);
        # $this->transactionService->setDebug(true);
    }
}