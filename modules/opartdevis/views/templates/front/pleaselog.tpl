{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
<div class="content">
    <div class="row">
        <section id="center_column" class="span9">
            {capture name=path}{l s='Create your quotation' mod='opartdevis'}{/capture}
            <h1>{l s='Create your quotation' mod='opartdevis'}</h1>
            <p>
                <strong>{l s='You have to be logged before create your quotation' mod='opartdevis'}</strong>
                <a href="{$link->getPageLink('authentication', true, null, "&back={$back}")|escape:'htmlall':'UTF-8':'UTF-8'}" class="btn btn-default button button-small">
                    <span>{l s='Go to login page' mod='opartdevis'} <i class="icon-chevron-right"></i> </span>
                </a>
            </p>
            {if $OPARTDEVIS_SHOWFREEFORM == 1}
            <br />
            <p>
                {l s='You can also request a quoation without be logged using this form' mod='opartdevis'}
                <a href="{$link->getModuleLink('opartdevis', 'sendmessage',[])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                    <span>{l s='Use simple request form' mod='opartdevis'} <i class="icon-chevron-right"></i> </span>
                </a>
            </p>
            {/if}
        </section>
    </div>								
</div>