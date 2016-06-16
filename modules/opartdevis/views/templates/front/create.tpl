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
            {include file="$tpl_dir./errors.tpl"}
            {if isset($cartEmpty) && $cartEmpty==true}
            <p>{l s='Your cart is empty, please add product into your cart before creating your quotation.' mod='opartdevis'}</p>
            {/if}
            {if $showForm}        
            <form action="{$link->getModuleLink('opartdevis', 'createquotation')|escape:'htmlall':'UTF-8'}" method="post" class="contact-form-box" enctype="multipart/form-data" id="opartDevisForm">
                <input type="hidden" name="idCart" value="{$id_cart|escape:'htmlall':'UTF-8'}" />
                <input type="hidden" name="quotationId" value="{$quotationId|escape:'htmlall':'UTF-8'}" />
                <fieldset>
                    <h4>{l s='Products in your quotation' mod='opartdevis'}</h4>
                    <table class="table table-bordered stock-management-on" id="cart_summary">
                        <thead>
                        <tr>
                            <th>{l s='Product' mod='opartdevis'}</th>
                            <th>{l s='Availability' mod='opartdevis'}</th>
                            <th>{l s='Qty' mod='opartdevis'}</th>
                            <th>{l s='Unit price' mod='opartdevis'} {if $priceDisplay == 1}{l s='tax excl.' mod='opartdevis'}{else}{l s='tax incl.' mod='opartdevis'}{/if}</th>
                            <th>{l s='Total' mod='opartdevis'} {if $priceDisplay == 1}{l s='tax excl.' mod='opartdevis'}{else}{l s='tax incl.' mod='opartdevis'}{/if}</th>
                        </tr>
                        </thead>
                        {foreach $summary.products as $product}
                        <tr>
                            <td>{$product.name|escape:'htmlall':'UTF-8'}</td>
                            <td>{if $product.quantity_available>0}{$product.available_now|escape:'htmlall':'UTF-8'}{else}{$product.available_later|escape:'htmlall':'UTF-8'}{/if}</td>
                            <td>{$product.cart_quantity|escape:'htmlall':'UTF-8'}</td>
                            <td>{if $priceDisplay == 1}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_wt}{/if}</td>
                            <td>{if $priceDisplay == 1}{convertPrice price=$product.total}{else}{convertPrice price=$product.total_wt}{/if}</td>
                        </tr>
                        {/foreach}
						<!-- discount -->
						{if sizeof($summary.discounts)}
							{foreach $summary.discounts as $discount}
								<tr class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount|escape:'htmlall':'UTF-8'}"><td colspan="2">{$discount.name|escape:'htmlall':'UTF-8'}</td>										
									<td>1</td>
									<td>
										<span class="price-discount">
										{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
										</span>
									</td>
									<td class="cart_discount_price">
										<span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
									</td>
								</tr>
							{/foreach}
						{/if}
                        <tfoot>
                        <tr class="cart_total_price">
                         <td colspan="4" class="text-right">{l s='Total' mod='opartdevis'} {l s='tax excl.' mod='opartdevis'}</td>
                         <td class="price" id="opartQuotationTotalQuotation">{convertPrice price=$summary.total_price_without_tax}</td>
                        </tr>										
                        <tr class="cart_total_price">
                         <td colspan="4" class="text-right">{l s='Total shipping' mod='opartdevis'}</td>
                         <td class="price" id="opartQuotationTotalShipping">{convertPrice price=$summary.total_shipping}</td>
                        </tr>	
                        <tr class="cart_total_price">
                         <td colspan="4" class="text-right">{l s='Total tax' mod='opartdevis'}</td>
                         <td class="price" id="opartQuotationTotalTax">{convertPrice price=$summary.total_tax}</td>
                        </tr>
                        <tr class="cart_total_price">
                         <td colspan="4" class="total_price_container text-right">{l s='Total' mod='opartdevis'} {l s='tax incl.' mod='opartdevis'}</td>
                         <td class="price" id="total_price_container"><span id="opartQuotationTotalQuotationWithTax">{convertPrice price=$summary.total_price}</span></td>
                        </tr>
                        </tfoot>                        
                    </table>
                    <p class="opartDevisInfos">{l s='To edit your product list, open your cart and make your change.' mod='opartdevis'}<br />{l s='Then click again on the "create quotation" button.' mod='opartdevis'}</p>
                    <!-- addresses -->
                    {if count($addresses)>0}
                    <div class="clearfix">
                        <h4>{l s='Choose your addresses' mod='opartdevis'}</h4>
                        <div class="form-group opartDevisConteneurTextarea opartDevisConteneurTextareaLeft">
                            <label for="delivery_address"> {l s='Delivery addresse' mod='opartdevis'}</label>
                            <select name="delivery_address" {if isset($summary)}onChange="opartDevisLoadCarrierList();"{/if}>
                                {foreach $addresses as $address}
                                <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.delivery->id == $address.id_address}selected="selected"{/if}>{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>                        
                        <div class="form-group opartDevisConteneurTextarea opartDevisConteneurTextareaRight">
                            <label for="invoice_address"> {l s='Invoice addresse' mod='opartdevis'}</label>
                            <select name="invoice_address">
                                {foreach $addresses as $address}
                                <option value="{$address.id_address|escape:'htmlall':'UTF-8'}" {if isset($summary) && $summary.invoice->id == $address.id_address}selected="selected"{/if}>{$address.firstname|escape:'htmlall':'UTF-8'} {$address.lastname|escape:'htmlall':'UTF-8'} - {$address.address1|escape:'htmlall':'UTF-8'}{if $address.address2!=""} {$address.address2|escape:'htmlall':'UTF-8'}{/if} - {$address.postcode|escape:'htmlall':'UTF-8'} {$address.city|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    {else}
                    <p class="opartDevisInfos">{l s='We didn\'t found any addresses, please go to your personnal account and add addresses' mod='opartdevis'}</p>
                    {/if}
                    {if isset($summary)}
						{if $from!='payment'}
							<!-- carriers -->
							<div class="clearfix">
								<div class="form-group">
								<h4>{l s='Choose your carrier' mod='opartdevis'}</h4>                        
								<select id="opart_devis_carrier_input" name="opart_devis_carrier_input"></select>	
								</div>                        
								<input type="hidden" name="selected_carrier" value="{if isset($cart->id_carrier)}{$cart->id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
							</div>
                                                {else}
                                                    <input type="hidden" name="opart_devis_carrier_input" value="{if isset($cart->id_carrier)}{$cart->id_carrier|escape:'htmlall':'UTF-8'}{/if}" />                                                            
						{/if}
                    {/if}
                    <!-- messages -->
                    <div class="clearfix">
                        <h4>{l s='Additionnal informations' mod='opartdevis'}</h4>
                        <div class="form-group opartDevisConteneurTextarea opartDevisConteneurTextareaLeft">
                            <label for="message_visible">{l s='Add information (visible on quotation)' mod='opartdevis'}</label>
                            <textarea class="form-control" id="message" name="message_visible">{if isset($message_visible)}{$message_visible|escape:'htmlall':'UTF-8':'UTF-8'|stripslashes}{/if}</textarea>
                        </div>
                        <div class="form-group opartDevisConteneurTextarea opartDevisConteneurTextareaRight">
                            <label for="message_not_visible">{l s='Leave us a message (not visible on quotation)' mod='opartdevis'}</label>
                            <textarea class="form-control" id="message" name="message_not_visible">{if isset($message_not_visible)}{$message_not_visible|escape:'htmlall':'UTF-8':'UTF-8'|stripslashes}{/if}</textarea>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="form-group opartDevisConteneurQuotationName">
                            <label for="quotation_name">{l s='Add a name to your quotation' mod='opartdevis'}</label>
                            <input type="text" name="quotation_name" id="quotation_name" value="{$quotationName|escape:'htmlall':'UTF-8'}"/>
                        </div>
                    </div>
                        
                    <p class="cart_navigation clearfix">
                        <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                            <span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='opartdevis'}</span>
                        </a>
                        <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small">
                            <span><i class="icon-chevron-left"></i> {l s='Home' mod='opartdevis'}</span>
                        </a>
                        <button type="submit" name="submitQuotation" id="submitMessage" class="button btn btn-default button-medium"><span><i class="icon-save"></i> {l s='Save and send your quotation' mod='opartdevis'}</span></button>
                    </p>
                </fieldset>                
            </form>         
            {/if}
        </section>
    </div>
</div>
<script type="text/javascript">
	/*var opart_module_dir = "{$ps_base_url|escape:'htmlall':'UTF-8'}{$opart_module_dir|escape:'htmlall':'UTF-8'}";  
        opartDevisControllerUrl = "{$ps_base_url|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=opartdevis&controller=createquotation";*/
    	/*var opart_module_dir = "{$base_dir|escape:'htmlall':'UTF-8'}{$opart_module_dir|escape:'htmlall':'UTF-8'}";  
	opartDevisControllerUrl = "{$base_dir|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=opartdevis&controller=createquotation";*/
    
        var opart_module_dir = "{$content_dir|escape:'htmlall':'UTF-8'}{$opart_module_dir|escape:'htmlall':'UTF-8'}";  
	opartDevisControllerUrl = "{$content_dir|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=opartdevis&controller=createquotation";
    
    opartDevisControllerUrl
        currency_format = {$currency->format|escape:'htmlall':'UTF-8'};
	currency_sign = '{$currency->sign|escape:'htmlall':'UTF-8'}';
	currency_blank = {$currency->blank|escape:'htmlall':'UTF-8'};
        $(document).ready(function() {
    opartDevisLoadCarrierList();
        $('#opart_devis_carrier_input').change(function() {
             OpartDevisChangeCarrier();
        });
    })
</script>