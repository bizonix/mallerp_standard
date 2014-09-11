<?php
$head = array(
    lang('name'),
    lang('value'),
);

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
        $config = array(
            'name'        => 'submit',
            'value'       => lang('delete'),
            'type'        => 'button',
            'onclick'     => "delete_sku('sku_$skus[$i]');",
        );

        $item_sku_html .= "<div id='sku_$skus[$i]' style='margin-top: 5px;'>";

        $qty = element($i, $qties);
        $item_sku_html .=  '<input name="sku[]" type="hidden" value="'.$skus[$i].'" />'.' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . '<input type="text" name="qty[]" value="'.$qty.'" />' . ' (' . get_product_name($skus[$i]) . ')' : '');
        $item_sku_html .= block_button($config) . '<br/>' . block_image(get_product_image($skus[$i]))  ;
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';

$product_info =<<<PRODUCT
    <div style='padding: 10px;'>
    $item_sku_html
    </div>
PRODUCT;


$data = array(
    array(
        lang('item_number'),
        $order->item_no,
    ),
    array(
        lang('item_id_str'),
        $order->item_id_str,
    ),
    array(
        lang('name'),
        $order->name,
    ),
    array(
        lang('buyer_id'),
        $order->buyer_id,
    ),
    array(
        lang('address_line_1'),
        $order->address_line_1,
    ),
    array(
        lang('address_line_2'),
        $order->address_line_2,
    ),
    array(
        lang('town_city'),
        $order->town_city,
    ),
    array(
        lang('state_province'),
        $order->state_province,
    ),
    array(
        lang('country'),
        $order->country,
    ),
    array(
        lang('zip_code'),
        $order->zip_code,
    ),
    array(
        lang('contact_phone_number'),
        $order->contact_phone_number,
    ),
    array(
        lang('is_register'),
        $order->is_register,
    ),
    array(
        lang('product_list'),
        $product_info,
    ),
    array(
        lang('net'),
        $order->net. '('.$order->currency. ')' ,
    ),
    array(
        lang('transaction_id'),
        $order->transaction_id,
    ),
    array(
        lang('description'),
        $order->descript,
    ),
    array(
        lang('ship_remark'),
        $order->ship_remark,
    ),
    array(
        lang('item_no'),
        $order->item_no,
    ),
);

$config = array(
    'name'        => 'sys_remark',
    'value'       => $order->sys_remark,
    'cols'=>90,
    'rows'=>3,
    'readonly' => 'ture'
);
$data[] = array(
    lang('sys_remark'),
    form_textarea($config),
);

$options_status = array(
    'waiting_for_testing'       =>          lang('waiting_for_testing'),
    'perfect'                   =>          lang('perfect'),
    'repairing'                 =>          lang('repairing'),
    'fixed'                     =>          lang('fixed'),
    'beyond_repair'             =>          lang('beyond_repair'),
//    'warehousing'               =>          lang('warehousing'),
);

$options_cause = array(
    'quality_problems'          =>          lang('quality_problems'),
    'address_unknown'           =>          lang('address_unknown'),
    'request_for_replacement'   =>          lang('request_for_replacement'),
    'customer_reject'           =>          lang('customer_reject'),
    'prohibit_importing'        =>          lang('prohibit_importing'),
    'address_not_arrived'       =>          lang('address_not_arrived'),
    'customer_removal'          =>          lang('customer_removal'),
    'unclaimed'                 =>          lang('unclaimed'),
    'undeliverable'             =>          lang('undeliverable'),
    'customer_returned'         =>          lang('customer_returned'),
    'no_send'                   =>          lang('no_send'),
    'other'                     =>          lang('other'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('recommend_status')),
    form_dropdown(
        'recommend_status',
        $options_status,
        'waiting_for_testing'
    ),
);

$config = array(
      'name'        => 'recommend_no',
      'id'          => 'recommend_no',
//      'value'       => 'R-' . date('ymd') .'-'. substr($order->item_no, 3),//$keyword ? $keyword->link_url : '',
      'maxlength'   => '90',
      'size'        => '35',
);
$data[] = array(
    $this->block->generate_required_mark(lang('recommend_no')),
    form_input($config),
);

$data[] = array(
    $this->block->generate_required_mark(lang('recommend_cause')),
    form_dropdown(
        'recommend_cause',
        $options_cause,
        'quality_problems'
    ),
);

$config = array(
      'name'        => 'email_time',
      'id'          => 'email_time',
      'value'       => date('Y-m-d h:m:s'),//$keyword ? $keyword->keyword : '',
      'maxlength'   => '90',
      'size'        => '25',
);
$data[] = array(
    $this->block->generate_required_mark(lang('recommend_email_time')),
    form_input($config),
);

$config = array(
      'name'        => 'recommend_remark',
      'id'          => 'recommend_remark',
    'cols'=>90,
    'rows'=>3,
);
$data[] = array(
    lang('recommend_remark'),
    form_textarea($config),
);

if ($recommend)
{
    $title = lang('recommend_service_list_edit');
}
else
{
    $title = lang('recommend_service_list_add');
}

echo block_header($title);

$attributes = array(
    'id' => 'recommend_form',
);

$url = site_url('qt/recommend/save_recommend');
$redirect_url = site_url('qt/recommend/search');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('save_recommend_list'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();ajax_redirect_qt('$url',$('recommend_form').serialize(true), '$redirect_url');",
);
echo form_hidden('order_id', $order->id);
echo form_hidden('recommend_id', $recommend ? $recommend->id : '-1');
echo '<h2>'.block_button($config).'</h2>';
echo form_close();

?>


<script>

function delete_sku(id)
{
    $(id).innerHTML = '';
    return;
}

</script>