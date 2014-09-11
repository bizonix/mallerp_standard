function update_country_area(url, value)
{
    new Ajax.Updater('country_area', url, {
        parameters: {
            subarea_group_id:　value
        }
    });
}

function before_print_label(url)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var order_count = 0;
    helper.show_loading();
    selects.each(function(e)
    {
        if (e.checked)
        {
            order_id = e.value;
            params.set('order_id_' + order_count, order_id);
            order_count++;
        }
    });
    
    if (order_count == 0)
    {
        helper.hide_loading();
        alert('no item selected!');
        return false;
    }

    params.set('order_count', order_count);

    new Ajax.Request(url, {
        parameters: params,
        onSuccess: function (transport) {
            helper.hide_loading();
            var response = transport.responseText;
            $('content').update(response);
            $('content').setStyle({
                'margin': 0
            });
            $('nav').hide();
            $('footer').hide();
        },
        onFailure: function () {
            helper.hide_loading();
            alert('unknown error, try again!');
        }
    });

    return true;
}

function print_label(url)
{
    helper.show_loading();

    var selects = $$('input[id^="order_id_"]');
    var params = $H({});
    var order_count = 0;
    selects.each(function(e)
    {
        var order_id = e.value;
        params.set('order_id_' + order_count, order_id);
        order_count++;
    });

    if (order_count == 0)
    {
        helper.hide_loading();
        return false;
    }

    params.set('order_count', order_count);
    
    new Ajax.Request(url, {
        parameters: params,
        onSuccess: function (transport) {
            helper.hide_loading();
            var response = transport.responseText;
            $('content').update(response);
            $('content').setStyle({
                'margin': 0
            });
            $('nav').hide();
            $('footer').hide();
        },
        onFailure: function () {
            helper.hide_loading();
            alert('unknown error, try again!');
        }
    });

    return true;
}

function before_confirm_shipping(url)
{
    var params = $H({});

    if ($('bar_code').value.strip())
    {
        params.set('value', $('bar_code').value.strip());
        params.set('type', 'bar_code');
    }
    else if ($('item_no').value.strip())
    {
        params.set('value', $('item_no').value.strip());
        params.set('type', 'item_no');
    }
    else
    {
        alert('input bar code or item no.!');
        return false;
    }
    helper.update_content(url, params);
    
    return true;
}

function confirm_shipping(url)
{
    if ( ! $('is_register').value.strip())
    {
        alert('Shipping type!');
        return false;
    }
    if ( ! $('weight_0').value.strip() || ! helper.is_num($('weight_0').value.strip()))
    {
        alert('Weight is not number!');
        return false;
    }

    var track_numbers = $A();
    var params = $H({});
    if ($('weight_0').value.strip())
    {
        params.set('weight_0', $('weight_0').value.strip());
    }
    if ($('shipping_remark').value.strip())
    {
        params.set('shipping_remark', $('shipping_remark').value.strip());
    }
    params.set('is_register', $('is_register').value.strip());
    params.set('order_id', $('order_id').value);
    if ($('track_number_0').value.strip())
    {
        
        if ($('track_number_0').value.length < 10)
        {
            alert('Track Number Error!');
            return false;
        }
        params.set('track_number_0', $('track_number_0').value.strip());
        track_numbers[0] = $('track_number_0').value.strip();
    }
    
    var packet_count = $('packet_count').value;
    params.set('packet_count', packet_count);
    
    if (packet_count > 1)
    {
        for (var i = 1; i < packet_count; i++)
        {
            if ($('weight_' + i))
            {
                if ($('is_register').value.strip() == 'H')
                {
                    alert('Epacket can\'t split packet');
                    return false;
                }
                if ( ! $('weight_' + i).value.strip() || ! helper.is_num($('weight_' + i).value.strip()))
                {
                    alert('Packet weight is not number!');
                    return false;
                }
                params.set('weight_' + i, $('weight_' + i).value.strip());
                if ($('track_number_' + i).value.strip())
                {
                    if ($('track_number_' + i).value.length < 10)
                    {
                        alert('Track Number Error!');
                        return false;
                    }
                    params.set('track_number_' + i, $('track_number_' + i).value.strip());
                    if (track_numbers.indexOf($('track_number_' + i).value.strip()) >= 0)
                    {
                        alert('Can\'t have the same track number: ' + $('track_number_' + i).value.strip());
                        return false;
                    }
                    track_numbers[i] = $('track_number_' + i).value.strip();
                }
                
            }
        }
    }

    helper.show_loading();
    helper.ajax_reflesh(url, params);
    
    return false;
}

