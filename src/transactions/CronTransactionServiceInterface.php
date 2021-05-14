<?php

namespace PrestaShop\Module\XQMaileon\Transactions;

interface CronTransactionServiceInterface {
    /**
     * Periodically called by cron service.
     * Return value will be sent to cron trigger as JSON.
     */
    public function trigger(): array;
}