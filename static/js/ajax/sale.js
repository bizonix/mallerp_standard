function add_product(base_url, current)
{
    var item_count = $('counter').value;
    var new_row = '<tr class="td-odd"><td style="padding: 2px;"> SKU: <input type="text" size="8" maxlength="20" id="sku_' + item_count + '" value="" name="sku_' + item_count + '"> QTY: <input type="text" size="4" maxlength="20" id="qty_' + item_count + '" value="1" name="qty_' + item_count + '"> ' + helper.cancel_icon(base_url, '$(this.parentNode.parentNode).remove();') + '</td><td style="padding: 2px;"><img style="display: none;" height="80" id="image_' + item_count + '" src=""></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="weight_' + item_count + '" value="" name="weight_' + item_count + '"><div id="weight_more_' + item_count + '"></div></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="price_' + item_count + '" value="" name="price_' + item_count + '"></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="length_' + item_count + '" value="" name="length_' + item_count + '"></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="width_' + item_count + '" value="" name="width_' + item_count + '"></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="height_' + item_count + '" value="" name="height_' + item_count + '"></td><td style="padding: 2px;"><input type="text" size="8" maxlength="20" id="profit_' + item_count + '" value="" name="profit_' + item_count + '"></td><td style="padding: 2px;"></td></tr>';
    var tbody = current.parentNode.parentNode.parentNode;
    $(tbody).insert(new_row);
    $('counter').value = parseInt(item_count) + 1;
}

function init_price_result(url)
{
    helper.hide_failure();
    var params = $H({});

    params.set('init', 1);
    params.set('eshop_code', $('eshop_code').value);

    helper.update_content(url, params, 'price_result', 1);
}

function calculate_price(url, init)
{
    helper.hide_failure();
    
    var params = calculate_params(init);

    if (params == false)
    {
        return;
    }

    helper.update_content(url, params, 'price_result', 1);
}

function calculate_profit(url)
{
    var params = calculate_params();

    if (params == false)
    {
        return;
    }
    var suggest_price = $('suggest_price').value.strip();
    if ( ! suggest_price || suggest_price <= 0)
    {
        alert('price');
        return;
    }
    params.set('suggest_price', suggest_price);

    helper.hide_failure();
    
    helper.update_content(url, params, 'price_result', 1);
}

function change_quantity(num){
    var unit_price = "unit_price_"+num;
    var quantity ="quantity_"+num;
    var sub_total = "sub_total_"+num;
    var array_unit_price = $(unit_price).value;
    var qty = $(quantity).value;
    var total = $(sub_total).value;
    if(isNaN(qty)){
        alert('Quantity');
    }
    var total_all = (array_unit_price)*(qty);
    $(sub_total).value = total_all;
    var count = $('count').value;
            var total_t = 0;
            for (i=0;i<count;i++)
            {
                var sub_total_m = "sub_total_"+i;
                var total_m = $(sub_total_m).value;
                total_t = parseInt(total_m) + parseInt(total_t);
            }
            var abc = "abc";
            $(abc).value = total_t;
}

function  change_unit_price(num){
    var unit_price = "unit_price_"+num;
    var quantity ="quantity_"+num;
    var sub_total = "sub_total_"+num;
    var array_unit_price = $(unit_price).value;
    var qty = $(quantity).value;
    if(isNaN(array_unit_price))
    {
        alert('Unit Price');
    }
    var sub_total_all = (array_unit_price)*(qty);
    $(sub_total).value = sub_total_all;
var count = $('count').value;
            var total_t = 0;
            for (i=0;i<count;i++)
            {
                var sub_total_m = "sub_total_"+i;
                var total_m = $(sub_total_m).value;
                total_t = parseInt(total_m) + parseInt(total_t);
            }
            var abc = "abc";
            $(abc).value = total_t;
}

function change_sub_total(j){
    var unit_price = "unit_price_"+j;
    var quantity ="quantity_"+j;
    var sub_total = "sub_total_"+j;
    var array_unit_price = $(unit_price).value;
    var qty = $(quantity).value;
    var total = $(sub_total).value;
    var unit_price_all = (total)/(qty);
    $(unit_price).value = unit_price_all;
    var count = $('count').value;
            var total_t = 0;
            for (i=0;i<count;i++)
            {
                var sub_total_m = "sub_total_"+i;
                var total_m = $(sub_total_m).value;
                total_t = parseInt(total_m) + parseInt(total_t);
            }
            var abc = "abc";
            $(abc).value = total_t;
}

