<?php

use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;
use PrestaShop\Module\XQMaileon\Transactions\CronTransactionServiceInterface;

class XQMaileonCronModuleFrontController extends ModuleFrontController
{
    # All services that will be triggered by the Cron Task
    /**
     * @var CronTransactionServiceInterface[]
     */
    const SERVICES = [
        AbandonedCartTransactionService::class
    ];

    public function initContent()
    {
        parent::initContent();
        header("Content-Type: application/json");

        $cron_token = Tools::getValue('token', false);
        if ($cron_token == false || $cron_token != Configuration::get(ConfigOptions::XQMAILEON_CRON_TOKEN)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Invalid Cron Token'
            ]);
            exit;
        }

        $ret = [];
        foreach (self::SERVICES as $serviceClass) {
            $service  = new $serviceClass();
            $result = $service->trigger();
            # sets result value to return array on service class name as key
            $path = explode('\\', (string) $serviceClass);
            $ret[array_pop($path)] = $result;
        }

        echo json_encode($ret);
        exit;
        # do cron stuff now
    }
}
