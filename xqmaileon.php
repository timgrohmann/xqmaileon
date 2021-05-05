<?php

/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once __DIR__ . '/vendor/autoload.php';


use PrestaShop\Module\XQMaileon\MaileonRegister;
use PrestaShop\Module\XQMaileon\Configure\XQConfigureForm;

use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\HelperForm;
use PrestaShop\PrestaShop\Adapter\Entity\Module;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Adapter\Entity\Customer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Xqmaileon extends Module
{
    /**
     * @var XQConfigureForm
     */
    protected $config_form;
    protected $registerer;

    public static string $NEWSLETTER_FIELD = 'newsletter';

    public function __construct()


    {
        $this->name = 'xqmaileon';
        $this->tab = 'emailing';
        $this->version = '1.0.0';
        $this->author = 'XQueue';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('XQueue Maileon');
        $this->description = $this->l('XQueue Maileon is great.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);


        $api_key = Configuration::get('XQMAILEON_API_KEY');
        if (!empty($api_key)) {
            $this->registerer = new MaileonRegister($api_key);
        }

        $this->config_form = new XQConfigureForm($this);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('XQMAILEON_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('actionCustomerAccountUpdate') &&
            $this->registerHook('actionObjectCustomerUpdateBefore');
    }

    public function uninstall()
    {
        Configuration::deleteByName('XQMAILEON_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitXqmaileonModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        $output .= $this->config_form->renderForm();

        $client = new Client();

        $api_success = false;

        $key = Configuration::get('XQMAILEON_API_KEY');

        try {
            /**
             * @var ResponseInterface
             */
            $res = $client->get("https://api.maileon.com/1.0/ping", array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($key)
                )
            ));
            $api_success = $res->getStatusCode() == 200;
        } catch (RequestException $e) {
        }


        $this->context->smarty->assign('api_success', $api_success);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/api-test.tpl');

        return $output;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = ConfigOptions::all_options;

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        ## workaround

        $field_name = $this->name . '_' . Xqmaileon::$NEWSLETTER_FIELD;
        $this->context->customer->{$field_name} = $this->context->customer->newsletter;

        $additionalFields = array();
        if (Configuration::get('XQMAILEON_REG_CHECKOUT', false)) {
            $additionalFields[] = (new FormField)
                ->setName(Xqmaileon::$NEWSLETTER_FIELD)
                ->setType('checkbox')
                ->setLabel($this->l('Möchten Sie unseren Newsletter abbonieren?'));
        }
        return $additionalFields;
    }

    public function hookActionCustomerAccountAdd($params)
    {
        # new user registered and checked the newsletter field
        /**
         * @var Customer
         */
        $customer = $params['newCustomer'];
        if (ConfigOptions::getOptionBool(ConfigOptions::XQMAILEON_REG_CHECKOUT) && !empty(Tools::getValue(Xqmaileon::$NEWSLETTER_FIELD))) {
            
            # automatically set optin on customer when they have an account and it is configured
            if (Configuration::get(ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION) == 1 && !$customer->isGuest()) {
                $customer->optin = true;
            }
            $customer->newsletter = true;
            $this->registerer->addContact($customer);
        }
    }

    /**
     * Called when customer updates information.
     */
    public function hookActionObjectCustomerUpdateBefore($params)
    {

        /**
         * @var Customer
         */
        $customer = $params['object'];

        # retrieves stored state of customer from db
        $old = new Customer($customer->id);

        if ($customer->isGuest()) return;

        $new_newsletter_state = !empty(Tools::getValue(Xqmaileon::$NEWSLETTER_FIELD));
        $old_newsletter_state = !empty($old->newsletter);

        $new_email = Tools::getValue('email');
        $old_email = $old->email;

        # newly subscribed to newsletter: tell maileon
        if ($new_newsletter_state == true && $old_newsletter_state == false) {
            if (Configuration::get(ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION) == 1) {
                $customer->optin = true;
                $this->registerer->addContact($customer);
            } else {
                $this->registerer->addContact($customer);
            }
            
            $customer->newsletter = true;
        }

        # unsubscribed from newsletter: tell maileon
        if ($new_newsletter_state == false && $old_newsletter_state == true) {
            $this->registerer->removeContact($customer);
            $customer->newsletter = false;
        }

        if ($new_email != $old_email && !empty($old_email) && !$customer->isGuest()) {
            # email updated, check with update options
        }


    }
}
