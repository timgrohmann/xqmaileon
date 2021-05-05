<?php

namespace PrestaShop\Module\XQMaileon\Configure;

use PrestaShop\Module\XQMaileon\Mapper\OptInPermissionMapper;

class XQConfigureForm
{
    /**
     * @var Module
     */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    function renderForm()
    {
        $context = \Context::getContext();
        $helper = new \HelperForm();

        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->default_form_language = $context->language->id;

        $helper->submit_action = 'submitXqmaileonModule';
        $helper->currentIndex = \AdminController::$currentIndex . '&configure=' . $this->module->name;
        $helper->token = \Tools::getAdminTokenLite('AdminModules');

        dump($this->getConfigFormValues());
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $context->controller->getLanguages(),
            'id_language' => $context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Live mode'),
                        'name' => ConfigOptions::XQMAILEON_LIVE_MODE,
                        'is_bool' => true,
                        'desc' => $this->module->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Zielpermission'),
                        'name' => ConfigOptions::XQMAILEON_PERMISSION_MODE,
                        'desc' => $this->module->l('Use this module in live mode'),
                        'options' => array(
                            'query' => (new OptInPermissionMapper())->getPermissionOptions(),
                            'id' => 'val',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="material-icons mi-input">mail</i>',
                        'name' => ConfigOptions::XQMAILEON_DOI_KEY,
                        'label' => $this->module->l('DOI-Schlüssel'),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="material-icons mi-input">insert_link</i>',
                        'name' => ConfigOptions::XQMAILEON_WEBHOOK_TOKEN,
                        'label' => $this->module->l('Webhook-Token'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('DOI-Mails aus Maileon senden'),
                        'name' => ConfigOptions::XQMAILEON_SEND_DOI_WITH_MAILEON,
                        'is_bool' => true,
                        'desc' => $this->module->l('Ob DOI-Mails mit Maileon versendet werden sollen, oder nicht.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'col' => 6,
                        'label' => $this->module->l('Newsletter-Registrierung eines angemeldeten Kunden'),
                        'name' => ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->module->l('Permission automatisch setzen')
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->module->l('DOI-Prozess staren')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Newsletteranmeldung im Checkout'),
                        'name' => ConfigOptions::XQMAILEON_REG_CHECKOUT,
                        'is_bool' => true,
                        'desc' => $this->module->l('Ob eine Newsletteranmeldung im Checkout angezeigt werden soll.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="material-icons mi-input">vpn_key</i>',
                        'desc' => $this->module->l('Enter your Maileon API Key.'),
                        'name' => ConfigOptions::XQMAILEON_API_KEY,
                        'label' => $this->module->l('API-Key'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     * Creates a copy of the default value map and replaces by Configuration Parameters.
     */
    protected function getConfigFormValues()
    {
        $x = ConfigOptions::all_options;
        array_walk($x, function (&$v, $k) {
            $v = \Configuration::get($k, $v);
        });
        return $x;
    }
}
