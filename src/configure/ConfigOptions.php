<?php

namespace PrestaShop\Module\XQMaileon\Configure;

use de\xqueue\maileon\api\client\contacts\Permission;

abstract class ConfigOptions {
    const XQMAILEON_API_KEY = 'XQMAILEON_API_KEY';
    const XQMAILEON_REG_CHECKOUT = 'XQMAILEON_REG_CHECKOUT';
    const XQMAILEON_PERMISSION_MODE = 'XQMAILEON_PERMISSION_MODE';
    const XQMAILEON_DOI_KEY = 'XQMAILEON_DOI_KEY';
    const XQMAILEON_SEND_DOI_WITH_MAILEON = 'XQMAILEON_SEND_DOI_WITH_MAILEON';
    const XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION = 'XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION';
    const XQMAILEON_ABANDONED_TIME = 'XQMAILEON_ABANDONED_TIME';

    const XQMAILEON_CRON_TOKEN = 'XQMAILEON_CRON_TOKEN';
    const XQMAILEON_WEBHOOK_TOKEN = 'XQMAILEON_WEBHOOK_TOKEN';



    # default values
    const all_options = [
        ConfigOptions::XQMAILEON_API_KEY => null,
        ConfigOptions::XQMAILEON_REG_CHECKOUT => true,
        ConfigOptions::XQMAILEON_PERMISSION_MODE => 4,
        ConfigOptions::XQMAILEON_DOI_KEY => null,
        ConfigOptions::XQMAILEON_SEND_DOI_WITH_MAILEON => true,
        ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION => 1,
        ConfigOptions::XQMAILEON_ABANDONED_TIME => '15'
    ];
    
    public static function getOptionBool(string $key)
    {
        return !empty(\Configuration::get($key));
    }
}