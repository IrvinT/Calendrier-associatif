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
			{capture name=path}{l s='Quotation created' mod='opartdevis'}{/capture}
			<h1>{l s='Quote created' mod='opartdevis'}</h1>
			<p class="success">{l s='Your quote has been successfully created.' mod='opartdevis'}</p>
			<p><a href="{$link->getModuleLink('opartdevis', 'showpdf',['idCart'=>$id_cart])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-medium"><span>{l s='Click here to download your quote in PDF format' mod='opartdevis'}<i class="icon-chevron-right right"></i></span></a></p>
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