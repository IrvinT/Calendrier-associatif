{if $status == 'ok'}
	<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='cheque'}</p>
    <div class="box order-confirmation">
    	<h3 class="page-subheading">{l s='Your check must include:' mod='cheque'}</h3>
		{* - {l s='Payment amount.' mod='cheque'} <span class="price"><strong>{$total_to_pay}</strong></span> *}
    	
    	<div class="row">
			<div class="col-xs-10 col-xs-offset-1 col-sm-5">
				<p>
				  	- Un chèque d'acompte d'un montant de
					<span class="price"><strong>
						<br/>{$cheque1}
					</strong></span>
				  	<br>
				  	Ce chèque sera encaissé à réception.
				</p>
			</div>
			<div class="col-xs-10 col-xs-offset-1 col-sm-offset-0 col-sm-5">
				<p>
				    - Un chèque du solde d'un montant de 
				 	<span class="price"><strong>
				 		{$cheque2}
				 	</strong></span>
				 	<br/>
				    Ce chèque sera encaissé après livraison de vos 	calendriers.
				  </p>
			</div>
		</div>
    	<div class="row">
			<div class="col-xs-10 col-xs-offset-1 col-sm-5">
				<p>
					- {l s='Payable to the order of' mod='cheque'}
					<br />
					<strong>{if $chequeName}{$chequeName}{else}___________{/if}</strong>
					<br />
					<br />
					- {l s='Mail to' mod='cheque'}
					<br />
					<strong>{if $chequeAddress}{$chequeAddress}{else}___________{/if}</strong>
				</p>
			</div>
			<div class="col-xs-10 col-xs-offset-1 col-sm-offset-0 col-sm-5">
				<p>
					{if !isset($reference)}
						<br />- {l s='Do not forget to insert your order number #%d.' sprintf=$id_order mod='cheque'}
					{else}
						<br />- {l s='Do not forget to insert your order reference %s.' sprintf=$reference mod='cheque'}
					{/if}
					<br />
					- {l s='An email has been sent to you with this information.' mod='cheque'}
					<br />
					- <strong>{l s='Your order will be sent as soon as we receive your payment.' mod='cheque'}</strong>
					<br />
					- {l s='For any questions or for further information, please contact our' mod='cheque'} 
					<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='customer service department.' mod='cheque'}">{l s='customer service department.' mod='cheque'}</a>.
				</p>
			</div>
		</div>

	</div>
{else}
	<p class="alert alert-warning">
		{l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='cheque'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='customer service department.' mod='cheque'}">{l s='customer service department.' mod='cheque'}</a>.
	</p>
{/if}
