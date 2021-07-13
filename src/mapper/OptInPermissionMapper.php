<?php

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
