<?php

use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;

class XQMaileonCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $cron_token = Tools::getValue('token', false);
        if ($cron_token == false || $cron_token != Configuration::get(ConfigOptions::XQMAILEON_CRON_TOKEN)) {
            http_response_code(400);
            echo 'Invalid Cron Token';
            exit;
        }

        echo 'Cron executed successfully';
        exit;
        # do cron stuff now
    }
}