function give_order_back(current, url, order_id, is_customer)
{
    var remark = $('remark_' + order_id).value;
    var params = {
        order_id: order_id, 
        remark: remark, 
        is_customer: is_customer
    };

    if ( ! confirm('Are you sure?'))
    {
        return;
    }
    if (current)
    {
        helper.drop_row(current, '', url, params);
    }
    else
    {
        helper.ajax_reflesh(url, params);
    }
}

function force_change(current, url, order_id, is_customer)
{
    var params = {
        order_id: order_id, 
        is_customer: is_customer
    };

    if ( ! confirm('Are you sure?'))
    {
        return;
    } 
     
    helper.drop_row(current, '', url, params);
}

function instock_by_label(url)
{
    var params = $H({});

    if ($('bar_code').value.strip())
    {
        params.set('order_id', $('bar_code').value.strip());
    }
    else
    {
        alert('input bar code!');
        return false;
    }
    helper.ajax_reflesh(url, params, 'POST', 1);

    return true;
}
function select_orders(url)
{
    
    var params = $H({});
    if ($('item_no').value.strip())
    {
        params.set('item_no', $('item_no').value.strip());
    }
    else
    {
        alert('input item no!');
        return false;
    }
    
    helper.update_content(url, params, 'order_div');

    return true;
}

function give_order_back_shipping(url, order_id)
{
    if ( ! confirm('Are you sure?'))
    {
        return;
    }
    var params = $H({});
    params.set('weight', $('weight').value.strip());
    params.set('shipping_remark', $('shipping_remark').value.strip());
    params.set('is_register', $('is_register').value.strip());
    params.set('track_number', $('track_number').value.strip());
    params.set('order_id', order_id);

    helper.show_loading();
    helper.ajax_reflesh(url, params);

    return false;
}

function change_action(url)
{   
    form_atr = $("return_order_form");
    form_atr.action = url;
    return true;
}

function add_packet(base_url)
{
    var item_count = $('packet_count').value;
    var new_item = '<div style="margin: 0;">';
    var track_number_id = 'track_number_' + item_count;
    new_item += '<input type="text" size="10" maxlength="20" id="' + track_number_id + '" name="' + track_number_id + '">';
    new_item += '</div>';
    var item_div = 'track_number_div';
    $(item_div).insert(new_item);

    var new_item = '<div style="margin: 0;">';
    var item_id = 'weight_' + item_count;
    new_item += '<input type="text" size="10" maxlength="20" id="' + item_id + '" name="' + item_id + '">&nbsp;';
    new_item += helper.cancel_icon(base_url, '$(this.parentNode).remove();$(\'' + track_number_id + '\').remove();');
    new_item += '</div>';
    var item_div = 'weight_div';
    $(item_div).insert(new_item);
    $('packet_count').value = parseInt(item_count) + 1;
}


function modify_shipping(url, count, id)
{
    var params = $H({});
    params.set('id', id);
    params.set('count', count);

    if (count > 0)
    {
        for (var i = 0; i < count; i++)
        {
            if ($('shipping_weight_' + i + '_' + id))
            {
                if ( ! $('shipping_weight_' + i + '_' + id).value.strip() || ! helper.is_num($('shipping_weight_' + i + '_' + id).value.strip()))
                {
                    alert('Packet weight is not number!');
                    return false;
                }
                params.set('shipping_weight_' + i + '_' + id, $('shipping_weight_' + i + '_' + id).value.strip());
            }
        }
    }

    helper.ajax(url, params);

    return false;
}
