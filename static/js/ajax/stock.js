function outstock_by_statistics(url)
{
    var params = $H({end_time: $('end_time').value});
    $('outstock').disabled = true;
    
    return helper.ajax(url, params, 1);
}

function batch_inoutstock(url, stock_type)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var product_count = 0;
    var error = false;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var product_id = e.value;
            params.set('product_id_' + product_count, product_id);
            var outstock = $('stock_count_' + product_id).value;
            if ($('shelf_code_' + product_id))
            {
                params.set('shelf_code_' + product_count, $('shelf_code_' + product_id).value);
            }
			if ($('stock_code_' + product_id))
            {
                params.set('stock_code_' + product_count, $('stock_code_' + product_id).value);
            }

            if ($('type_' + product_id))
            {
                var type = $('type_' + product_id).value;
            }
            if ($('type_extra_' + product_id))
            {
                var type_extra = $('type_extra_' + product_id).value;
            }
            if ( ! /^\d+$/.test(outstock))
            {
                error = 'stock number!';
                throw $break
            }
            if ($('type_extra_' + product_id))
            {
                if ( ! type_extra.strip())
                {
                    error = 'remark!';
                    throw $break
                }
            }
            else
            {
                if ( ! $('shelf_code_' + product_id).value.strip())
                {
                    error = 'shelf code is required!';
                    throw $break;
                }
            }
            
            params.set('stock_count_' + product_count, outstock);
            if ($('type_' + product_id))
            {
                params.set('type_' + product_count, type);
            }
            if ($('type_extra_' + product_id))
            {
                params.set('type_extra_' + product_count, type_extra);
            }
            product_count++;
        }
    });

    if (error)
    {
        alert(error);
        return false;
    }

    if (product_count == 0)
    {
        alert('no item selected!');
        return false;
    }

    params.set('product_count', product_count);
    params.set('stock_type', stock_type);

    helper.ajax_reflesh(url, params, 'post', 1);

    return true;
}

function verify_apply_instock(current, url, apply_id, status, last, ask)
{
    if (status == -1 && ! confirm('Are you sure?'))
    {
        return;
    }
    var params = $H({});
    params.set('apply_id', apply_id);
    params.set('status', status);
    
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params);
    }
}

function batch_verify_apply_instock(url, status, ask)
{
    if (status == -1 && ! confirm('Are you sure?'))
    {
        return;
    }
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var apply_count = 0;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var apply_id = e.value;
            params.set('apply_id_' + apply_count, apply_id);
            apply_count++;
        }
    });

    if (apply_count == 0)
    {
        alert('no item selected!');
        return false;
    }

    params.set('apply_count', apply_count);
    params.set('status', status);

    helper.ajax_reflesh(url, params, 'post');

    return true;
}

function batch_stock_check_or_count(url)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var product_count = 0;
    var error = false;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var product_id = e.value;
            params.set('product_id_' + product_count, product_id);
            var stock_count = $('stock_count_' + product_id).value;
            var before_count = $('before_count_' + product_id).value;
			
			if ($('stock_code_' + product_id))
            {
                var stock_code = $('stock_code_' + product_id).value;
            }
			if ($('stock_code_' + product_id))
            {
                params.set('stock_code_' + product_count, stock_code);
            }
			
            if ($('type_' + product_id).value == '')
            {
                error = 'explanation is required!';
                throw $break;
            }
            if ($('type_' + product_id))
            {
                var type = $('type_' + product_id).value;
            }
            if (($('duty_' + product_id).value == '') && ((stock_count - before_count) != 0))
            {
                error = 'duty is required!';
                throw $break;
            }
            if ($('duty_' + product_id))
            {
                var duty = $('duty_' + product_id).value;
            }
            if ($('type_extra_' + product_id))
            {
                var type_extra = $('type_extra_' + product_id).value;
            }
            if ($('shelf_code_' + product_id))
            {
                var shelf_code = $('shelf_code_' + product_id).value;
            }
            if ( ! /^\d+$/.test(stock_count))
            {
                error = 'stock number!';
                throw $break;
            }          
            params.set('stock_count_' + product_count, stock_count);
            if ($('type_' + product_id))
            {
                params.set('type_' + product_count, type);
            }
            if ($('duty_' + product_id))
            {
                params.set('duty_' + product_count, duty);
            }
            if ($('type_extra_' + product_id))
            {
                params.set('type_extra_' + product_count, type_extra);
            }
            if ($('shelf_code_' + product_id))
            {
                params.set('shelf_code_' + product_count, shelf_code);
            }
            product_count++;
        }
    });

    if (error)
    {
        alert(error);
        return false;
    }

    if (product_count == 0)
    {
        alert('no item selected!');
        return false;
    }   
    params.set('product_count', product_count);
    helper.ajax_reflesh(url, params, 'post',1);

    return true;
}


function add_sku_and_qty(base_url)
{
    
    var new_item = '<div style="margin: 5px;">';
    new_item += " SKU : ";
    var sku_id = 'sku[]';
    new_item += '<input type="text" size="20" maxlength="20"  value="" name="' + sku_id + '"> Qty : ';
    var qty = 'qty[]';
    new_item += '<input type="text" size="10" maxlength="10" value="" name="' + qty + '"> ';
    
    new_item += '&nbsp; 申报名称 : <input type="text" size="50" maxlength="50" value="" name="declared_name[]"> ';
    new_item += '&nbsp; 申报价值 : <input type="text" size="15" maxlength="15" value="" name="declared_price[]"> ';
    
    new_item += helper.cancel_icon(base_url, '$(this.parentNode).remove();');
    new_item += '</div>';
    $('sku_and_qty').insert(new_item);
    
}

function clear_div()
{
    $('success-msg-top').hide();
    $('important-top').hide();
    $('success-msg-foot').hide();
    $('important-foot').hide();
}

function confirm_review(url,id)
{
//    helper.show_loading();
    var params = $H({});
    var type_extra = $('type_extra_' + id).value.strip();
    var duty = $('duty_' + id).value.strip();
    params.set('type_extra', type_extra);
    params.set('id', id);
    params.set('duty', duty);
    helper.ajax_reflesh(url, params);
    return true;
}