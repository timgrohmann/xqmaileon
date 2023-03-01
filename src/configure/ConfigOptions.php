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

namespace PrestaShop\Module\XQMaileon\Configure;

abstract class ConfigOptions
{
    const XQMAILEON_API_KEY = 'XQMAILEON_API_KEY';
    const XQMAILEON_REG_CHECKOUT = 'XQMAILEON_REG_CHECKOUT';
    const XQMAILEON_PERMISSION_MODE = 'XQMAILEON_PERMISSION_MODE';
    const XQMAILEON_SEND_ORDER_CONF = 'XQMAILEON_SEND_ORDER_CONF';
    const XQMAILEON_PERMISSION_MODE_ORDER_CONF = 'XQMAILEON_PERMISSION_MODE_ORDER_CONF';
    const XQMAILEON_PERMISSION_MODE_ABANDONED_CART = 'XQMAILEON_PERMISSION_MODE_ABANDONED_CART';
    const XQMAILEON_DOI_KEY = 'XQMAILEON_DOI_KEY';
    const XQMAILEON_SEND_DOI_WITH_MAILEON = 'XQMAILEON_SEND_DOI_WITH_MAILEON';
    const XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION = 'XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION';
    const XQMAILEON_ABANDONED_TIME = 'XQMAILEON_ABANDONED_TIME';

    const XQMAILEON_CRON_TOKEN = 'XQMAILEON_CRON_TOKEN';
    const XQMAILEON_WEBHOOK_TOKEN = 'XQMAILEON_WEBHOOK_TOKEN';

    const XQMAILEON_NEWSLETTER_SIGNUP_FOOTER = 'XQMAILEON_NEWSLETTER_SIGNUP_FOOTER';
    const XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_A = 'XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_A';
    const XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_B = 'XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_B';

    // default values
    const ALL_OPTIONS = [
        ConfigOptions::XQMAILEON_API_KEY => null,
        ConfigOptions::XQMAILEON_REG_CHECKOUT => true,
        ConfigOptions::XQMAILEON_PERMISSION_MODE => 4,
        ConfigOptions::XQMAILEON_DOI_KEY => null,
        ConfigOptions::XQMAILEON_SEND_DOI_WITH_MAILEON => true,
        ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION => 1,
        ConfigOptions::XQMAILEON_ABANDONED_TIME => '15',
        ConfigOptions::XQMAILEON_SEND_ORDER_CONF => true,
        ConfigOptions::XQMAILEON_PERMISSION_MODE_ORDER_CONF => 1,
        ConfigOptions::XQMAILEON_PERMISSION_MODE_ABANDONED_CART => 1,
        ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER => true,
        ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_A => '',
        ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_B => '',
    ];

    public static function getOptionBool(string $key)
    {
        return !empty(\Configuration::get($key));
    }
}
