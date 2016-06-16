{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
<script type="text/javascript">
    var id_lang_default = {$id_lang_default|escape:'htmlall':'UTF-8'};
    var opart_module_dir = "{$ps_base_url|escape:'htmlall':'UTF-8'}{$opart_module_dir|escape:'htmlall':'UTF-8'}";
    var token = '{$opart_token|escape:'htmlall':'UTF-8'}';
</script>
<div id="opartDevisMsgAlwaysTop"></div>
<form action="{$href|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="opartDevisForm">	
    <input type="hidden" name="submitAddOpartDevis" value="1">
    {if isset($obj->id_opartdevis) && $obj->id_opartdevis!=""}
        <input type="hidden" value="{$obj->id_opartdevis|escape:'htmlall':'UTF-8'}" name="id_opartdevis" />
        <input type="hidden" value="{$obj->id_cart|escape:'htmlall':'UTF-8'}" name="id_cart" />
    {/if}
    <!-- name -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Quotation name' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-lg-1"><span class="pull-right"></span></div>	
                <label class="control-label col-lg-2">
                    {l s='Add a name to this quotation:' mod='opartdevis'}
                </label>
                <div class="col-lg-7">
                    <input type="text" value="{if isset($obj)}{$obj->name|escape:'htmlall':'UTF-8'}{/if}" name="quotation_name" />
                </div>
            </div>
        </div>
    </div>
    <!-- user -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Customer' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group redirect_product_options redirect_product_options_product_choise">	
                <div class="col-lg-1"><span class="pull-right"></span></div>	
                <label class="control-label col-lg-2" for="opart_devis_customer_autocomplete_input">
                    {l s='choose customer:' mod='opartdevis'}
                </label>
                <div class="col-lg-7">
                    <input type="hidden" value="" name="id_product_redirected" />
                    <div class="input-group">
                        <input type="text" id="opart_devis_customer_autocomplete_input" name="opart_devis_customer_autocomplete_input" autocomplete="off" class="ac_input" />
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <p class="help-block">{l s='Start by typing the first letters of the customer\'s firstname or lastname, then select the customer from the drop-down list.' mod='opartdevis'}</p>				
                    <h2 style="clear:both;">
                        <i class="icon-male"></i> 
                        <span href="" id="opart_devis_customer_info"><span style="color:red">{l s='Please choose a customer' mod='opartdevis'}</span></span>
                    </h2>			
                </div>
                <input type="hidden" name="opart_devis_customer_id" id="opart_devis_customer_id" value=""/>
            </div>
        </div>
    </div>
    <!--  address -->
    <div class="panel">
        <h3><i class="icon-envelope-alt"></i> {l s='Address' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>
            <label class="control-label col-lg-2" for="opart_devis_customer_autocomplete_input">
                {l s='Invoice address:' mod='opartdevis'}
            </label>
            <div class="col-lg-7">
                <select id="opart_devis_invoice_address_input" name="invoice_address"></select>	
            </div>
            <div style="clear:both; height:20px;"></div>
            <div class="col-lg-1"><span class="pull-right"></span></div>
            <label class="control-label col-lg-2" for="opart_devis_customer_autocomplete_input">
                {l s='delivery address:' mod='opartdevis'}
            </label>					
            <div class="col-lg-7">
                <select id="opart_devis_delivery_address_input" name="delivery_address"></select>				
                <p class="help-block">{l s='First, you have to choose a customer and you will be able to choose one of his addresses.' mod='opartdevis'}</p>
            </div>			
            <div style="clear:both;"></div>
            <input type="hidden" name="selected_invoice" id="selected_invoice" value="{if isset($cart->id_address_invoice)}{$cart->id_address_invoice|escape:'htmlall':'UTF-8'}{/if}" />
            <input type="hidden" name="selected_delivery" id="selected_delivery" value="{if isset($cart->id_address_delivery)}{$cart->id_address_delivery|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    </div>
    <!-- products -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Products' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='add product:' mod='opartdevis'}
            </label>
            <div class="col-lg-7">
                <input type="hidden" value="" name="id_product_redirected" />
                <div class="input-group">
                    <input type="text" id="opart_devis_product_autocomplete_input" name="opart_devis_product_autocomplete_input" autocomplete="off" class="ac_input" />
                    <span class="input-group-addon"><i class="icon-search"></i></span>					
                </div>
                <p class="help-block">{l s='Start by typing the first letters of the products\'s name, then select the product from the drop-down list.' mod='opartdevis'}</p>					
            </div>
            <div style="clear:both; height:20px;"></div>	
            <div class="col-lg-1"><span class="pull-right"></span></div>			
            <label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='products in quotation:' mod='opartdevis'}
            </label>
            <div class="col-lg-7">
                <table class="table" id="opartDevisProdList">
                    <tr>
                        <th style="width:5%">{l s='id' mod='opartdevis'}</th>
                        <th>{l s='name' mod='opartdevis'}</th>
                        <th>{l s='Attributes' mod='opartdevis'}</th>
                        <th style="width:10%">{l s='Catalog price' mod='opartdevis'}</th>
                        <!--<th style="width:10%">{l s='real price' mod='opartdevis'}</th>-->
                        <th style="width:10%">{l s='Your price' mod='opartdevis'}</th>
                        <th style="width:10%">{l s='Quantity' mod='opartdevis'}</th>
                        <th style="width:5%">&nbsp;</th>
                    </tr>	
                </table>	
            </div>
            <div style="clear:both;"></div>			
        </div>
    </div>
	<!-- discounts -->
	<div class="panel">
        <h3><i class="icon-archive"></i> {l s='Reductions' mod='opartdevis'}</h3>
		<div class="form-horizontal">
			<div class="col-lg-1"><span class="pull-right"></span></div>
			<label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='add reduction:' mod='opartdevis'}
            </label>
			<div class="col-lg-7">
                <div class="input-group">
					<select id="opart_devis_select_cart_rules">
						{if count($cart_rules)>0}
							<option value="-1">--- {l s='cart rules' mod='opartdevis'} ---</option>
							{foreach $cart_rules as $rule}
								<option value="{$rule.id_cart_rule|escape:'htmlall':'UTF-8'}">{$rule.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{else}						
							<option value="-1">--- {l s='no cart rules avaibles' mod='opartdevis'} ---</option>
						{/if}
					</select>
				</div>
			</div>
			<div style="clear:both; height:20px;"></div>	
            <div class="col-lg-1"><span class="pull-right"></span></div>			
            <label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='discount in quotation:' mod='opartdevis'}
            </label>
            <div class="col-lg-7">
                <table class="table" id="opartDevisCartRuleList">
                    <tr>
                        <th style="width:5%">{l s='id' mod='opartdevis'}</th>
                        <th>{l s='name' mod='opartdevis'}</th>
						<th>{l s='description' mod='opartdevis'}</th>
                        <th>{l s='code' mod='opartdevis'}</th>
						<th>{l s='free shipping' mod='opartdevis'}</th>
						<th>{l s='reduction percent' mod='opartdevis'}</th>
						<th>{l s='reduction amount' mod='opartdevis'}</th>
						<th>{l s='reduction type' mod='opartdevis'}</th>
						<th>{l s='gift product' mod='opartdevis'}</th>
						<th>&nbsp;</th>
                    </tr>	
                </table>	
            </div>
            <div style="clear:both;"></div>	
		</div>
	</div>
    <!-- carriers -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Carriers' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='choose carrier:' mod='opartdevis'} <a href="#" id="opart_devis_refresh_carrier_list" style="display:inline-block; vertical-align:middle;"><i class="process-icon-refresh"></i></a>	
            </label>
            <div class="col-lg-7">			
                <select id="opart_devis_carrier_input" name="opart_devis_carrier_input" onchange="$('#selected_carrier').val($(this).val())"></select>	
                <p class="help-block">{l s='First you have to choose customer, addresses and all products then click on the reload button and you will be able to choose a carrier.' mod='opartdevis'}</p>				
            </div>
            <div style="clear:both;"></div>
            <input type="hidden" name="selected_carrier" value="{if isset($cart->id_carrier)}{$cart->id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
        </div>
    </div>
    <!-- upload file-->
    <div class="panel">
        <h3><i class="icon-upload-alt"></i> {l s='upload files attachment for mail' mod='opartdevis'}</h3>
        <div class="form-horizontal clearfix">
            <center>{l s='You can choose several files, pressing the CTRL key (size max:5MB)' mod='opartdevis'}</center>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
             <br><center><input id="file-name" type="file" name="fileopartdevis[]" multiple enctype="multipart/form-data"></center>
            {if (is_dir($pathuploadfiles)) AND ($dir_flag neq false)}
                {assign var= file value= opendir($pathuploadfiles)}
                {while $files = readdir($file)}
                    {if $files != '.' AND $files != '..'}
                        <br>
                        <div>
                            <center>
                                <a href="{$view_flag|escape:'htmlall':'UTF-8'}opartdevis/uploadfiles/{$dir_flag|escape:'htmlall':'UTF-8'}/{$files|escape:'htmlall':'UTF-8'}" target="_blank">{$files|escape:'htmlall':'UTF-8'}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <button type="button" class="upload_attachement" data-name="{$files|escape:'htmlall':'UTF-8'}" data-id="{$dir_flag|escape:'htmlall':'UTF-8'}" style="background: transparent; border: 0px; padding: 0px; opacity:0.2px; -webkit-appearance: none;" data-dismiss="alert">×</button>
                            </center>
                        </div>
                    {/if}
                {/while}
                {closedir($file)}{* HTML needed can't escape *}
            {/if}
        </div>
    </div>
    <!-- additional information -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Additional informations' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" for="opart_devis_product_autocomplete_input">
                {l s='Message:' mod='opartdevis'}
            </label>
            <div class="col-lg-7">			
                <textarea name="message_visible">{if isset($obj->message_visible) && $obj->message_visible!=""}{$obj->message_visible|escape:'htmlall':'UTF-8'}{/if}</textarea>	
                <p class="help-block">{l s='Visible on quotation.' mod='opartdevis'}</p>						
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <!-- TOTAL -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Total' mod='opartdevis'}</h3>
        <div class="form-horizontal">
			<!-- total without tax -->
			<div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total quotation:' mod='opartdevis'} = 
            </label>
            <div class="col-lg-7"><span id="totalQuotation"></span></div>            
			<div style="clear:both;"></div>
			
			<!-- total tax -->
			<div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total tax' mod='opartdevis'} = 
            </label>
            <div class="col-lg-7"><span id="totalTax"></span></div>            
			<div style="clear:both;"></div>
			
			<!-- total discounts -->
			<div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total discounts' mod='opartdevis'} = 
            </label>
            <div class="col-lg-7"><span id="totalDiscounts"></span></div>            
			<div style="clear:both;"></div>
			
			<!-- total shipping -->
			<div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total shipping' mod='opartdevis'} = 
            </label>
            <div class="col-lg-7"><span id="totalShipping"></span></div>            
			<div style="clear:both;"></div>
			
			<!-- total with tax -->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0; font-size:1.5em;">
                {l s='Total quotation with tax:' mod='opartdevis'} = 
            </label>
            <span id="totalQuotationWithTax" style="color:red; font-weight:bold; font-size:1.5em;"></span>     
			<div style="clear:both;"></div>
			<div class="col-lg-1"><span class="pull-right"></span></div>	
			<label class="control-label col-lg-2">
				<a href="#" id="opart_devis_refresh_total_quotation" style="display:inline-block; vertical-align:middle;"><i class="process-icon-refresh"></i></a>{l s='Refesh total' mod='opartdevis'}
			</label>
			<div style="clear:both;"></div>
        </div>
    </div>
    <div style="clear:both";></div>
    <div class="panel">
        <div class="panel-footer">
            <a href="{$hrefCancel|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='cancel' mod='opartdevis'}</a>
            <button type="submit" name="submitAddOpartDevis" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='save' mod='opartdevis'}</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    id_lang_default = {$id_lang_default|escape:'htmlall':'UTF-8'};
    specific_price_txt = "{l s='Specific price'  mod='opartdevis'}";
    from_qty_text = "{l s='from'  mod='opartdevis'}";
    qty_text = "{l s='quantity'  mod='opartdevis'}";
    opartDevisControllerUrl = 'index.php?controller=AdminOpartDevis&token={$opart_token|escape:'htmlall':'UTF-8'}';
    opartDevisMsgQuoteSaved = "{l s='Your quote has been saved' mod='opartdevis'}";
    //urlLoadCarrier = 'index.php?controller=AdminOpartDevis&ajax_carrier_list&token={$opart_token|escape:'htmlall':'UTF-8'}';
    {if $customer!=null}
        OpartDevisAddCustomerToQuotation({$customer->id|escape:'htmlall':'UTF-8'},'{$customer->firstname|escape:'htmlall':'UTF-8'}','{$customer->lastname|escape:'htmlall':'UTF-8'}');
    {/if}
    {if $cart!=null}
        {foreach $products AS $product}
            OpartDevisAddProductToQuotation({$product.id_product|escape:'htmlall':'UTF-8'},'{$product.name|escape:'htmlall':'UTF-8'}','{$product.catalogue_price|escape:'htmlall':'UTF-8'}',{$product.cart_quantity|escape:'htmlall':'UTF-8'},{$product.id_product_attribute|escape:'htmlall':'UTF-8'},'{$product.specific_price|escape:'htmlall':'UTF-8'}','{$product.specific_qty|escape:'htmlall':'UTF-8'}');
        {/foreach}
    {/if}
	{if $cart!=null && !empty($summary.discounts)}		
		{foreach $summary.discounts AS $rule}
			{if $rule.reduction_product==-2}
				reduction_type = "{l s='selected product' mod='opartdevis'}"
			{else if $rule.reduction_product==-1}
				reduction_type = "{l s='cheapest product' mod='opartdevis'}"
			{else if $rule.reduction_product==0}
				reduction_type = "{l s='order' mod='opartdevis'}"	
			{else}
				reduction_type = "{l s='specific product' mod='opartdevis'} ({$rule.reduction_product})"{/if}
					
			OpartDevisAddRuleToQuotation({$rule.id_cart_rule|escape:'htmlall':'UTF-8'},'{$rule.name|escape:'htmlall':'UTF-8'}','{$rule.description|escape:'htmlall':'UTF-8'}','{$rule.code|escape:'htmlall':'UTF-8'}',{$rule.free_shipping|escape:'htmlall':'UTF-8'},'{$rule.reduction_percent|escape:'htmlall':'UTF-8'}','{$rule.reduction_amount|escape:'htmlall':'UTF-8'}',reduction_type,{$rule.gift_product|escape:'htmlall':'UTF-8'});
		{/foreach}
	{/if}

</script>
<!--
<pre>
    {*{$cart|print_r}*}

{foreach $products AS $product}

            {$product|print_r}{/foreach}
</pre>
-->