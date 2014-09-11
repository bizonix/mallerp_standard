function submit_content_by_purchase_apply(element, url)
{
    var form = $H($('purchase_apply_form').serialize(true));

    if ($('product_description'))
    {
        form = form.merge({product_description: tinyMCE.get('product_description').getContent()});
    }
    element.blur();
    
    return helper.ajax(url, form, 1);
}

function filter_purchase_list(url, url_key, value)
{
    if (value < 0)
    {
        url = url + url_key;
    }
    else
    {
        url = url + value + '/' + url_key;
    }
    window.location.href = url;

}


function update_qty(sku_url, s_id,id,purchase_qty,arrival_qty,clue)
{
    var qty = $(id).value;
    if(purchase_qty - arrival_qty < qty)
    {
        
        alert(clue);
//        if(confirm(clue))
//        {
//            helper.ajax_reflesh(sku_url, {id: s_id, value: qty},'post',0);
//        }
    }
    else
    {
        helper.ajax_reflesh(sku_url, {id: s_id, value: qty},'post',0);
    }
}

function update_purchase_order_status(url)
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
            params.set('purchase_order_id_' + order_count, order_id);
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