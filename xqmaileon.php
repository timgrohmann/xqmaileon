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
use PrestaShop\PrestaShop\Adapter\Entity\Module;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Adapter\Entity\Customer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;

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
    public static string $CONTACT_FORM_MARKER_FIELD = 'XQM_CONTACT_FORM_MARKER_FIELD';

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
        Configuration::updateValue(ConfigOptions::XQMAILEON_CRON_TOKEN, Tools::passwdGen(16));
        Configuration::updateValue(ConfigOptions::XQMAILEON_WEBHOOK_TOKEN, Tools::passwdGen(16));

        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('actionCustomerAccountUpdate') &&
            $this->registerHook('actionObjectCustomerUpdateBefore') &&
            AbandonedCartTransactionService::installDatabase();
    }

    public function uninstall()
    {
        foreach (ConfigOptions::all_options as $k => $v) Configuration::deleteByName($k);

        return parent::uninstall() &&
            AbandonedCartTransactionService::uninstallDatabase();
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
        $this->context->smarty->assign('api_key_set', !empty($key));
        $cron_hint = $this->context->link->getModuleLink($this->name, 'cron', array('token' => Configuration::get(ConfigOptions::XQMAILEON_CRON_TOKEN)), Configuration::get('PS_SSL_ENABLED'));

        $this->context->smarty->assign('cron_token', $cron_hint);

        $webhookToken = Configuration::get(ConfigOptions::XQMAILEON_WEBHOOK_TOKEN);
        $webhookDoiConfirm = $this->context->link->getModuleLink($this->name, 'webhook', array('token' => $webhookToken, 'type' => 'doi'), Configuration::get('PS_SSL_ENABLED'));
        $webhookUnsubscribe = $this->context->link->getModuleLink($this->name, 'webhook', array('token' => $webhookToken, 'type' => 'unsubscribe'), Configuration::get('PS_SSL_ENABLED'));

        $this->context->smarty->assign('webhook_doi_confirm', $webhookDoiConfirm);
        $this->context->smarty->assign('webhook_unsubscribe', $webhookUnsubscribe);

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
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
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
                ->setLabel($this->l('Do you want to subscribe to our newsletter?'));
        }
        $additionalFields[] = (new FormField)
            ->setName(Xqmaileon::$CONTACT_FORM_MARKER_FIELD)
            ->setType('hidden')
            ->setValue(1);
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
        # return if not submitted from customer account from,
        # customer objects may be modified by other parties and not considered
        # for this plugin
        if (!Tools::isSubmit(Xqmaileon::$CONTACT_FORM_MARKER_FIELD)) return;

        /**
         * The new value of the customer object that will be written in the update.
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
            }

            $this->registerer->addContact($customer);
            $customer->newsletter = true;
        }

        # unsubscribed from newsletter: tell maileon
        if ($new_newsletter_state == false && $old_newsletter_state == true) {
            $this->registerer->removeContact($customer);
            $customer->newsletter = false;
            $customer->optin = false;
        }

        if ($new_email != $old_email && !empty($old_email) && !$customer->isGuest()) {
            $customer->optin = (Configuration::get(ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION) == 1);
            $this->registerer->updateCustomerEmail($customer, $old_email);
        }


    }
}
