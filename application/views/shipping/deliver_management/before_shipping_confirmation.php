<?php
$base_url = base_url();

$error_notice = lang('error_notice');
$error_information = NULL;
if ( ! $order)
{
    $error_information = lang('order_not_exists');
}
else
{
    $status_name = get_order_status_name($order->order_status);
    if ($order->order_status != 0 && $status_name !== 'wait_for_shipping_confirmation')
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
    $head = array(
        lang('is_register'),
        lang('track_number'),
        lang('weight'),
        lang('remark'),
        lang('product_information'),
        lang('customer_information'),
        lang('options'),
    );

    $row = array();
    $row[] = form_dropdown('is_register', $local_shipping, $order->is_register,'id = is_register');
    
    $config = array(
          'name'        => 'track_number_0',
          'id'          => 'track_number_0',
          'value'       => '',
          'size'        => '10',
    );
    $row[] = "<div id='track_number_div'>" . form_input($config) . "</div>";
    $gift = lang('mouse_pad_gift');
    if (strpos($order->descript, $gift) !== FALSE)
    {
        $shipping_weight += 22;
    }
    $config = array(
          'name'        => 'weight_0',
          'id'          => 'weight_0',
          'value'       => $shipping_weight,
          'size'        => '10',
    );
    $row[] = "<div id='weight_div'>" . form_input($config) . block_add_icon_only("add_packet('$base_url');") . "</div>";

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
Item No: {$order->item_no} <br/><br/>
$name: $order->name<br/>
$address: $order->address_line_1  $order->address_line_2<br/>
$town_city: $order->town_city<br/>
$state_province: $order->state_province<br/>
$country: $order->country<br/>
$zip_code: $order->zip_code<br/><br/>
</div>
CUSTOMER;
    $row[] = $customer_information;

    $give_back_url = site_url('shipping/deliver_management/give_order_back');
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('give_order_back'),
        'onclick' => "give_order_back(0, '$give_back_url', $order->id, 0);",
    );
    $give_back = block_button($config);
    $config = array(
        'name'        => 'remark_' . $order->id,
        'id'          => 'remark_' . $order->id,
        'rows'        => '2',
        'cols'        => '14',
    );
    $give_back .= '<br/>' . form_textarea($config);
    $row[] = $give_back;

    $data[] = $row;
    $url = site_url('shipping/deliver_management/make_order_shipped');
    $js = "onClick=\"return confirm_shipping('$url');return false;\"";
    $html = '';

    $config = array(
        'name' => 'packet_count',
        'id' => 'packet_count',
        'value' => 1,
        'type' => 'hidden',
    );

    $html .= form_input($config);
    $html .= form_open();
    $html .= $this->block->generate_table($head, $data);
    $html .= '<center>' . '<br/>' . form_submit('confirm_shipped', lang('confirm_shipped'), $js) . '</center>';
    $html .= "<input type='hidden' name='order_id' id='order_id' value='$order->id' />";
    $html .= form_close();
}

echo $html;
?>

<script>
    helper.focus('weight_0');
    helper.focus('return');
</script>
