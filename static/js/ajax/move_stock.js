
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
    var html = '<div style="margin: 5px;">';
    html += 'SKU <input type="text" name="sku[]" value="" id="sku" maxlength="100" size="30"  /> QTY <input type="text" name="qty[]" value="" id="qty" maxlength="100" size="30"  />'
    html += helper.cancel_icon(base_url, '$(this.parentNode).remove();');
    html += '</div>';
    $('item_div').insert(html);
}

function delete_sku(link)
{
        $(link.parentNode).remove();    
}

function confirm_arrival_received(current, url, id)
{

    if ( ! confirm('Are you sure?'))
    {
        return;
    }
	var received_count = $('received_count_' + id).value;
	var params = $H({'received_count': received_count});
    if (current)
    {
        helper.drop_row(current, '', url, params);
    }
    else
    {
        helper.ajax_reflesh(url, params);
    }
}
