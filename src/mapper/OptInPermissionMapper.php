<?php

namespace PrestaShop\Module\XQMaileon\Mapper;

use Configuration;
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
        return Permission::getPermission($code);
    }

    /**
     * @return Permission
     */
    public function getAbandonedCartNewPermission()
    {
        $code = \Configuration::get(ConfigOptions::XQMAILEON_PERMISSION_MODE_ABANDONED_CART);
        return Permission::getPermission($code);
    }

    /**
     * @return Permission
     */
    public function getOrderConfNewPermission()
    {
        $code = \Configuration::get(ConfigOptions::XQMAILEON_PERMISSION_MODE_ORDER_CONF);
        return Permission::getPermission($code);
    }

    public function getCurrentHasDoubleOptIn()
    {
        return $this->getCurrentPermission()->getCode() >= Permission::$DOI->getCode();
    }

    public function getCurrentHasDoubleOptInPlus()
    {
        return $this->getCurrentPermission()->getCode() == Permission::$DOI_PLUS->getCode();
    }
}
