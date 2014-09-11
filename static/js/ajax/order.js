function confirm_order(current, url, order_id, last)
{
    helper.show_loading();
    var params = $H({});
    var count = $('count_' + order_id).value.strip();
    var qty_string = '';
    var sku_string = '';
    var item_id_string = '';
    for (var i = 0; i < count; i++)
    {
        if (! $('sku_' + order_id + '_' + i))
        {
            continue;
        }
        var qty = $('qty_' + order_id + '_' + i).value.strip();
        var sku = $('sku_' + order_id + '_' + i).value.strip();
        var item_id = $('item_id_' + order_id + '_' + i).value.strip();
        if (!sku)
        {
            alert('SKU!');
            return false;
        }
        if (!qty)
        {
            alert('Qty!');
            return false;
        }
	if (qty==0)
        {
            alert('Qty=0!');
            return false;
        }

        item_id_string += item_id;
        qty_string += qty;
        sku_string += sku;
        if (i < count - 1)
        {
            item_id_string += ',';
            qty_string += ',';
            sku_string += ',';
        }
    }
    
    params.set('item_id_string', item_id_string);
    params.set('qty_string', qty_string);
    params.set('sku_string', sku_string);

    var shipping_way = $('shipping_way_' + order_id).value.strip();
    if (!shipping_way)
    {
        alert('Shipping way!');
        return false;
    }
    params.set('shipping_way', shipping_way);

    var phone = $('phone_' + order_id).value.strip();
    params.set('phone', phone);

    var note = $('note_' + order_id).value.strip();
    params.set('note', note);
    params.set('order_id', order_id);
    
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params);
    }
    return true;
}



function batch_confirm_order(url)
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
            var count = $('count_' + order_id).value.strip();
            var qty_string = '';
            var sku_string = '';
            var item_id_string = '';
            for (var i = 0; i < count; i++)
            {
                if (! $('sku_' + order_id + '_' + i))
                {
                    continue;
                }
                var qty = $('qty_' + order_id + '_' + i).value.strip();
                var sku = $('sku_' + order_id + '_' + i).value.strip();
                var item_id = $('item_id_' + order_id + '_' + i).value.strip();
                if (!sku)
                {
                    error = 'SKU!';
                    throw $break;
                }
                if (!qty)
                {
                    error = 'Qty!';
                    throw $break;
                }
		if (qty==0)
                {
                    error = 'Qty=0!';
                    throw $break;
                }

                item_id_string += item_id;
                qty_string += qty;
                sku_string += sku;
                if (i < count - 1)
                {
                    item_id_string += ',';
                    qty_string += ',';
                    sku_string += ',';
                }
            }
            params.set('item_id_string_' + order_count, item_id_string);
            params.set('qty_string_' + order_count, qty_string);
            params.set('sku_string_' + order_count, sku_string);
            
            var shipping_way = $('shipping_way_' + order_id).value.strip();
            if (!shipping_way)
            {
                error = 'Shipping way!';
                return false;
            }
            params.set('shipping_way_' + order_count, shipping_way);

            var phone = $('phone_' + order_id).value.strip();
            params.set('phone_' + order_count, phone);

            var note = $('note_' + order_id).value.strip();
            params.set('note_' + order_count, note);

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
        return false;
    }

    helper.show_loading();

    params.set('order_count', order_count);

    helper.ajax_reflesh(url, params);

    return true;
}

function hold_order(current, url, order_id, last)
{
    var params = $H({'order_id': order_id});
    var note = $('note_' + order_id).value.strip();
    params.set('note', note);
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params, current.parentNode.parentNode.parentNode);
    }
    return false;
}

function finance_order_back(current, url, order_id, last)
{
    if ( ! confirm('Are you sure?'))
    {
        return false;
    }
    var params = $H({'order_id': order_id});
    var note = $('note_' + order_id).value.strip();
    params.set('note', note);
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params);
    }
    return false;
}

function close_order(current, url, order_id, last)
{
    if ( ! confirm('Are you sure?'))
    {
        return false;
    }
    var params = $H({'order_id': order_id});
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params, current.parentNode.parentNode.parentNode);
    }
    return false;
}

function add_item(base_url, order_id)
{
    var item_count = $('count_' + order_id).value;
    var new_item = '<div style="margin: 5px;">';
    new_item += "Item ID: ";
    var item_id = 'item_id_' + order_id + '_' + item_count;
    new_item += '<input type="text" size="10" maxlength="20" id="' + item_id + '" name="' + item_id + '"> SKU: ';
    var sku_id = 'sku_' + order_id + '_' + item_count;
    new_item += '<input type="text" size="8" maxlength="20" id="' + sku_id + '" value="" name="' + sku_id + '"> Qty: ';
    var qty = 'qty_' + order_id + '_' + item_count;
    new_item += '<input type="text" size="6" maxlength="8" id="' + qty + '" value="1" name="' + qty + '"> ';
    new_item += helper.cancel_icon(base_url, '$(this.parentNode).remove();');
    new_item += '</div>';
    var item_div = 'item_div_' + order_id;
    $('count_' + order_id).value = parseInt($('count_' + order_id).value) + 1;
    $(item_div).insert(new_item);
}

function add_item_for_product_list(base_url)
{
    var html = '<div style="margin: 0px;">';
    html += 'SKU <input type="text" name="sku[]" value="" id="sku" maxlength="100" size="15"  /> QTY <input type="text" name="qty[]" value="" id="qty" maxlength="100" size="15"  /> PRICE <input type="text" name="price[]" value="" id="price" maxlength="100" size="15"  />'
    html += helper.cancel_icon(base_url, '$(this.parentNode).remove();');
    html += '</div>';
    $('item_div').insert(html);
}

