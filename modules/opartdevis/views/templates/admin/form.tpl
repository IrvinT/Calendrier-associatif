{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{if isset($erreurs) && count($erreurs)>0}
<div class="alert alert-warning">
    {foreach from=$erreurs item=erreur}{$erreur|escape:'javascript':'UTF-8'}<br />{/foreach}
</div>
{/if}
<form action="{$adminModuleUrl|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" class="defaultForm form-horizontal">
    <div class="panel" id="fieldset_0">
        <div class="panel-heading"><i class="icon-cogs"></i> {l s='Configure opart devis' mod='opartdevis'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" for="sendMailtoCustomer" >{l s='Send mail to customer' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <input type="checkbox" id="sendMailtoCustomer" name="sendMailtoCustomer" value="1" {if isset($fieldsValue.sendMailtoCustomer) && $fieldsValue.sendMailtoCustomer==1}checked="checked"{/if}>
                           <p class="help-block">{l s='Send an email to customer with quotation pdf' mod='opartdevis'}</p>
                </div>
                <label class="control-label col-lg-3" for="sendMailtoAdmin">{l s='Send mail to admin' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <input type="checkbox" id="sendMailtoAdmin" name="sendMailtoAdmin"  value="1" {if isset($fieldsValue.sendMailtoAdmin) && $fieldsValue.sendMailtoAdmin==1}checked="checked"{/if}>				
                           <p class="help-block">{l s='Send an email to admin with quotation pdf' mod='opartdevis'}</p>		
                </div>
                <label class="control-label col-lg-3" for="adminMail">{l s='Choose admin contact' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <select name="adminContactId">
                        {foreach $contacts as $contact}
                        <option value="{$contact.id_contact|escape:'htmlall':'UTF-8'}" {if isset($fieldsValue.adminContactId) && $fieldsValue.adminContactId==$contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'htmlall':'UTF-8'} ({$contact.email|escape:'htmlall':'UTF-8'})</option>
                        {/foreach}
                    </select>
                    <p class="help-block">{l s='This contact will be used to receive quotations and customers messages' mod='opartdevis'}</p>
                </div>
                <label class="control-label col-lg-3" for="freeText">{l s='Free text' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    {if isset($fieldsValue.freeText)}{assign "freeTextValue" $fieldsValue.freeText}{else}{assign "freeTextValue" ""}{/if}
                    {include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='freeText'
				input_value=$freeTextValue}
                    <p class="help-block">{l s='This text will appear on the pdf file before the validation text' mod='opartdevis'}</p>	
                </div>
                
                <label class="control-label col-lg-3" for="validationText">{l s='Validation text' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    {if isset($fieldsValue.validationText)}{assign "validationTextValue" $fieldsValue.validationText}{else}{assign "validationTextValue" ""}{/if}
                    {include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='validationText'
				input_value=$validationTextValue}
                    <p class="help-block">
                        {l s='Enter here the validation condition of your quotation' mod='opartdevis'}.
                        {l s='This text will appear at the bottom of the pdf file' mod='opartdevis'}<br />
                        {l s='Ex:' mod='opartdevis'} {l s='To validate your order, you just need to send us back the quote signed to the following address:' mod='opartdevis'} {l s='company name' mod='opartdevis'} - {l s='address' mod='opartdevis'} - {l s='postcode' mod='opartdevis'} - {l s='city' mod='opartdevis'}
                    </p>		
                </div>
                
                <label class="control-label col-lg-3" for="goodforagrementText">{l s='Good for agrement text' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    {if isset($fieldsValue.goodforagrementText)}{assign "goodforagrementTextValue" $fieldsValue.goodforagrementText}{else}{assign "goodforagrementTextValue" ""}{/if}
                    {include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='goodforagrementText'
				input_value=$goodforagrementTextValue}
                    <p class="help-block">
                        {l s='Enter here the text good for agrement or another text' mod='opartdevis'}.
                    </p>		
                </div>
                
                <label class="control-label col-lg-3" for="maxProdFirstPage" >{l s='Maximum product on first page' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <input type="text" id="maxProdFirstPage" name="maxProdFirstPage" value="{if isset($fieldsValue.maxProdFirstPage)}{$fieldsValue.maxProdFirstPage|escape:'htmlall':'UTF-8'}{else}6{/if}" />
                    <p class="help-block">{l s='Set here the maximum number of product will be displaying on the first pdf page.' mod='opartdevis'}</p>
                </div>
                
                <label class="control-label col-lg-3" for="maxProdPage" >{l s='Maximum product on others pages' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <input type="text" id="maxProdPage" name="maxProdPage" value="{if isset($fieldsValue.maxProdPage)}{$fieldsValue.maxProdPage|escape:'htmlall':'UTF-8'}{else}10{/if}" />
                    <p class="help-block">{l s='Set here the maximum number of product will be displaying on pdf pages except first page.' mod='opartdevis'}</p>
                </div>
                
                <label class="control-label col-lg-3" for="expireTime" >{l s='Quotation are valid for' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <input type="text" id="maxProdPage" name="expireTime" value="{$fieldsValue.expireTime|escape:'htmlall':'UTF-8'}" />
                    <p class="help-block">{l s='Set here the maximum number of day during wich quotes are valid. 0 to disable this feature' mod='opartdevis'}</p>
                </div>
                
                <label class="control-label col-lg-3" for="showFreeForm" >{l s='Display free form' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <select name="showFreeForm" id="showFreeForm">
                        <option value="1" {if isset($fieldsValue.showFreeForm) && $fieldsValue.showFreeForm==1}selected="selected"{/if}>{l s='yes' mod='opartdevis'}</option>
                        <option value="0" {if isset($fieldsValue.showFreeForm) && $fieldsValue.showFreeForm==0}selected="selected"{/if}>{l s='no' mod='opartdevis'}</option>
                    </select>
                    <p class="help-block">{l s='Set yes if you wan\'t that your customers be able to fill the free form' mod='opartdevis'}</p>
                </div>
                
                <label class="control-label col-lg-3" for="showAccountBtn" >{l s='The \'My quotation\' button is always displayed' mod='opartdevis'} :</label>
                <div class="col-lg-9">
                    <select name="showAccountBtn" id="showFreeForm">
                        <option value="1" {if isset($fieldsValue.showAccountBtn) && $fieldsValue.showAccountBtn==1}selected="selected"{/if}>{l s='yes' mod='opartdevis'}</option>
                        <option value="0" {if isset($fieldsValue.showAccountBtn) && $fieldsValue.showAccountBtn==0}selected="selected"{/if}>{l s='no' mod='opartdevis'}</option>
                    </select>
                    <p class="help-block">{l s='Set no if you wan\'t hide the my quotation bouton when customer don\'t have any quotation in his account' mod='opartdevis'}</p>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="module_form_submit_btn" name="submitSettings" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='opartdevis'}
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
   hideOtherLanguage({$defaultLangId|escape:'htmlall':'UTF-8'});
</script>