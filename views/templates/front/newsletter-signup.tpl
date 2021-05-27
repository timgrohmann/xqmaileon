<div class="maileon">
    <h4>{l s='Newsletter' mod='xqmaileon'}</h4>
    {if $msg}
        <p class="notification {if $nw_error}notification-error{else}notification-success{/if}">{$msg}</p>
    {else}
        <form action="#" method="post">
            <input type="email" name="email" value="{$value}" placeholder="{l s='Your e-mail' mod='xqmaileon'}" required />
            <button type="submit" value="ok" name="submitNewsletter">{l s='Sign up' mod='xqmaileon'}</button>
        </form>
    {/if}
</div>