function delete_sku(link)
{
        $(link.parentNode).remove();    
}

/*
 *批量审核订单。
 ***/
function batch_auditing_order(url)
{
    var selects = $$('input[id^="checkbox_select_"]');
    var params = $H({});
    var order_count = 0;
    var error = false;
    if(confirm("你确定批理通过"))
    {
        selects.each(function(e)
        {
            if (e.checked)
            {
                var order_id = e.value;
                params.set('order_id_' + order_count, order_id);
                
                var refund_verify_type = $('refund_verify_type_'+order_id).value;
                params.set('refund_verify_type_' + order_id, refund_verify_type);
                
                var refund_verify_content = $('refund_verify_content_'+order_id).value;
                params.set('refund_verify_content_' + order_id, refund_verify_content);
                
                var refund_duty = $('refund_duty_'+order_id).value;
                params.set('refund_duty_' + order_id, refund_duty);

                var refund_sku = $$('input[id^="refund_sku_' + order_id + '"]');

                var refund_sku_str = '';
                
                refund_sku.each(function(o)
                {
                    if (o.checked)
                    {
                        refund_sku_str += o.value+','
                    }
                });
                  
                var other_sku_obj = $('other_refund_sku_'+order_id);
                
                var other_sku = '';
                if(other_sku_obj)
                {
                    other_sku = other_sku_obj.value;
                }
                
                refund_sku_str += other_sku;
                
                if(refund_sku_str)
                {
                    if( ! other_sku)
                    {
                        refund_sku_str = refund_sku_str.substr(0, refund_sku_str.length-1);
                    }
                    params.set('refund_sku_str_' + order_id, refund_sku_str);
                }
                
                order_count++;
            }
        });
    }

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
        $('order_auditing').hide();
    }
    return true;
}

/**
 *
 **/
function batch_auditing_order_duty(url)
{
    var selects = $$('input[id^="checkbox_select_"]');
    var params = $H({});
    var order_count = 0;
    var error = false;
    
    if(confirm("你确定批理通过"))
    {
        selects.each(function(e)
        {
            if (e.checked)
            {
                var order_id = e.value;
                params.set('order_id_' + order_count, order_id);
                
                var refund_verify_type = $('refund_verify_type_'+order_id).value;
                params.set('refund_verify_type_' + order_id, refund_verify_type);
                
                var refund_verify_content = $('refund_verify_content_'+order_id).value;
                params.set('refund_verify_content_' + order_id, refund_verify_content);
                
                var refund_duty = $('refund_duty_'+order_id).value;
                params.set('refund_duty_' + order_id, refund_duty);

                var refund_sku = $$('input[id^="refund_sku_' + order_id + '"]');

                var refund_sku_str = '';
                
                refund_sku.each(function(o)
                {
                    if (o.checked)
                    {
                        refund_sku_str += o.value+','
                    }
                });

                var other_sku = $('other_refund_sku_'+order_id).value;
                refund_sku_str += other_sku;
                
                if(refund_sku_str)
                {
                    if( ! other_sku)
                    {
                        refund_sku_str = refund_sku_str.substr(0, refund_sku_str.length-1);
                    }
                    params.set('refund_sku_str_' + order_id, refund_sku_str);
                }
                
                order_count++;
            }
        });
    }

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
        $('order_auditing').hide();
    }
    return true;
}



function auditing_order(current,url,order_id)
{
    var params = $H({});
    
    if(confirm("你确定通过"))
    {
        params.set('order_id', order_id);
        
        var refund_verify_type = $('refund_verify_type_'+order_id).value;
        params.set('refund_verify_type_' + order_id, refund_verify_type);

        var refund_verify_content = $('refund_verify_content_'+order_id).value;
        params.set('refund_verify_content_' + order_id, refund_verify_content);

        var refund_duty = $('refund_duty_'+order_id).value;
        params.set('refund_duty_' + order_id, refund_duty);

        var refund_sku = $$('input[id^="refund_sku_' + order_id + '"]');

        var refund_sku_str = '';

        refund_sku.each(function(o)
        {
            if (o.checked)
            {
                refund_sku_str += o.value+','
            }
        });

//        var other_sku = $('other_refund_sku_'+order_id).value;
//        refund_sku_str += other_sku;

        var other_sku_obj = $('other_refund_sku_'+order_id);
                
        var other_sku = '';
        if(other_sku_obj)
        {
            other_sku = other_sku_obj.value;
        }

        refund_sku_str += other_sku;


        if(refund_sku_str)
        {
            if( ! other_sku)
            {
                refund_sku_str = refund_sku_str.substr(0, refund_sku_str.length-1);
            }
            params.set('refund_sku_str_' + order_id, refund_sku_str);
        }

        helper.drop_row(current, '', url, params);
        
        return true;
    }
    return false;
}


function auditing_order_by_rejected(current,url,order_id)
{
    var params = $H({});
    
    if(confirm("你确定拒绝？"))
    {
        params.set('order_id', order_id);

        helper.drop_row(current, '', url, params);
        
        return true;
    }
    return false;
}
function split_add_item_for_product_list(base_url)
{
    var html = '<div style="margin: 0px;">';
    html += 'SKU <input type="text" name="old_sku[]" value="" id="old_sku" maxlength="100" size="15"  /> QTY <input type="text" name="old_qty[]" value="" id="old_qty" maxlength="100" size="15"  /> PRICE <input type="text" name="old_price[]" value="" id="old_price" maxlength="100" size="15"  />'
    html += helper.cancel_icon(base_url, '$(this.parentNode).remove();');
    html += '</div>';
    $('old_item_div').insert(html);
}