function make_pi(url){

    var params = $H({});
    var counter = $('counter').value;
    var product_count = 0;
    for (var i = 0; i < counter; i++)
    {
        if ( ! $('sku_' + i))
        {
            continue;
        }
        var sku = $('sku_' + i).value.strip();
        if ( ! sku)
        {
            alert('sku');
            return false;
        }
        params.set('sku_' + product_count, sku);

        var qty = $('qty_' + i).value.strip();
        if ( ! qty)
        {
            alert('qty');
            return false;
        }
        params.set('qty_' + product_count, qty);

        product_count++;
    }
    params.set('product_count', product_count);

    helper.update_content(url, params, 'on_pi_manage_show');
}

function calculate_params()
{
    var params = $H({});
    var counter = $('counter').value;

    var product_count = 0;
    for (var i = 0; i < counter; i++)
    {
        if ( ! $('sku_' + i))
        {
            continue;
        }
        var sku = $('sku_' + i).value.strip();
        if ( ! sku)
        {
            alert('sku');
            return false;
        }
        params.set('sku_' + product_count, sku);

        var qty = $('qty_' + i).value.strip();
        if ( ! qty)
        {
            alert('qty');
            return false;
        }
        params.set('qty_' + product_count, qty);

        var weight = $('weight_' + i).value.strip();
        if ( ! weight)
        {
            alert('weight');
            return false;
        }
        params.set('weight_' + product_count, weight);

        var price = $('price_' + i).value.strip();
        if ( ! price)
        {
            alert('price');
            return false;
        }
        params.set('price_' + product_count, price);

        var profit = $('profit_' + i).value.strip();
        if ( ! profit || profit >= 1)
        {
            alert('profit');
            return false;
        }
        params.set('profit_' + product_count, profit);
		
		var length = $('length_' + i).value.strip();
        params.set('length_' + product_count, length);
		var width = $('width_' + i).value.strip();
        params.set('width_' + product_count, width);
		var height = $('height_' + i).value.strip();
        params.set('height_' + product_count, height);
        product_count++;
    }

    if ( ! $('eshop_code').value)
    {
        alert('eshop is required');
        return false;
    }
    params.set('product_count', product_count);
    params.set('pay_option', $('pay_option').value);
    params.set('pay_discount', $('pay_discount').value);
    params.set('eshop_code', $('eshop_code').value);
    params.set('sale_mode', $('sale_mode').value);
    if ($('sale_mode').value == 'auction')
    {
        if ($('bid_rate').value <= 0 || $('bid_rate').value > 100)
        {
            alert('bid rate is required and should be > 0 and <= 100');
            return false;
        }
        params.set('bid_rate', $('bid_rate').value);
    }
    params.set('eshop_list_count', $('eshop_list_count').value);
    params.set('shipping_type', $('shipping_type').value);
    params.set('shipping_country', $('shipping_country').value);
    params.set('buyer_shipping_cost', $('buyer_shipping_cost').value);
    params.set('other_cost', $('other_cost').value);
    params.set('eshop_category', $('eshop_category').value);

    return params;
}

function fetch_product_information(url)
{
    helper.hide_failure();
    
    var params = $H({});
    var counter = $('counter').value;

    var product_count = 0;
    for (var i = 0; i < counter; i++)
    {
        if ( ! $('sku_' + i))
        {
            continue;
        }
        var sku = $('sku_' + i).value.strip();
        if ( ! sku)
        {
            alert('sku');
            return false;
        }
        params.set('sku_str_' + product_count, sku + "|" + i);

        product_count++;
    }
    params.set('product_count', product_count);

    helper.show_loading();
    new Ajax.Request(url, {
        parameters: params,
        onSuccess: function (transport) {
            var response = $H(eval(transport.responseJSON));
            if (response.get('status') == 0) {
                helper._failure(response.get('msg'));
            }
            else
            {
                response = $A(eval(transport.responseJSON));
                response.each(function (e) {
                    var product = $H(e);
                    var index = product.get('index');
                    $('weight_' + index).value = product.get('total_weight');
                    $('weight_more_' + index).innerHTML = product.get('weight_more');
                    $('weight_more_' + index).show();
                    $('price_' + index).value = product.get('price');
					$('length_' + index).value = product.get('length');
					$('width_' + index).value = product.get('width');
					$('height_' + index).value = product.get('height');
                    $('profit_' + index).value = product.get('lowest_profit');
                    $('image_' + index).src = product.get('image_url');
                    $('image_' + index).show();
                });
            }
            helper.hide_loading();
        },
        onFailure: function (transport) {
            helper._failure(transport.responseText);
            helper.hide_loading();
        }
    });

    return false;
}

function fetch_eshop_catalog(url)
{
    var params = $H({});
    params.set('eshop_code', $('eshop_code').value);
    params.set('sale_mode', $('sale_mode').value);
    
    helper.update_content(url, params, 'category_div', 1);
}

function toggle_bid_rate()
{
    if ($('sale_mode').value == 'auction')
    {
        $('bid_rate_div').show();
    }
    else
    {
        $('bid_rate_div').hide();
    }
}
