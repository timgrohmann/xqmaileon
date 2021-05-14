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
    <h3><i class="material-icons mi-input">info</i> {l s='API-Anbindung testen' mod='xqmaileon'}</h3>
    <p>
        {l s='Hier können Sie überprüfen, ob die angegebene Konfiguaration funktioniert.' mod='xqmaileon'}<br />
    </p>

    <div class="center">
        {if $api_success}
            <span class="material-icons green">
                done
            </span>
            Der eingegebene API-Schlüssel funktioniert!
        {else}
            <span class="material-icons red">
                close
            </span>
			Der eingegebene API-Schlüssel funktioniert nicht. Bitte überprüfen Sie diesen erneut in Ihrem Maileon Backend.
        {/if}
    </div>

</div>

<div class="panel">
    <h3><i class="material-icons mi-input">timer</i> {l s='Cron-Job für Warenkorbabbrecher einrichten' mod='xqmaileon'}</h3>
    <p>
        Um automatisch nach Warenkorbabbrechern zu suchen, richten Sie in ihrem System einen Cronjob ein, der folgende URL abruft:
    </p>
    <p class="center">
        {$cron_token}
    </p>
</div>

<div class="panel">
    <h3><i class="material-icons mi-input">world</i> {l s='Webhooks einrichten' mod='xqmaileon'}</h3>
    <p>
        Damit Maileon Informationen an Prestashop senden kann, richten Sie bitte in Maileon folgende Webhooks ein.
    </p>
    <table class="table">
        <tr>
            <th>Typ</th>
            <th>Webhook-URI</th>
        </tr>
        <tr>
            <td>DOI-Bestätigung</td>
            <td>{$webhook_doi_confirm}</td>
        </tr>
        <tr>
            <td>Abmeldung</td>
            <td>{$webhook_unsubscribe}</td>
        </tr>
    </table>
</div>