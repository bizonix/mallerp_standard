<?php
$head = array(
    lang('item_number').'\\'.lang('item_id_str'),
    lang('name'),
    lang('buyer_id'),
    lang('address_line_1'),
    lang('town_city'),
    lang('state_province'),
    lang('country'),
    lang('transaction_id'),
    lang('product_information'),
    lang('options'),
);

if($tag == 'shiping_edit_number')
{
    $head[count($head)-3] = lang('track_number');
    unset($head[count($head)-1]);
    $head[] = lang('shipping_weight');
}

$data = array();
foreach ($orders as $order) {
    if ($tag == 'qt')
    {
        if( ! empty ($order->order_id))
        {
            $url = site_url('qt/recommend/add_edit_recommend', array($order->id, 'completed_table'));
        }
        else
        {
            $url = site_url('qt/recommend/add_edit_recommend', array($order->id, 'order_table'));
        }
        $url_str =  '<a href='.$url.'>'.lang('recommend').'</a><br/>';
    } 
    elseif($tag == 'order')
    {
        $url = site_url('order/regular_order/copy', array($order->id));
        $url_str =  '<a href='.$url.'>'.lang('copy_order').'</a><br/>';
    }
   

    $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
    
    $item_title_str = str_replace(',', '<br/>', $order->item_title_str);

    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($skus);
    
    $item_sku_html = '';
    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        $link = element($i, $item_ids, '');
        if (strlen($link) == 12)
        {
            $link = '<a target="_blank" href="' . $ebay_url . $item_ids[$i] . '">' . $item_ids[$i] .'</a>';
        }
        $item_sku_html .= '<div style="margin-top: 5px;">';
        $item_sku_html .= "Item ID: $link<br/>";
        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . '<br/>';
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    
$product_info =<<<PRODUCT
    <div style='padding: 10px;'>
    $item_title_str<br/>
    $item_sku_html
    </div>
PRODUCT;
    if($tag == 'qt' || $tag == 'order')
    {
        $data[] = array(
            $order->item_no.'<br/><br/>'.$order->item_id_str,
            $order->name,
            $order->buyer_id,
            $order->address_line_1,
            $order->town_city,
            $order->state_province,
            $order->country,
            $order->transaction_id,
            $product_info,
            $url_str,
        );
    }

    $url_number = site_url('shipping/shipping_code/verify_track_number',array('track_number'));

    $number_id = 'track_number_' . $order->id;
    $config_number = array(
        'name'        => $number_id,
        'id'          => $number_id,
        'value'       => ! empty($order->track_number) ? $order->track_number : '',
        'maxlength'   => '100',
        'size'        => '30',
    );
    $track_number_html = form_input($config_number);
    $config_save_number = array(
            'name'        => 'save_track_number',
            'value'       => lang('save'),
            'type'        => 'button',
            'onclick'     => "helper.ajax('$url_number', {id: $order->id, track_number: \$('$number_id').value}, 1);",
        );
    $track_number_html .= form_input($config_save_number);


    $url_weight = site_url('shipping/shipping_code/verify_track_number', array('ship_weight'));

    $sub_ship_weight_arr = explode(',', $order->sub_ship_weight_str);

 
    $count = count($sub_ship_weight_arr);
    if($count == 1) {
        $config_weight = array(
            'name'        => 'shipping_weight_0' . '_'.$order->id,
            'id'          => 'shipping_weight_0' . '_'.$order->id,
            'value'       => ! empty($order->sub_ship_weight_str) ? $order->sub_ship_weight_str : '',
            'maxlength'   => '100',
            'size'        => '30',
        );
        $shipping_weight_html = form_hidden($config_weight);
    }
 
    $shipping_weight_html = null;
    foreach($sub_ship_weight_arr as $key => $value)
    {
        $config_weight = array(
            'name'        => 'shipping_weight_' . $key.'_'.$order->id,
            'id'          => 'shipping_weight_' . $key.'_'.$order->id,
            'value'       => ! empty($value) ? $value : '',
            'maxlength'   => '100',
            'size'        => '30',
        );
        $weight_id = "\$('shipping_weight_' + $key +'_' + $order->id).value";
        $shipping_weight_html .= form_input($config_weight).  br();
    }
    $config_save_weight = array(
            'name'        => 'save_shipping_weight',
            'value'       => lang('save'),
            'type'        => 'button',
            'onclick'     => "modify_shipping('$url_weight', '$count', '$order->id');",
        );
    $shipping_weight_html .= form_input($config_save_weight);

    if($tag == 'shiping_edit_number')
    {
        $data[] = array(
            $order->item_no.'<br/><br/>'.$order->item_id_str,
            $order->name,
            $order->buyer_id,
            $order->address_line_1,
            $order->town_city,
            $order->state_province,
            $order->country,
            $track_number_html,
            $product_info,
            $shipping_weight_html,
        );
    }
}

if($tag == 'qt')
{
    $title = lang('recommend_service_list');
}
elseif($tag == 'order')
{
    $title = lang('copy_order');
}
elseif($tag == 'shiping_edit_number')
{
    $title = lang('edit_number_and_weight');
}

echo block_header($title);

echo form_open();

echo $this->block->generate_table($head, $data);

echo form_close();

?>
