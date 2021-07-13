{*
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
*}

<div class="panel">
    <h3><i class="material-icons mi-input">info</i> {l s='Test API-Connection' mod='xqmaileon'}</h3>
    <p>
        {l s='Check if you connected to the Maileon API successfully.' mod='xqmaileon'}<br />
    </p>

    <div class="center">
        {if $api_success}
            <span class="material-icons green">
                done
            </span>
            {l s='Great! The API key you entered works!' mod='xqmaileon'}
        {else if !$api_key_set}
            <span class="material-icons red">
                close
            </span>
            {l s='Please enter an API key in the form above.' mod='xqmaileon'}
        {else}
            <span class="material-icons red">
                close
            </span>
            {l s='The API key you entered does not work. Please check the API key in your Maileon backend.' mod='xqmaileon'}
        {/if}
    </div>

</div>

<div class="panel">
    <h3><i class="material-icons mi-input">timer</i>
        {l s='Set up Cronjob for Abandoned Cart Notifications' mod='xqmaileon'}</h3>
    <p>
        {l s='Add a cron job to your system/server that calls the following URL, to enable abandoned cart notififcations.' mod='xqmaileon'}
    </p>
    <p class="center">
        {$cron_token | escape:'htmlall':'UTF-8' }
    </p>
</div>

<div class="panel">
    <h3><i class="material-icons mi-input">public</i> {l s='Set up Webhooks' mod='xqmaileon'}</h3>
    <p>
        {l s='Please create the following webhooks in your Maileon backend to fully integrate with Prestashop.' mod='xqmaileon'}
    </p>
    <table class="table">
        <tr>
            <th>{l s='Type' mod='xqmaileon'}</th>
            <th>Webhook-URI</th>
        </tr>
        <tr>
            <td>{l s='DOI Confirmation' mod='xqmaileon'}</td>
            <td>{$webhook_doi_confirm | escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td>{l s='Unsubscription' mod='xqmaileon'}</td>
            <td>{$webhook_unsubscribe | escape:'htmlall':'UTF-8'}</td>
        </tr>
    </table>
</div>