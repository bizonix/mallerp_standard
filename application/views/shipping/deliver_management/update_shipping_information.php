
<?php

$error_notice = lang('error_notice');
$error_information = NULL;
if ( ! $order)
{
    $error_information = lang('order_not_exists');
}
else
{
    $status_name = get_order_status_name($order->order_status);
    if ($status_name != 'wait_for_shipping_confirmation' && $status_name !== 'wait_for_feedback')
    {
        $error_information = lang('order_status_is') . lang($status_name);
    }
}
$return = lang('return');
$reflesh_page = lang('please_reflesh_page');
if ($error_information)
{
    $html =<<<HTML
<center>
<div align="center" style="
    background-color: #FBE6F2;
    border: 1px solid #D893A1;
    padding: 10px 10px 10px 10px;
    width: 300px;">
    $error_notice: $error_information
    <br/><br/>
    $reflesh_page<span id="return" style="text-decoration:underline; cursor:pointer;" onClick="location.reload();">$return</span>
</div>
</center>
HTML;
}
else {
    $html = '';
        $update_shipping_information = sprintf(lang('update_shipping_information_detail'), lang($status_name));
        $html .=<<<HTML
<center>
<div align="center" style="
    background-color: #FBE6F2;
    border: 1px solid #D893A1;
    padding: 10px 10px 10px 10px;
    width: 300px;">
    $update_shipping_information
</div>
</center>
HTML;
    $head = array(
        lang('item_no'),
        lang('is_register'),
        lang('track_number'),
        lang('weight'),
        lang('remark'),
        lang('product_information'),
        lang('customer_information'),
        lang('options'),
    );

    $row = array();
    $row[] = $order->item_no;
    $config = array(
          'name'        => 'is_register',
          'id'          => 'is_register',
          'value'       => $order->is_register,
          'size'        => '4',
    );
    // TODO: update 'xxxxx' to real weight
    $row[] = sprintf(lang('estimated_weight_suggest_shipping_type'), 'xxxxx') . form_input($config);
    
    $config = array(
          'name'        => 'track_number',
          'id'          => 'track_number',
          'value'       => $order->track_number,
          'size'        => '20',
    );
    $row[] = form_input($config);

    $config = array(
          'name'        => 'weight',
          'id'          => 'weight',
          'value'       => $order->ship_weight,
          'size'        => '10',
    );
    $row[] = form_input($config);

    $config = array(
        'name'        => 'shipping_remark',
        'id'          => 'shipping_remark',
        'rows'        => '2',
        'cols'        => '14',
        'value'       => $order->descript,
    );
    $row[] = form_textarea($config);

    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($skus);
    $item_sku_html = '';
    for ($i = 0; $i < $count; $i++)
    {
        $item_sku_html .= '<div style="margin: 5px;">';
        $item_sku_html .=  $skus[$i] . '【' . get_product_name($skus[$i]) . '】';
        $item_sku_html .=  ' Qty: ' . $qties[$i];
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';

    $edit_button = block_edit_link(site_url('order/regular_order/edit_customer_info', array($order->id)), TRUE);

    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_sku_html<br/>
</div>
PRODUCT;
    $row[] = $product_info;

    $name = lang('receiver');
    $town_city = lang('town_city');
    $state_province = lang('state_province');
    $country = lang('country');
    $zip_code = lang('zip_code');
    $address = lang('address');

    $customer_information = <<< CUSTOMER
<div style='padding: 10px;'>
$name: $order->name<br/>
$address: $order->address_line_1  $order->address_line_2<br/>
$town_city: $order->town_city<br/>
$state_province: $order->state_province<br/>
$country: $order->country<br/>
$zip_code: $order->zip_code $edit_button<br/>
</div>
CUSTOMER;
    $row[] = $customer_information;

    $give_back_url = site_url('shipping/deliver_management/give_order_back_to_shipping');
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('give_order_back_to_shipping'),
        'onclick' => "give_order_back_shipping('$give_back_url', $order->id);",
    );
    $give_back = block_button($config);
    $row[] = $give_back;

    $data[] = $row;
    
    $html .= $this->block->generate_table($head, $data);
}

echo $html;

echo block_notice_div(lang('note') . ": <br/>" . lang('update_shipping_information_note'));
?>

<script>
    helper.focus('weight');
    helper.focus('return');
</script>
