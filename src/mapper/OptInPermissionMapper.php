<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2022 XQueue GmbH
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
 *  @copyright 2013-2022 XQueue
 *  @license   MIT
 */

namespace PrestaShop\Module\XQMaileon\Mapper;

use de\xqueue\maileon\api\client\contacts\Permission;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;

class OptInPermissionMapper
{
    private $permission_options;

    public function __construct()
    {
        $this->permission_options = [
            [
                'name' => 'None',
                'xq_val' => Permission::$NONE
            ],
            [
                'name' => 'Single Opt-In',
                'xq_val' => Permission::$SOI
            ],
            [
                'name' => 'Confirmed Opt-In',
                'xq_val' => Permission::$COI
            ],
            [
                'name' => 'Double Opt-In',
                'xq_val' => Permission::$DOI
            ],
            [
                'name' => 'Double Opt-In+',
                'xq_val' => Permission::$DOI_PLUS
            ],
        ];
    }

    public function getPermissionOptions()
    {
        return array_map(function ($x) {
            $x["val"] = $x["xq_val"]->getCode();
            return $x;
        }, $this->permission_options);
    }

    /**
     * @return Permission
     */
    public function getCurrentPermission()
    {
        $code = \Configuration::get(ConfigOptions::XQMAILEON_PERMISSION_MODE);
        return $this->getPermission($code);
    }

    /**
     * @return Permission
     */
    public function getAbandonedCartNewPermission()
    {
        $code = \Configuration::get(ConfigOptions::XQMAILEON_PERMISSION_MODE_ABANDONED_CART);
        return $this->getPermission($code);
    }

    /**
     * @return Permission
     */
    public function getOrderConfNewPermission()
    {
        $code = \Configuration::get(ConfigOptions::XQMAILEON_PERMISSION_MODE_ORDER_CONF);
        return $this->getPermission($code);
    }

    public function getCurrentHasDoubleOptIn()
    {
        return $this->getCurrentPermission()->getCode() >= Permission::$DOI->getCode();
    }

    public function getCurrentHasDoubleOptInPlus()
    {
        return $this->getCurrentPermission()->getCode() == Permission::$DOI_PLUS->getCode();
    }

    public function getPermission($code)
    {
        switch ($code) {
            case 1:
                return Permission::$NONE;
            case "none":
                return Permission::$NONE;
            case 2:
                return Permission::$SOI;
            case "soi":
                return Permission::$SOI;
            case 3:
                return Permission::$COI;
            case "coi":
                return Permission::$COI;
            case 4:
                return Permission::$DOI;
            case "doi":
                return Permission::$DOI;
            case 5:
                return Permission::$DOI_PLUS;
            case "doi+":
                return Permission::$DOI_PLUS;
            case 6:
                return Permission::$OTHER;
            case "other":
                return Permission::$OTHER;

            default:
                return Permission::$OTHER;
        }
    }
}
