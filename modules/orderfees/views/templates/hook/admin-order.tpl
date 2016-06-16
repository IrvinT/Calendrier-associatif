{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
{if (count($fees) > 0 || $can_edit)}
    <script id="fees_block" type="text/template">
        <div class="panel panel-fees" style="{if !sizeof($fees)}display:none;{/if}">
        <div class="table-responsive">
            <table class="table">
                    <thead>
                            <tr>
                                    <th>
                                            <span class="title_box ">
                                                    {l s='Fee' mod='orderfees'}
                                            </span>
                                    </th>
                                    <th>
                                            <span class="title_box ">
                                                    {l s='Value' mod='orderfees'}
                                            </span>
                                    </th>
                                    {if $can_edit}
                                    <th></th>
                                    {/if}
                            </tr>
                    </thead>
                    <tbody>
                            {foreach from=$fees item=fee}
                            <tr>
                                    <td>{$fee['name']|escape:'html':'UTF-8'}</td>
                                    <td>
                                    {displayPrice price=$fee['value'] currency=$currency->id}
                                    </td>
                                    {if $can_edit}
                                    <td>
                                            <a href="{$current_index|escape:'html':'UTF-8'}&vieworder&submitDeleteFee&id_order_cart_rule={$fee['id_order_cart_rule']|intval}&id_order={$order->id|intval}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
                                                    <i class="icon-minus-sign"></i>
                                                    {l s='Delete fee' mod='orderfees'}
                                            </a>
                                    </td>
                                    {/if}
                            </tr>
                            {/foreach}
                    </tbody>
            </table>
        </div>
        <div class="current-edit" id="fee_form" style="display:none;">
                <div class="form-horizontal well">
	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Name' mod='orderfees'}
		</label>
		<div class="col-lg-9">
			<input class="form-control" type="text" name="fee_name" value="" />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Type' mod='orderfees'}
		</label>
		<div class="col-lg-9">
			<select class="form-control" name="fee_type" id="fee_type">
				<option value="1">{l s='Percent' mod='orderfees'}</option>
				<option value="2">{l s='Amount' mod='orderfees'}</option>
			</select>
		</div>
	</div>

	<div id="discount_value_field" class="form-group">
		<label class="control-label col-lg-3">
			{l s='Value' mod='orderfees'}
		</label>
		<div class="col-lg-9">
			<div class="input-group">
				<div class="input-group-addon">
					<span id="fee_currency_sign" style="display: none;">{$currency->sign|escape:'html':'UTF-8'}</span>
					<span id="fee_percent_symbol">%</span>
				</div>
				<input class="form-control" type="text" name="fee_value"/>
			</div>
			<p class="text-muted" id="fee_value_help" style="display: none;">
				{l s='This value must include taxes.' mod='orderfees'}
			</p>
		</div>
	</div>

	{if $order->hasInvoice()}
	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Invoice' mod='orderfees'}
		</label>
		<div class="col-lg-9">
			<select name="fee_invoice">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id|intval}" selected="selected">
					{$invoice->getInvoiceNumberFormatted($current_id_lang)|escape:'html':'UTF-8'} - {displayPrice price=$invoice->total_paid_tax_incl currency=$order->id_currency}
				</option>
				{/foreach}
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<p class="checkbox">
				<label class="control-label" for="fee_all_invoices">
					<input type="checkbox" name="fee_all_invoices" id="fee_all_invoices" value="1" /> 
					{l s='Apply on all invoices' mod='orderfees'}
				</label>
			</p>
			<p class="help-block">
				{l s='If you select to create this fee for all invoices, one fee will be created per order invoice.' mod='orderfees'}
			</p>
		</div>
	</div>
	{/if}

	<div class="row">
		<div class="col-lg-9 col-lg-offset-3">
			<button class="btn btn-default" type="button" id="cancel_add_fee">
				<i class="icon-remove text-danger"></i>
				{l s='Cancel' mod='orderfees'}
			</button>
			<button class="btn btn-default" type="submit" name="submitNewFee">
				<i class="icon-ok text-success"></i>
				{l s='Add' mod='orderfees'}
			</button>
		</div>
	</div>
</div>
        </div>
        </div>
    </script>
    
    <script id="fees_button" type="text/template">
        <button id="add_fee" class="btn btn-default" type="button">
                <i class="icon-folder-open-alt"></i>
                {l s='Add a fee' mod='orderfees'}
        </button>
    </script>
    {/if}

    <script type="text/javascript">
        var has_fee = {if count($fees)}1{else}0{/if};
        
        $(function() {
            // Build layout
            $('#add_voucher').after($('#fees_button').html());
            $('.panel-vouchers').after($('#fees_block').html());

            $('#add_fee').unbind('click');
            $('#add_fee').click(function() {
                    $('.order_action').hide();
                    $('.panel-fees,#fee_form').show();
                    return false;
            });

            $('#cancel_add_fee').unbind('click');
            $('#cancel_add_fee').click(function() {
                    $('#fee_form').hide();
                    if (!has_fee)
                            $('.panel-fees').hide();
                    $('.order_action').show();
                    return false;
            });

            $('#fee_type').unbind('click');
            $('#fee_type').change(function() {
                // Percent type
                if ($(this).val() == 1) {
                    $('#fee_value_field').show();
                    $('#fee_currency_sign').hide();
                    $('#fee_value_help').hide();
                    $('#fee_percent_symbol').show();
                }
                // Amount type
                else if ($(this).val() == 2) {
                    $('#fee_value_field').show();
                    $('#fee_percent_symbol').hide();
                    $('#fee_value_help').show();
                    $('#fee_currency_sign').show();
                }
            });

            $('#total_products').closest('div').next('.clear').after($('#fees-block'));
        });
    </script>