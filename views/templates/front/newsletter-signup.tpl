<div class="maileon">
    <h4>{l s='Newsletter' mod='xqmaileon'}</h4>
    {if $msg}
        <p class="notification {if $nw_error}notification-error{else}notification-success{/if}">{$msg | escape:'htmlall':'UTF-8'}</p>
    {else}
        <p>{l s=$text_a mod='xqmaileon'}</p>
        <form action="#" method="post">
            <input type="email" name="email" value="{$value | escape:'htmlall':'UTF-8'}" placeholder="{l s='Your e-mail' mod='xqmaileon'}" required />
            <button type="submit" value="ok" name="submitNewsletter">{l s='Sign up' mod='xqmaileon'}</button>
        </form>
        <p>{l s=$text_b mod='xqmaileon'}</p>
    {/if}
</div>