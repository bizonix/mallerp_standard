
function accounting_cost(url)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var order_count = 0;
    var error = false;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var order_id = e.value;
            params.set('order_id_' + order_count, order_id);
            var shipping_cost = $('shipping_cost_' + order_id).value;

            var product_cost = 0;
            var product_cost_string = '';
            var count =  $('order_sku_count_' + order_id).value;

            for(i=0;i<count;i++)
            {
                var price = $('cost_price_' + order_id + '_' + i).value;
                if ( ! price)
                {
                    product_cost = 0;
                    break;
                }
                else
                {
                    product_cost += $('sku_count_' + order_id + '_' + i).value * price;
                    product_cost_string += price + ',';
                }            
            }
            if(product_cost)
            {
                product_cost += $('other_cost_price_' + order_id).value*1;
                product_cost_string += $('other_cost_price_' + order_id).value;
            }
            
            params.set('product_cost_string_' + order_count, product_cost_string);
            params.set('shipping_cost_' + order_count, shipping_cost);
            params.set('product_cost_' + order_count, product_cost);
            order_count++;
        }
    });

    if (error)
    {
        alert(error);
        return false;
    }

    if (order_count == 0)
    {
        alert('no item selected!');
    }
    else
    {
        params.set('order_count', order_count);
        helper.ajax_reflesh(url, params, 'post',1);
    }
    return true;
}

function count_top_cost(id,count)
{
    var cost_price = 0;
    for(i=0;i<count;i++)
    {
        var price = $('cost_price_'+id+'_'+i).value;
        var num = $('sku_count_'+id+'_'+i).value;
        cost_price += price*num*1;
    }

    $('top_cost_'+id).value = cost_price;

}

function accounting_cost_download()
{
    var selects = $$('input[id^="select_" type=checkbox]');
    
    var order_ids_str = '';
    
    selects.each(function(e)
    {
        if (e.checked)
        {
            var order_id = e.value;
            order_ids_str += order_id+',';
        }
    });
    
    $('order_ids_str').value = order_ids_str;
    
    return true;
}