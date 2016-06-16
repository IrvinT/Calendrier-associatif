{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Geodis
 *  @copyright 2010-2016 Geodis
 *}

<fieldset>
<legend>{l s='FRANCE EXPRESS module configuration' mod='geodisfranceexpress'}</legend>

{if (!$display_alert)}
    <img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/module_install.png" /><strong>{l s='FRANCE EXPRESS module is configured' mod='geodisfranceexpress'}</strong>

{else}
    <img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/warn2.png" /><strong>{l s='My Carrier is not configured yet, please:' mod='geodisfranceexpress'}</strong>
    <br />
    {if isset($alert['carrier1'])} 
    	<img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/warn2.png" />{l s='Configure the carrier 1 overcost' mod='geodisfranceexpress'}<br />
    {/if}
    {if isset($alert['carrier2'])} 
    	<img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/warn2.png" />{l s='Configure the carrier 2 overcost' mod='geodisfranceexpress'}<br />
    {/if}
{/if}

<br /> Ci dessous la liste des <strong>régles de gestion</strong> à respecter pour <em>configurer les methodes FRANCE EXPRESS</em>.
<br /><br />Pays d'expédition de la méthode 1 (origin) : <span class="notice">France, Monaco</span>
<br />Pays d'expédition de la méthode 2 (origin) : <span class="notice">France, Monaco, Belgique</span>
<br /><br />Pays de destination de la méthode 1 : <span class="notice">France, Monaco</span>
<br />Pays de destination de la méthode 2 : <span class="notice">France, Monaco, Belgique, Luxembourg</span>
<br /><br />Poids Max de la commande : <span class="notice">1 tonne</span>
<br /><br />Longueur Max du colis : <span class="notice">3 mètres</span>
<br /><br />Hauteur Max du colis : <span class="notice">2 mètres</span><br />

</fieldset><div class="clear">&nbsp;</div>

<style>
	{literal}
    #tabList { clear: left; }
    .tabItem { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
    .notice {color:#8bc954}
    {/literal}
</style>

<div id="tabList">
    <div class="tabItem">
        <form action="index.php?tab={$tab|escape:'htmlall':'UTF-8'}&configure={$configure|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&tab_module={$tab_module|escape:'htmlall':'UTF-8'}&module_name={$module_name|escape:'htmlall':'UTF-8'}&id_tab=1&section=general" method="post" class="form" id="configForm">
        
        <fieldset style="border: 0px;">
            <legend>{l s='Delivery Method 1 : ' mod='geodisfranceexpress'}{$geodis_method1_name|escape:'htmlall':'UTF-8'}</legend>
            <label>{l s='Active' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <input type="radio" name="carrier1_activated" id="activated_on" value="1" {if ($carrier1_activated)}  checked="checked"{/if} />
                <label class="t" for="ajax_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='geodisfranceexpress'}" title="{l s='Enabled' mod='geodisfranceexpress'}" /></label>
                <input type="radio" name="carrier1_activated" id="activated_off" value="0" {if (!$carrier1_activated)}  checked="checked"{/if} />
                <label class="t" for="ajax_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='geodisfranceexpress'}" title="{l s='Disabled' mod='geodisfranceexpress'}" /></label>
            </div>

            <label>{l s='Description' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <textarea rows="3" name="france_express_carrier1_description">{$method_1_delay|escape:'htmlall':'UTF-8'}</textarea>
            </div>

            <label>{l s='Price' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <input type="text" size="20" name="mycarrier1_overcost" value="{$method_1_price|escape:'htmlall':'UTF-8'}" />
            <p class="clear">{l s='You must add a price for the delivery method to be displayed' mod='geodisfranceexpress'}</p>
            </div>
        </fieldset>
        <br/>
        <fieldset style="border: 0px;">
            <legend>{l s='Delivery Method 2 : ' mod='geodisfranceexpress'}{$geodis_method2_name|escape:'htmlall':'UTF-8'}</legend>
            <label>{l s='Active' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <input type="radio" name="carrier2_activated" id="activated_on" value="1" {if ($carrier2_activated)}  checked="checked"{/if} />
                <label class="t" for="ajax_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='geodisfranceexpress'}" title="{l s='Enabled' mod='geodisfranceexpress'}" /></label>
                <input type="radio" name="carrier2_activated" id="activated_off" value="0" {if (!$carrier2_activated)}  checked="checked"{/if} />
                <label class="t" for="ajax_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='geodisfranceexpress'}" title="{l s='Disabled' mod='geodisfranceexpress'}" /></label>
            </div>

            <label>{l s='Description' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <textarea rows="3" name="france_express_carrier2_description">{$method_2_delay|escape:'htmlall':'UTF-8'}</textarea>
            </div>

            

            <label>{l s='Price' mod='geodisfranceexpress'} : </label>
            <div class="margin-form">
                <input type="text" size="20" name="mycarrier2_overcost" value="{$method_2_price|escape:'htmlall':'UTF-8'}" />
            <p class="clear">{l s='You must add a price for the delivery method to be displayed' mod='geodisfranceexpress'}</p>
            </div>
        </fieldset>
        
        <!--div class="margin-form"><input class="button" name="submitSave" type="submit"></div-->
        <div class="margin-form"><button name="submitSave" class="btn btn-default pull-right button" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='geodisfranceexpress'}</button></div>
        </form>
    </div>
</div>