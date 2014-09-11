<?php

$head = array(
    lang('delay_times'),
    lang('item_information'),
    lang('product_information'),
    lang('customer_remark'),
    lang('touching'),
);

$data = array();
$product_skus = array();
$product = NULL;
foreach($orders as $order)
{
    $delay_time = secs_to_readable($order->delay_times);
    $item_html = '<div>';
    $item_html .= '<div style="margin-top: 5px;">'.$order->item_no.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('name') .': '. $order->name.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('country') .': '. $order->country.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('zip_code') .': '. $order->zip_code.'</div>';
    $item_html .= '</div>';
    $sku_html = '<div>';
    $skus = explode(',', $order->sku_str);
    $qtys = explode(',', $order->qty_str);
    $item_ids = explode(',', $order->item_id_str);
    $count = count($skus);
    for($i = 0; $i < $count; $i++)
    {
        if(!in_array($skus[$i], $product_skus))
        {
            $product = $this->purchase_order_model->fetch_product_by_sku($skus[$i]);
            $product_skus[$skus[$i]] = $product;
        }
        else
        {
            $product = $product_skus[$skus[$i]];
        }
        $purchaser = get_purchaser_name_by_sku($skus[$i]);
        $sku_html .= '<div style="margin-top: 5px;">';
        if($product->dueout_count + $product->min_stock_number -($product->stock_count + on_way_count($skus[$i])) > 0)
        {
            $sku_html .= lang('sku').': <font color = red >' . get_status_image($skus[$i]). $skus[$i]  . '</font>';
        }
        else
        {
             $sku_html .=  lang('sku') .': ' . get_status_image($skus[$i]) . $skus[$i] ;
        }
        $sku_html .=  ' '.lang('qty_str') . ':' . $qtys[$i]  .  ' ' . $product->name_cn .' ' ;
        if($product->stock_count + on_way_count($skus[$i]) < $product->dueout_count + $product->min_stock_number)
        {
             $sku_html .= '<font color = red >' . $purchaser . '</font>';
        }
        else
        {
            $sku_html .= $purchaser;
        }
        $sku_html .=  ' ' . lang('storage_warning').': ' . $product->min_stock_number;
        $sku_html .=  ' ' . lang('7-days_sales_amounts').': ' . $product->sale_in_7_days;
        $sku_html .=  ' ' . lang('30-days_sales_amounts').': ' . $product->sale_in_30_days;
        $sku_html .=  ' ' . lang('60-days_sales_amounts').': ' . $product->sale_in_60_days;
        $sku_html .= '</div>';

    }
    $sku_html .= '</div>';
    $data[] = array(
        $delay_time['days'].lang('day').'<br>'.$delay_time['hours'].lang('hour'),
        $item_html,
        $sku_html,
       "<abbr title='$order->descript'>". word_limiter($order->descript,6)."</abbr>",
        $order->input_user,
    );
}

echo block_header(lang('for_the_qt_orders'));
echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$all_purchasers = $this->user_model->fetch_all_purchase_users();
$purchasers = array('0' => lang('all_purchasers'));
foreach ($all_purchasers as $purchaser)
{
    $purchasers[$purchaser->u_id] = $purchaser->u_name;
}

echo form_dropdown('purchaser', $purchasers, $current_purchaser);

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

$sortable = array(
    'default',
    'default',
    'default',
    'default',
    'default',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");



?>
