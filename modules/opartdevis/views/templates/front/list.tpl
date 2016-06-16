{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
<div class="content">
	<div class="row">
		<section id="center_column" class="span12">		
			{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='opartdevis'}</a><span class="navigation-pipe">{$navigationPipe|escape:'javascript':'UTF-8'}</span>{l s='Quotes' mod='opartdevis'}{/capture}
			{include file="$tpl_dir./errors.tpl"}
			
			<h1>{l s='Quotes' mod='opartdevis'}</h1>
			{if isset($deleted) && $deleted=="success"}
				<div class="alert alert-success">{l s='Quote deleted' mod='opartdevis'}</div>
			{/if}
			<div class="block-center" id="block-history">
				{if $quotations && count($quotations)}
				<table id="order-list" class="std">
                                    <thead>
                                        <tr>
                                            <th class="item">{l s='Number' mod='opartdevis'}</th>
                                            <th class="item">{l s='Date' mod='opartdevis'}</th>
                                        {if isset($expiretime) && $expiretime > 0} 
                                            <th class="item">{l s='Expired date' mod='opartdevis'}</th>
                                        {/if}
                                            <th class="item">{l s='Name' mod='opartdevis'}</th>
                                            <th class="item">&nbsp;</th>
                                            <th class="last_item">&nbsp;</th>
                                            <th class="last_item">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {foreach from=$quotations item=quotation name=myLoop}
                                        <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                                            <td class="history_method">{$quotation.id_opartdevis|escape:'htmlall':'UTF-8'}</td>
                                            <td class="history_method"><span class="price">{dateFormat date=$quotation.date_add full=1}</span></td>
                                        {if isset($quotation.expire_date) && $quotation.expire_date > 0} 
                                            <td class="history_method"><span class="price">{dateFormat date=$quotation.expire_date full=1}</span></td>
                                        {/if}
                                            <td class="history_method">{$quotation.name|escape:'htmlall':'UTF-8'}</td>
                                            <td class="history_method">       
                                                    {if $quotation.statut == 1}
                                                    <a href="{$link->getModuleLink('opartdevis','load',['opartquotationId'=>$quotation.id_opartdevis,'proceedCheckout'=>true])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                                                        <span>{l s='proceed to checkout' mod='opartdevis'}<i class="icon-chevron-right right"></i></span>
                                                    </a>
                                                    {else if $quotation.statut == 2}
                                                    <a href="{$link->getPageLink('order-detail', true, NULL, "id_order={$quotation.id_order|intval}")|escape:'html':'UTF-8'}" class="btn btn-default button button-small">
                                                        <span>{l s='Display order' mod='opartdevis'}<i class="icon-chevron-right right"></i></span>
                                                    </a>
                                                    {else if $quotation.statut == 3}
                                                        {l s='Expired' mod='opartdevis'}
                                                    {else}
                                                    <a href="{$link->getModuleLink('opartdevis','load',['opartquotationId'=>$quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                                                        <span>{l s='Modify' mod='opartdevis'}<i class="icon-chevron-right right"></i></span>
                                                    </a>
                                                    {/if}
                                            </td>
                                            <td class="history_method">
                                                {if $quotation.statut == 0 || $quotation.statut == 3}
                                                <a href="{$link->getModuleLink('opartdevis','list',['action'=>'delete','opartquotationId'=>$quotation.id_opartdevis])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small"><span>{l s='Delete' mod='opartdevis'} <i class="icon-trash right"></i></span></a>
                                                {/if}
                                            </td>
                                            <td class="history_method"><a href="{$link->getModuleLink('opartdevis','showpdf',['idCart'=>$quotation.id_cart])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small"><span>{l s='Download' mod='opartdevis'} <i class="icon-download-alt right"></i></span></a></td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
				</table>
				<div id="block-order-detail" class="hidden">&nbsp;</div>
				{else}
					<p class="warning">{l s='You have no quote' mod='opartdevis'}</p>
				{/if}
			</div>
			<ul class="footer_links clearfix">
				<li>
					<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
						<span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='opartdevis'}</span>
					</a>
				</li>
				<li class="f_right">
					<a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
						<span><i class="icon-chevron-left"></i> {l s='Home' mod='opartdevis'}</span>
					</a>
				</li>
			</ul>
		</section>
	</div>
</div>