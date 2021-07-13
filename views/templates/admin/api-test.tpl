{*
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
        {$cron_token|escape:'htmlall':'UTF-8' }
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
            <td>{$webhook_doi_confirm|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td>{l s='Unsubscription' mod='xqmaileon'}</td>
            <td>{$webhook_unsubscribe|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </table>
</div>