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

use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;
use PrestaShop\Module\XQMaileon\Transactions\CronTransactionServiceInterface;

class XQMaileonCronModuleFrontController extends ModuleFrontController
{
    // All services that will be triggered by the Cron Task
    /**
     * @var CronTransactionServiceInterface[]
     */
    const SERVICES = [
        AbandonedCartTransactionService::class,
    ];

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        $cron_token = Tools::getValue('token', false);
        if ($cron_token == false || $cron_token != Configuration::get(ConfigOptions::XQMAILEON_CRON_TOKEN)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Invalid Cron Token',
            ]);
            exit;
        }

        $ret = [];
        foreach (self::SERVICES as $serviceClass) {
            $service = new $serviceClass();
            $result = $service->trigger();
            // sets result value to return array on service class name as key
            $path = explode('\\', (string) $serviceClass);
            $ret[array_pop($path)] = $result;
        }

        echo json_encode($ret);
        exit;
        // do cron stuff now
    }
}
