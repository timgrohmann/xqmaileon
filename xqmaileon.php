<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2021 XQueue GmbH
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
 *  @copyright 2013-2021 XQueue
 *  @license   MIT
 */

require_once dirname(__FILE__) . '/vendor/autoload.php';

use de\xqueue\maileon\api\client\MaileonAPIException;
use de\xqueue\maileon\api\client\utils\PingService;
use PrestaShop\Module\XQMaileon\MaileonRegister;
use PrestaShop\Module\XQMaileon\Configure\XQConfigureForm;

use PrestaShop\PrestaShop\Adapter\Entity\Module;
use PrestaShop\PrestaShop\Adapter\Entity\Customer;

use PrestaShop\Module\XQMaileon\Configure\ConfigOptions;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;
use PrestaShop\Module\XQMaileon\Transactions\OrderConfirmationTransactionService;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Xqmaileon extends Module
{
    /**
     * @var XQConfigureForm
     */
    protected $config_form;

    /**
     * @var MaileonRegister
     */
    protected $registerer;

    public static $NEWSLETTER_FIELD = 'newsletter';
    public static $CONTACT_FORM_MARKER_FIELD = 'XQM_CONTACT_FORM_MARKER_FIELD';

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

        $this->displayName = $this->l('Maileon Integration');
        $this->description = $this->l('Seamlessly integrate your marketing automation with Maileon.');

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
            $this->registerHook('header') &&
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('actionCustomerAccountUpdate') &&
            $this->registerHook('actionObjectCustomerUpdateBefore') &&
            $this->registerHook('actionEmailSendBefore') &&
            $this->registerHook('displayFooterBefore') &&
            AbandonedCartTransactionService::installDatabase();
    }

    public function uninstall()
    {
        # foreach (ConfigOptions::ALL_OPTIONS as $k => $v) Configuration::deleteByName($k);

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

        $api_success = false;
        $key = \Configuration::get(ConfigOptions::XQMAILEON_API_KEY);

        try {
            $config = array(
                'BASE_URI' => 'https://api.maileon.com/1.0',
                'API_KEY' =>  $key
            );
            $pingService = new PingService($config);
            $api_success = $pingService->pingGet()->getStatusCode() == 200;
        } catch (MaileonAPIException $e) {
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
        $form_values = ConfigOptions::ALL_OPTIONS;

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
        if (ConfigOptions::getOptionBool(ConfigOptions::XQMAILEON_REG_CHECKOUT)) {
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
        if (!Tools::isSubmit(Xqmaileon::$CONTACT_FORM_MARKER_FIELD)) {
            return;
        }

        /**
         * The new value of the customer object that will be written in the update.
         * @var Customer
         */
        $customer = $params['object'];

        # retrieves stored state of customer from db
        $old = new Customer($customer->id);

        if ($customer->isGuest()) {
            return;
        }

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

    # do not send order confirmations from prestashop, use maileon instead
    public function hookActionEmailSendBefore($params)
    {
        if ($params['template'] == 'order_conf' && ConfigOptions::getOptionBool(ConfigOptions::XQMAILEON_SEND_ORDER_CONF)) {
            $order = new \Order($params['templateVars']['{id_order}']);
            $service = new OrderConfirmationTransactionService();
            return !$service->sendConfirmation($order);
        }
        return true;
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }

    public function hookDisplayFooterBefore($params)
    {

        if ($this->context->customer->newsletter) {
            return;
        }

        if (!ConfigOptions::getOptionBool(ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER)) {
            return;
        }

        $this->context->smarty->assign([
            'id_module' => $this->id,
        ]);

        $variables = [
            'msg' => false,
            'nw_error' => false,
            'text_a' => \Configuration::get(ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_A),
            'text_b' => \Configuration::get(ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_B),
            'value' => ''
        ];

        if (Tools::isSubmit('submitNewsletter')) {
            $email = Tools::getValue('email', '');
            $customer = $this->context->customer;
            $success = false;
            if (!empty($customer) && $email == $customer->email) {
                $success = $this->registerer->addContact($customer);
                $customer->newsletter = true;
                $customer->update();
            } else {
                $success = $this->registerer->addEmail(Tools::getValue('email'));
            }

            if ($success) {
                $variables['msg'] = $this->l('Successfully registered new email:') . ' ' . Tools::getValue('email', '');
                $variables['nw_error'] = false;
            } else { /* @phpstan-ignore-line */
                $variables['msg'] = $this->l('Newsletter signup failed. Try again later.');
                $variables['nw_error'] = true;
            }
            $variables['value'] = $email;
        }

        $email = $this->context->customer->email;
        if (!empty($email)) {
            $variables['value'] = $email;
        }

        $this->smarty->assign($variables);

        return $this->fetch('module:xqmaileon/views/templates/front/newsletter-signup.tpl');
    }
}
