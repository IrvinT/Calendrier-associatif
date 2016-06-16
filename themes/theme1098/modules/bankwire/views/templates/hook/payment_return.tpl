{if $status == 'ok'}
	<div class="box">
		<h2 class="cheque-indent">
			<strong class="dark">{l s='Your order on %s is complete.' sprintf=$shop_name mod='bankwire'}</strong>
		</h2>
		<br />
		<h3>{l s='Please send us a bank wire with' mod='bankwire'}</h3>
		<br />
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<p>
					- {l s='Amount' mod='bankwire'} 
					<span class="price"> <strong>{$total_to_pay}</strong></span>
					<br />
					<br />
					- {l s='Name of account owner' mod='bankwire'} 
					<strong>{if $bankwireOwner}{$bankwireOwner}{else}___________{/if}</strong>
					<br/>
					<br/>
					- {l s='Include these details' mod='bankwire'}
					<br/>
					<strong>{if $bankwireDetails}{$bankwireDetails}{else}___________{/if}</strong>
				</p>
			</div>
			<div class="col-xs-12 col-sm-6">
				<p>
					- {l s='Bank name' mod='bankwire'}  
					<br/>
					<strong>{if $bankwireAddress}{$bankwireAddress}{else}___________{/if}</strong>
				</p>
			</div>
		</div>
		<p>
			<small>
				{if !isset($reference)}
					<br />
					- {l s='Do not forget to insert your order number #%d in the subject of your bank wire' sprintf=$id_order mod='bankwire'}
				{else}
					<br />
					- {l s='Do not forget to insert your order reference %s in the subject of your bank wire.' sprintf=$reference mod='bankwire'}
				{/if}		
				<br />
				{l s='An email has been sent with this information.' mod='bankwire'}
				<br /> 
				<strong>{l s='Your order will be sent as soon as we receive payment.' mod='bankwire'}</strong>
				<br />
				{l s='If you have questions, comments or concerns, please contact our' mod='bankwire'} 
			</small>
			<strong>
				<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='expert customer support team. ' mod='bankwire'}">{l s='expert customer support team. ' mod='bankwire'}</a>.
			</strong>
		</p>
	</div>
{else}
	<p class="alert alert-warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='bankwire'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='expert customer support team. ' mod='bankwire'}">{l s='expert customer support team. ' mod='bankwire'}</a>.
	</p>
{/if}
