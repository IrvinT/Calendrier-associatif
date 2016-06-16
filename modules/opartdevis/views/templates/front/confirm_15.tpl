{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{capture name=path}{l s='Quotation created' mod='opartdevis'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

			<h1>{l s='Quotation created' mod='opartdevis'}</h1>
			<p class="success">{l s='Your quotation as been created successfully' mod='opartdevis'}</p>
			<p><a href="{$link->getModuleLink('opartdevis', 'showpdf',['idCart'=>$id_cart])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-medium">{l s='Click here to download your quotation in pdf' mod='opartdevis'}</a></p>

<ul class="footer_links clearfix">
	<li><a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="" class="icon" /> {l s='Back to Your Account' mod='opartdevis'}</a></li>
	<li class="f_right"><a href="{$base_dir|escape:'htmlall':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/home.gif" alt="" class="icon" /> {l s='Home' mod='opartdevis'}</a></li>
</ul>
