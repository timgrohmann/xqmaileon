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
 */

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
    public function renderForm()
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

    public function l($string)
    {
        return $this->module->l($string, 'xqconfigureform');
    }


    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="material-icons mi-input">vpn_key</i>',
                        'desc' => $this->l('Enter your Maileon API Key.'),
                        'name' => ConfigOptions::XQMAILEON_API_KEY,
                        'label' => $this->l('API-Key'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Goal permission'),
                        'name' => ConfigOptions::XQMAILEON_PERMISSION_MODE,
                        'desc' => $this->l('Set the goal permission which should be enforced by maileon.'),
                        'options' => array(
                            'query' => (new OptInPermissionMapper())->getPermissionOptions(),
                            'id' => 'val',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send order confirmation mails from Maileon'),
                        'name' => ConfigOptions::XQMAILEON_SEND_ORDER_CONF,
                        'is_bool' => true,
                        'desc' => $this->l('If order confirmation mails should be sent from Maileon or not.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Permission for order confimation'),
                        'name' => ConfigOptions::XQMAILEON_PERMISSION_MODE_ORDER_CONF,
                        'desc' => $this->l('This permission will be set if a new maileon contact is created on order confimation.'),
                        'options' => array(
                            'query' => (new OptInPermissionMapper())->getPermissionOptions(),
                            'id' => 'val',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Permission for abandoned cart'),
                        'name' => ConfigOptions::XQMAILEON_PERMISSION_MODE_ABANDONED_CART,
                        'desc' => $this->l('This permission will be set if a new maileon contact is created on an abandoned cart notification.'),
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
                        'label' => $this->l('DOI-Key'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send DOI-Mails from Maileon'),
                        'name' => ConfigOptions::XQMAILEON_SEND_DOI_WITH_MAILEON,
                        'is_bool' => true,
                        'desc' => $this->l('If DOI-Mails should be sent from Maileon or not.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'col' => 6,
                        'label' => $this->l('Newsletter subscription of a registered customer'),
                        'name' => ConfigOptions::XQMAILEON_SUBSCRIPTION_SIGNEDIN_PERMISSION,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Set permission automatically')
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->l('Start DOI process')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Newsletter subscription in page footer'),
                        'name' => ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER,
                        'is_bool' => true,
                        'desc' => $this->l('If there should be a registration form above the shops footer.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'name' => ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_A,
                        'label' => $this->l('Text to display above registration form.'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => ConfigOptions::XQMAILEON_NEWSLETTER_SIGNUP_FOOTER_TEXT_B,
                        'label' => $this->l('Text to display below registration form.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Newsletter subscription during checkout'),
                        'name' => ConfigOptions::XQMAILEON_REG_CHECKOUT,
                        'is_bool' => true,
                        'desc' => $this->l('If the option to subscribe to the newsletter should be shown on checkout page.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'prefix' => '<i class="material-icons mi-input">timer</i>',
                        'name' => ConfigOptions::XQMAILEON_ABANDONED_TIME,
                        'label' => $this->l('Abandoned Cart Timer'),
                        'type' => 'select',
                        'col' => 6,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 5,
                                    'name' => $this->l('5 minutes')
                                ),
                                array(
                                    'id_option' => 15,
                                    'name' => $this->l('15 minutes')
                                ),
                                array(
                                    'id_option' => 30,
                                    'name' => $this->l('30 minutes')
                                ),
                                array(
                                    'id_option' => 60,
                                    'name' => $this->l('60 minutes')
                                ),
                                array(
                                    'id_option' => 120,
                                    'name' => $this->l('2 hours')
                                ),
                                array(
                                    'id_option' => 240,
                                    'name' => $this->l('4 hours')
                                ),
                                array(
                                    'id_option' => 480,
                                    'name' => $this->l('8 hours')
                                ),
                                array(
                                    'id_option' => 720,
                                    'name' => $this->l('12 hours')
                                ),
                                array(
                                    'id_option' => 1440,
                                    'name' => $this->l('1 day')
                                ),
                                array(
                                    'id_option' => null,
                                    'name' => $this->l('inactive')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
        $x = ConfigOptions::ALL_OPTIONS;
        array_walk($x, function (&$v, $k) {
            # get value from config or default from options if not set
            $v = \Configuration::get($k, null, null, null, $v);
        });
        return $x;
    }
}
