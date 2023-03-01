{*
* The MIT License (MIT)
*
* Copyright (c) 2013-2023 XQueue GmbH
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
*  @copyright 2013-2023 XQueue
*  @license   MIT
*}

<div class="maileon">
    <h4>{l s='Newsletter' mod='xqmaileon'}</h4>
    {if $msg}
        <p class="notification {if $nw_error}notification-error{else}notification-success{/if}">
            {$msg|escape:'htmlall':'UTF-8'}</p>
    {else}
        <p>{l s=$text_a mod='xqmaileon'}</p>
        <form action="#" method="post">
            <input type="email" name="email" value="{$value|escape:'htmlall':'UTF-8'}"
                placeholder="{l s='Your e-mail' mod='xqmaileon'}" required />
            <button type="submit" value="ok" name="submitNewsletter">{l s='Sign up' mod='xqmaileon'}</button>
        </form>
        <p>{l s=$text_b mod='xqmaileon'}</p>
    {/if}
</div>