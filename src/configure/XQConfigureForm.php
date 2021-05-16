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
                        'type' => 'text',
                        'prefix' => '<i class="material-icons mi-input">vpn_key</i>',
                        'desc' => $this->module->l('Enter your Maileon API Key.'),
                        'name' => ConfigOptions::XQMAILEON_API_KEY,
                        'label' => $this->module->l('API-Key'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Goal permission'),
                        'name' => ConfigOptions::XQMAILEON_PERMISSION_MODE,
                        'desc' => $this->module->l('Set the goal permission which should be enforced by maileon.'),
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
                        'label' => $this->module->l('DOI-Key'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Send DOI-Mails from Maileon'),
                        'name' => ConfigOptions::XQMAILEON_SEND_DOI_WITH_MAILEON,
                        'is_bool' => true,
                        'desc' => $this->module->l('If DOI-Mails should be sent from Maileon or not.'),
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
                        'label' => $this->module->l('Newsletter subscription of a registered customer'),
                        'name' => ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->module->l('Set permission automatically')
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->module->l('Start DOI process')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Newsletter subscription during checkout'),
                        'name' => ConfigOptions::XQMAILEON_REG_CHECKOUT,
                        'is_bool' => true,
                        'desc' => $this->module->l('If the option to subscribe to the newsletter should be shown on checkout page.'),
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
                        'prefix' => '<i class="material-icons mi-input">timer</i>',
                        'name' => ConfigOptions::XQMAILEON_ABANDONED_TIME,
                        'label' => $this->module->l('Abandoned Cart Timer'),
                        'type' => 'select',
                        'col' => 6,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 5,
                                    'name' => '5 minutes'
                                ),
                                array(
                                    'id_option' => 15,
                                    'name' => '15 minutes'
                                ),
                                array(
                                    'id_option' => 30,
                                    'name' => '30 minutes'
                                ),
                                array(
                                    'id_option' => 60,
                                    'name' => '60 minutes'
                                ),
                                array(
                                    'id_option' => null,
                                    'name' => 'inactive'
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
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
            # get value from config or default from options if not set
            $v = \Configuration::get($k, null, null, null, $v);
        });
        return $x;
    }
}
