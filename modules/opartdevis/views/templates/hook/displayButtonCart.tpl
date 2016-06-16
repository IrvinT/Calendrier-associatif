{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
<a href="{$link->getModuleLink('opartdevis', 'createquotation',['create'=>true])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small opartDevisCartToQuotationLink"><span>
{if !$quote}{l s='create quotation from my cart' mod='opartdevis'}
{else}
{l s='update my quotation' mod='opartdevis'}
{/if}
<i class="icon-chevron-right right"></i></span></a>