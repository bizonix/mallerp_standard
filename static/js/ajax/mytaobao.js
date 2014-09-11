function confirm_review(url, rate_id, order_id, current)
{
    var remark = $('remark_' + rate_id).value;
    var bad_type = $('comment_type_' + rate_id).value;
    var stock_type = $('stock_type_' + rate_id).value;
    var user = $('user_' + rate_id).value
    var refund_sku_str = '';

    
    if(order_id)
    {
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
        }
    }   
    else
    {
        var refund_sku_str = $('sku_' + rate_id).value;
    }
    
    var params = {rate_id: rate_id, remark: remark, bad_type: bad_type, stock_type: stock_type, user: user, sku: refund_sku_str};
    if( ! bad_type)
    {
        alert('Please select comment type!');
        return;
    }
   
    if ( ! confirm('Are you sure?'))
    {
        return;
    }
    
//    helper.ajax_reflesh(url, params, 'POST', 1);
    helper.drop_row(current, '', url, params);
}