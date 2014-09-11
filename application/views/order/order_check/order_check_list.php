<?php

$head = array(
    lang('item_no'),
    lang('item_id_str'),
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

$data = array();

foreach ($orders as $order) {
    if ($action == 'edit') {
        $url = site_url('order/order_check/add', array($order->id, $table));
        $url_str =  '<a href='.$url.'>'.lang('order_check').'</a><br/>';
    } else {
        $url = $this->block->generate_view_link(site_url('order/order_check/add', array($order->id, $table)));
    }

    $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';

    $item_title_str = str_replace(',', '<br/>', $order->item_title_str);

    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($item_ids);

    $item_sku_html = '';
    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        if (strlen($item_ids[$i]) == 12)
        {
            $link = '<a target="_blank" href="' . $ebay_url . $item_ids[$i] . '">' . $item_ids[$i] .'</a>';
        }
        else
        {
            $link = $item_ids[$i];
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

    $data[] = array(
        $order->item_no,
        $order->item_id_str,
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


$title = lang('order_check');
echo block_header($title);


echo form_open();

echo $this->block->generate_table($head, $data);

echo form_close();

?>
