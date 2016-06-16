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
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}
{* display error message *}
<div class="geodis_france_express alert alert-danger error" style="display: none;">
    <ol></ol>
</div>

{************************************************************ form for geodis method 1 *******************************************************************}
<div id="geodis-content_carrier_{$france_express_id_carrier1|escape:'html':'UTF-8'}" class="geodis-form france-express-form form-group"  style="display: none;">
    <h3>{l s='I have chosen ' mod='geodisfranceexpress'}...</h3>
    <div class="geodis-col-2">
        <div class="geodis-box">
            <div class="geodis-method-logo"><img src="/modules/geodisfranceexpress/views/img/{$france_express_carrier1_logo|escape:'html':'UTF-8'}"/></div>
            <div class="geodis-heading">
                {l s='Bookings available Monday to Saturday morning over a 14-day period. 1st possible slot: the morning of the day after shipping.' mod='geodisfranceexpress'}
                <br/>{l s='Please indicate your email address and/or your mobile number.' mod='geodisfranceexpress'}
                <br/>{l s='As soon as your order is shipped, FRANCE EXPRESS will send you a personal link giving you access to our online delivery booking portal.' mod='geodisfranceexpress'}
            </div>
            <div class="form-group">
                <label class="label">{l s='Email' mod='geodisfranceexpress'}</label>
                    <span class="input-box">
                        <input  type='text' class="form-control ac_input" name="email" value='{$email|escape:'htmlall':'UTF-8'}' />
                    </span>
            </div>
            <div class="form-group">
                <label class="label">{l s='Mobile phone' mod='geodisfranceexpress'}</label>
                    <span class="input-box">
                        <input  type='text' class="form-control ac_input" name="mobile" value="{$mobile|escape:'html':'UTF-8'}" />
                    </span>
            </div>
        </div>
    </div>
</div>
{************************************************************ form for geodis method 1 *******************************************************************}

{************************************************************ form for geodis method 2 *******************************************************************}
<div id="geodis-content_carrier_{$france_express_id_carrier2|escape:'html':'UTF-8'}" class="geodis-form france-express-form form-group"  style="display: none;">
    <h3>{l s='I have chosen ' mod='geodisfranceexpress'}...</h3>

    <div class="geodis-col-2">
        <div class="geodis-box">
            <div class="geodis-method-logo"><img src="/modules/geodisfranceexpress/views/img/{$france_express_carrier2_logo|escape:'html':'UTF-8'}"/></div>
            <div class="geodis-heading">
                {l s='Please indicate your phone number.' mod='geodisfranceexpress'}
                <br/>{l s='FRANCE EXPRESS will call you when your order is ready for delivery.' mod='geodisfranceexpress'}
            </div>
            <div class="form-group">
                <label class="label">{l s='Phone' mod='geodisfranceexpress'}</label>
                    <span class="input-box">
                        <input  type='text' class="form-control ac_input" name="fixe" value="{$fixe|escape:'html':'UTF-8'}" />
                    </span>
            </div>
            <div class="form-group">
                <label class="label">{l s='Mobile phone' mod='geodisfranceexpress'}</label>
                    <span class="input-box">
                        <input  type='text' class="form-control ac_input" name="mobile" value="{$mobile|escape:'html':'UTF-8'}" />
                    </span>
            </div>
        </div>
    </div>
</div>
{************************************************************ form for geodis method 2 *******************************************************************}

