{*
* 2007-2016 Geodis
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author Geodis
*  @copyright  2007-2016 Geodis 
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}
{if !empty($geodis_france_express_information)}
<h3>{l s='Detail delivery Mode chosen:' mod='geodisfranceexpress'}{$carrier->name|escape:'htmlall':'UTF-8'}</h3>
<div id="order-detail-content" class="table_block table-responsive">
<table class="table table-bordered {if $version < 1.6} std {/if}">
        <thead>
            <tr>
                {if (isset($geodis_france_express_information['phone']) && $geodis_france_express_information['phone'] != NULL)}  <th class="item">{l s='Phone' mod='geodisfranceexpress'}</th>{/if}
                {if (isset($geodis_france_express_information['mobile']) && $geodis_france_express_information['mobile'] != NULL)}<th class="item">{l s='Mobile phone' mod='geodisfranceexpress'}</th>{/if}
                {if (isset($geodis_france_express_information['email'])  && $geodis_france_express_information['email'] != NULL)}<th class="last_item">{l s='Email' mod='geodisfranceexpress'}</th>{/if}
            </tr>
        </thead>
        <tbody>
            <tr class="item">
               {if (isset($geodis_france_express_information['phone']) && $geodis_france_express_information['phone'] != NULL) }<td>{$geodis_france_express_information['phone']|escape:'htmlall':'UTF-8'}</td>{/if}
               {if (isset($geodis_france_express_information['mobile']) && $geodis_france_express_information['mobile'] != NULL)}<td>{$geodis_france_express_information['mobile']|escape:'htmlall':'UTF-8'}</td>{/if}
               {if (isset($geodis_france_express_information['email'])  && $geodis_france_express_information['email'] != NULL)}<td>{$geodis_france_express_information['email']|escape:'htmlall':'UTF-8'}</td>{/if}
            </tr>
        </tbody>
    </table>
</div>
{/if}
