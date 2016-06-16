/*
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**/
function opartDevisLoadCarrierList() {
    //console.log(urlLoadCarrier);
    var form=$('#opartDevisForm');
	data=form.serialize();	
	//ajax call
	$.ajax({ 
    	type : 'POST', 
        url :opartDevisControllerUrl+'&ajax_carrier_list&'+data,
        success : function(data){
            OpartDevisPopulateSelectCarrier(data);
        }, error : function(XMLHttpRequest, textStatus, errorThrown) { 
           alert('Une erreur est survenue !'); 
        }
    });	
}
function OpartDevisPopulateSelectCarrier(data) {
    //console.log(data);
    //decode jsoon;
    data = $.parseJSON(data);
    var carrierSelect = $('#opart_devis_carrier_input');
    carrierSelect.html('');
    for (var key in data) {
        if ($('#selected_carrier').val() == key)
            var selected = 'selected';
        else
            var selected = '';
        carrierSelect.append('<option value="' + key + '" ' + selected + '>' + data[key]['name'] + ' - ' + data[key]['price'] + ' &euro; (' + data[key]['taxOrnot'] + ')</option>');
    }
    OpartDevisChangeCarrier();
}
function OpartDevisChangeCarrier() {
    data=$('#opartDevisForm').serialize();
    $.ajax({
        type: 'POST',
        url: opartDevisControllerUrl + '&change_carrier_cart&' + data,
        success: function (data) {
            //console.log(data);
            var data = $.parseJSON(data);
            //console.log(data);
            $('#opartQuotationTotalQuotationWithTax').html(formatCurrency(data.total_price, currency_format, currency_sign, currency_blank));
            $('#opartQuotationTotalQuotation').html(formatCurrency(data.total_price_without_tax, currency_format, currency_sign, currency_blank));
            $('#opartQuotationTotalTax').html(formatCurrency(data.total_tax, currency_format, currency_sign, currency_blank));
            $('#opartQuotationTotalDiscounts').html(formatCurrency(data.total_discounts, currency_format, currency_sign, currency_blank));
            $('#opartQuotationTotalShipping').html(formatCurrency(data.total_shipping, currency_format, currency_sign, currency_blank));

        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('Une erreur est survenue !');
        }
    });
}