<script type="text/javascript">
    jQuery(document).ready(function() {
        //get id of geodis methods
        var id_geodis_method_1 = jQuery("input[value*='{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}']").attr("id");
        var id_geodis_method_2 = jQuery("input[value*='{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}']").attr("id");

        var oldContent_method1 = jQuery("#"+id_geodis_method_1).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html();
        if(oldContent_method1){
            jQuery("#"+id_geodis_method_1).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html(oldContent_method1.replace('Delivery time:','<span style="margin-left: -4px;"></span>'));
        }
        var oldContent_method2 = jQuery("#"+id_geodis_method_2).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html();
        if(oldContent_method2){
            jQuery("#"+id_geodis_method_2).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html(oldContent_method2.replace('Delivery time:','<span style="margin-left: -4px;"></span>'));
        }
        var oldContent_method1 = jQuery("#"+id_geodis_method_1).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html();
        if(oldContent_method1){
            jQuery("#"+id_geodis_method_1).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html(oldContent_method1.replace('Délai de livraison :','<span style="margin-left: -4px;"></span>'));
        }
        var oldContent_method2 = jQuery("#"+id_geodis_method_2).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html();
        if(oldContent_method2){
            jQuery("#"+id_geodis_method_2).parents('tr').find('td:not(.delivery_option_radio,.delivery_option_price,.delivery_option_logo)').html(oldContent_method2.replace('Délai de livraison :','<span style="margin-left: -4px;"></span>'));
        }
        //check if the method corresponds to the management rules. if not, disable checkbox and add an error message
        if ({$is_enabled[{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'} == 0) {
            //uncheck default checked checkbox
            jQuery('#' + id_geodis_method_1).attr('disabled', 'disabled');
            jQuery('#' + id_geodis_method_1).prop('checked', false);
            jQuery( ".method_{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}" ).remove();
            //add error message if method does not corresponds to geodis management rules
            jQuery('#' + id_geodis_method_1).parents('td').siblings('td:not(.delivery_option_logo)').first().append('<span class="geodis_message method_{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}" style="color: red">{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>');
            jQuery('#' + id_geodis_method_1).parents('.alternate_item').find('.delivery_option_delay').append('<span class="geodis_message method_{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}" style="color: red"><br>{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>')
            jQuery('#' + id_geodis_method_1).parents('.item').find('.delivery_option_delay').append('<span class="geodis_message method_{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}" style="color: red"><br>{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>')
        }
        if ({$is_enabled[{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'} == 0) {
            jQuery('#' + id_geodis_method_2).attr('disabled', 'disabled');
            jQuery('#' + id_geodis_method_1).prop('checked', false);
            jQuery( ".method_{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}" ).remove();
            jQuery('#' + id_geodis_method_2).parents('td').siblings('td:not(.delivery_option_logo)').first().append('<span class="geodis_message method_{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}" style="color: red">{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>');
            jQuery('#' + id_geodis_method_2).parents('.alternate_item').find('.delivery_option_delay').append('<span class="geodis_message method_{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}"" style="color: red"><br>{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>')
            jQuery('#' + id_geodis_method_2).parents('.item').find('.delivery_option_delay').append('<span class="geodis_message method_{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}"" style="color: red"><br>{l s='Your cart does not fulfill the conditions required for this mode' mod='geodisfranceexpress'}</span>')

        }


        //display form which correspond to checkbox selected by customer
        if (jQuery('#' + id_geodis_method_1).attr('checked')) {
            jQuery("#geodis-content_carrier_{$france_express_id_carrier1|escape:'htmlall':'UTF-8'}").show();
        }
        if (jQuery('#' + id_geodis_method_2).attr('checked')) {
            jQuery("#geodis-content_carrier_{$france_express_id_carrier2|escape:'htmlall':'UTF-8'}").show();
        }

        //check if customer select france express method 

        //add event to native ajax method
        jQuery('#form').on('submit', function(e) {
            jQuery('.geodis_france_express').hide();
            if (typeof jQuery('input[class="delivery_option_radio"]:checked', '#form').val() === "undefined") {
                alert('Please specify shipping method.');
                return false;
            }
            var shipping_method_value = (jQuery('input[class="delivery_option_radio"]:checked', '#form').val()).slice(0, - 1);
            //check if France express method is selected by customer 
            if ((parseInt(shipping_method_value) === parseInt({$france_express_id_carrier1|escape:'htmlall':'UTF-8'})) || (parseInt(shipping_method_value) === parseInt({$france_express_id_carrier2|escape:'htmlall':'UTF-8'}))){
                var form = jQuery(this);
                e.preventDefault();
                var valid = false;
                var id_div = '#geodis-content_carrier_' + shipping_method_value;
                jsonObj = {
                    'method' : 'SetShippingGeodisFranceExpressInformation',
                    'params' : {}
                };
                //get values of form selected by customer
                jQuery(id_div + ' input[type="text"]').each(function (){
                    var name = jQuery(this).attr("name");
                    var value = jQuery(this).val();
                    jsonObj['params'][name] = value;
                });
                jsonData = jsonObj;
                {literal}
                //javascript validator

                var email = jsonData.params.email;
                var mobile = jsonData.params.mobile;
                var fixe = jsonData.params.fixe;
                var error = false;
                var error_msg = '';
                //France Express regex for France
                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                var mobile_regex = /^(06|07|6|7)([-\/. ]?[0-9]{2}){4}$/;
                var fixe_regex = /^(01|1|02|2|03|3|04|4|05|5|09)([-\/. ]?[0-9]{2}){4}$/;

                {/literal}
                {if $customer_adress_country_code neq 'France'}
                //France express regex for countries other than France
                var mobile_regex = /^[0-9]+$/;
                var fixe_regex = /^[0-9]+$/;
                {/if}
                {literal}
                if (typeof mobile !== 'undefined' && typeof email !== 'undefined'){
                    if (!email.trim() && !mobile.trim()) {
                        {/literal}  error_msg += '<li>{l s='Please provide at least an email address or a phone number' mod='geodisfranceexpress'}</li>';{literal}
                        error = true;
                    }
                    if (email.trim()) {
                        if (!email.match(mailformat)){
                            {/literal}  error_msg += '<li>{l s='Please provide a valid email adress' mod='geodisfranceexpress'}</li>';{literal}
                            error = true;
                        }
                    }
                    if (mobile.trim()) {
                        if (!mobile.match(mobile_regex)){
                            {/literal}    error_msg += '<li>{l s='Please provide a valid phone number' mod='geodisfranceexpress'}</li>';{literal}
                            error = true;
                        }
                    }


                }
                if (typeof mobile !== 'undefined' && typeof fixe !== 'undefined'){
                    if (!fixe.trim() && !mobile.trim()) {
                        {/literal}  error_msg += '<li>{l s='Please provide at least one phone number' mod='geodisfranceexpress'}</li>';{literal}
                        error = true;
                    }
                    if (mobile.trim()) {
                        if (!mobile.match(mobile_regex)){
                            {/literal}     error_msg += '<li>{l s='Please provide a valid phone number' mod='geodisfranceexpress'}</li>';{literal}
                            error = true;
                        }
                    }
                    if (fixe.trim()) {
                        if (!fixe.match(fixe_regex)){
                            {/literal}    error_msg += '<li>{l s='Please provide a valid phone number' mod='geodisfranceexpress'}</li>';{literal}
                            error = true;
                        }
                    }

                }
                if (error){
                    jQuery('.geodis_france_express').show();
                    jQuery('.geodis_france_express ol').html(error_msg);
                    return false;
                }
                {/literal}
                if ((parseInt(shipping_method_value) === parseInt({$france_express_id_carrier1|escape:'htmlall':'UTF-8'})) || (parseInt(shipping_method_value) === parseInt({$france_express_id_carrier2|escape:'htmlall':'UTF-8'}))){
                    if(jQuery("#cgv").attr('checked')){
                        jQuery.ajax({
                            type: "POST",
                            dataType : 'json',
                            url: baseDir + 'modules/geodisfranceexpress/ajax.php',
                            data:jsonData,
                            success: function(xhr) {
                                if (xhr === 1) {
                                    valid = true;
                                }
                                else {
                                    valid = false;
                                }
                                if (valid == true && jQuery("#cgv").attr('checked')){
                                    form.off('submit').submit();
                                }
                            }
                        });
                    }
                }else{
                    form.off('submit').submit();
                }
            }
        });

    });

</script>