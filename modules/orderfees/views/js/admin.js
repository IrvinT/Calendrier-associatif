/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

/* global restrictions */
/* global token */
/* global currentToken */
/* global dimension_rule_groups_counter */
/* global dimension_rule_counters */
/* global zipcode_rule_groups_counter */
/* global zipcode_rule_counters */
/* global message_errors_zipcode_select_country */
/* global message_errors_dimension_select_dimension */
(function ($) {
    $(function () {
        restrictions.push('payment');

        toggleCartRuleFilter($('#payment_restriction'));

        $('#payment_restriction').click(function () {
            toggleCartRuleFilter(this);
        });
        $('#payment_select_remove').click(function () {
            removeCartRuleOption(this);
        });
        $('#payment_select_add').click(function () {
            addCartRuleOption(this);
        });

        $(document).ajaxSend(function (event, jqxhr, settings) {
            var components = settings.url.split('?');
            var params = components[1].split("&").map(function (n) {
                return n = n.split("="), this[n[0]] = n[1], this
            }.bind({}))[0];

            if (params['controller'] === 'AdminCartRules') {
                params['controller'] = 'AdminOrderFees';
                params['token'] = token;
            }

            settings.url = components[0] + '?' + $.param(params);
        });

        // Dimension restriction
        toggleCartRuleFilter($('#dimension_restriction'));

        $('#dimension_restriction').click(function () {
            toggleCartRuleFilter(this);
        });
        
        // Zipcode restriction
        toggleCartRuleFilter($('#zipcode_restriction'));

        $('#zipcode_restriction').click(function () {
            toggleCartRuleFilter(this);
        });
    });
})(jQuery);

function addDimensionRuleGroup()
{
    $('#dimension_rule_group_table').show();
    dimension_rule_groups_counter += 1;
    dimension_rule_counters[dimension_rule_groups_counter] = 0;

    $.get(
            'ajax-tab.php',
            {controller: 'AdminCartRules', token: currentToken, newDimensionRuleGroup: 1, dimension_rule_group_id: dimension_rule_groups_counter},
    function (content) {
        if (content !== "")
            $('#dimension_rule_group_table').append(content);
    }
    );
}

function removeDimensionRuleGroup(id)
{
    $('#dimension_rule_group_' + id + '_tr').remove();
}

function addDimensionRule(dimension_rule_group_id)
{
    var type = $('#dimension_rule_type_' + dimension_rule_group_id).val();
    
    // Check
    if (type === '') {
        alert(message_errors_dimension_select_dimension);
        
        return;
    }
    
    if (typeof dimension_rule_counters[dimension_rule_group_id] === 'undefined') {
        dimension_rule_counters[dimension_rule_group_id] = 0;
    }
    
    dimension_rule_counters[dimension_rule_group_id] += 1;
    
    if (type !== '')
        $.get(
                'ajax-tab.php',
                {
                    controller: 'AdminCartRules',
                    token: currentToken,
                    newDimensionRule: 1,
                    dimension_rule_type: type,
                    dimension_rule_group_id: dimension_rule_group_id,
                    dimension_rule_id: dimension_rule_counters[dimension_rule_group_id]
                },
        function (content) {
            if (content !== "")
                $('#dimension_rule_table_' + dimension_rule_group_id).append(content);
        }
        );
}

function removeDimensionRule(dimension_rule_group_id, dimension_rule_id)
{
    $('#dimension_rule_' + dimension_rule_group_id + '_' + dimension_rule_id + '_tr').remove();
}

function addZipcodeRuleGroup()
{
    $('#zipcode_rule_group_table').show();
    zipcode_rule_groups_counter += 1;
    zipcode_rule_counters[zipcode_rule_groups_counter] = 0;

    $.get(
            'ajax-tab.php',
            {controller: 'AdminCartRules', token: currentToken, newZipcodeRuleGroup: 1, zipcode_rule_group_id: zipcode_rule_groups_counter},
    function (content) {
        if (content !== "")
            $('#zipcode_rule_group_table').append(content);
    }
    );
}

function removeZipcodeRuleGroup(id)
{
    $('#zipcode_rule_group_' + id + '_tr').remove();
}

function addZipcodeRule(zipcode_rule_group_id)
{
    var type = $('#zipcode_rule_type_' + zipcode_rule_group_id).val();
    
    // Check
    if (type === '') {
        alert(message_errors_zipcode_select_country);
        
        return;
    }
    
    if (typeof zipcode_rule_counters[zipcode_rule_group_id] === 'undefined') {
        zipcode_rule_counters[zipcode_rule_group_id] = 0;
    }
    
    zipcode_rule_counters[zipcode_rule_group_id] += 1;
    
    if (type !== '')
        $.get(
                'ajax-tab.php',
                {
                    controller: 'AdminCartRules',
                    token: currentToken,
                    newZipcodeRule: 1,
                    zipcode_rule_type: type,
                    zipcode_rule_group_id: zipcode_rule_group_id,
                    zipcode_rule_id: zipcode_rule_counters[zipcode_rule_group_id]
                },
        function (content) {
            if (content !== "") {
                $('#zipcode_rule_table_' + zipcode_rule_group_id).append(content);
            }
        }
        );
}

function removeZipcodeRule(zipcode_rule_group_id, zipcode_rule_id)
{
    $('#zipcode_rule_' + zipcode_rule_group_id + '_' + zipcode_rule_id + '_tr').remove();
}