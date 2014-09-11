<?php
$options_status = array(
    'waiting_for_testing'       =>          lang('waiting_for_testing'),
    'perfect'                   =>          lang('perfect'),
    'repairing'                 =>          lang('repairing'),
    'fixed'                     =>          lang('fixed'),
    'beyond_repair'             =>          lang('beyond_repair'),
    'warehousing'               =>          lang('warehousing'),
);

$options_cause = array(
    'quality_problems'          =>          lang('quality_problems'),
    'address_unknown'           =>          lang('address_unknown'),
    'request_for_replacement'   =>          lang('request_for_replacement'),
    'customer_reject'           =>          lang('customer_reject'),
    'prohibit_importing'        =>          lang('prohibit_importing'),
    'address_not_arrived'       =>          lang('address_not_arrived'),
    'customer_removal'          =>          lang('customer_removal'),
);

$collection_cause = to_js_array($options_cause);
$collection_status = to_js_array($options_status);

$head = array(
    array('text' => lang('recommend_number_and_time'), 'sort_key' => 'recommend_no', 'id' => 'recommend'),
    lang('old_order_address_info'),
    lang('old_order_list_and_money'),
    array('text' => lang('receive_product_list'), 'sort_key' => 'sku_str'),
    array('text' => lang('recommend_cause'), 'sort_key' => 'cause'),
    array('text' => lang('recommend_remark'), 'sort_key' => 'remark'),
    array('text' => lang('recommend_status'), 'sort_key' => 'status'),
    array('text' => lang('creator'), 'sort_key' => 'creator'),
    array('text' => lang('created_date'), 'sort_key' => 'created_date'),
    lang('options'),
);

$data = array();

foreach ($recommends as $recommend) {
//    $order = get_order_info($recommend->order_id);
    $drop_button = $this->block->generate_drop_icon(
                    'qt/recommend/drop_recommend',
                    "{id: $recommend->rid}",
                    TRUE
    );

    $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
    $skus = explode(',', $recommend->r_sku_str);
    $qties = explode(',', $recommend->r_qty_str);
    $count = count($skus);

    $item_sku_html = '';
    $item_sku_html .= "<div>";
    for ($i = 0; $i < $count; $i++)
    {
        $item_sku_html .= '<div style="margin-top: 5px;">';
        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . '<br/>';
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';

$product_info =<<<PRODUCT
    <div style='padding: 10px;'>
    $item_sku_html
    </div>
PRODUCT;

    $old_order_info = '';
    $old_product_info = '';
//if($order)
//{
$old_order_info = <<<ORDER
    订单编号 :  $recommend->item_no <br>
    订单地址 :  $recommend->address_line_1 <br>
    重量 : $recommend->ship_weight (g) <br>
    追踪号 : $recommend->transaction_id <br>
ORDER;

    $item_title_str = str_replace(',', '<br/>', $recommend->item_title_str);
    $item_ids = explode(',', $recommend->item_id_str);
    $skus = explode(',', $recommend->sku_str);
    $qties = explode(',', $recommend->qty_str);
    $count = count($skus);

    $old_item_sku_html = '';
    $old_item_sku_html .= "<div id='item_div_$recommend->order_id'>";
    for ($i = 0; $i < $count; $i++)
    {
        if ( ! isset($item_ids[$i]))
        {
            $link = '';
        }
        else if (strlen($item_ids[$i]) == 12)
        {
            $link = '<a target="_blank" href="' . $ebay_url . $item_ids[$i] . '">' . $item_ids[$i] .'</a>';
        }
        else
        {
            $link = $item_ids[$i];
        }
        $old_item_sku_html .= '<div style="margin-top: 5px;">';
        $old_item_sku_html .= "Item ID: $link<br/>";
        $old_item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . '<br/>';
        $old_item_sku_html .= $recommend->gross .'('.$recommend->currency. ')'  . '</div>';
    }
    $old_item_sku_html .= '</div>';

$old_product_info =<<<PRODUCT
    <div style='padding: 10px;'>
    $item_title_str<br/>
    $old_item_sku_html
    </div>
PRODUCT;
//}

$instant_url= site_url('qt/recommend/instant_save_recommend_order');

$recommend_status_html = $this->block->generate_div("status_{$recommend->rid}", empty($recommend->status) ?  '[edit]' : lang($recommend->status));

if($recommend->status ==='warehousing')
{
    $recommend_status_html = lang('warehousing');
}

    $data[] = array(
        $this->block->generate_div("recommend_no_{$recommend->rid}", empty($recommend->recommend_no) ? '' : $recommend->recommend_no).'<br/>'.$recommend->email_time,
        $old_order_info,
        $old_product_info,
        $product_info,
        $this->block->generate_div("cause_{$recommend->rid}", empty($recommend->cause) ?  '[edit]' : lang($recommend->cause)),
        $this->block->generate_div("remark_{$recommend->rid}", empty($recommend->remark) ?  '[edit]' : $recommend->remark),
        $recommend_status_html,
        $recommend->creator,
        $recommend->created_date,
        $drop_button,
    );

    $user_priority = $this->user_model->fetch_user_priority_by_system_code('qt');

    if($user_priority >= 2)
    {
        echo $this->block->generate_editor(
            "recommend_no_{$recommend->rid}",
            'cause_orders_form',
            $instant_url,
            "{id: $recommend->rid, type: 'recommend_no'}"
        );
    }

    echo $this->block->generate_editor(
        "cause_{$recommend->rid}",
        'cause_orders_form',
        $instant_url,
        "{id: $recommend->rid, type: 'cause'}",
        "$collection_cause"
    );
    echo $this->block->generate_editor(
        "remark_{$recommend->rid}",
        'remark_orders_form',
        $instant_url,
        "{id: $recommend->rid, type: 'remark'}"
    );
    echo $this->block->generate_editor(
        "status_{$recommend->rid}",
        'status_orders_form',
        $instant_url,
        "{id: $recommend->rid, type: 'status'}",
        "$collection_status"
    );
}

$options_status = array(
    ''                          =>          lang('please_select'),
    'waiting_for_testing'       =>          lang('waiting_for_testing'),
    'perfect'                   =>          lang('perfect'),
    'repairing'                 =>          lang('repairing'),
    'fixed'                     =>          lang('fixed'),
    'beyond_repair'             =>          lang('beyond_repair'),
    'warehousing'               =>          lang('warehousing'),
);

$options_cause = array(
    ''                          =>          lang('please_select'),
    'quality_problems'          =>          lang('quality_problems'),
    'address_unknown'           =>          lang('address_unknown'),
    'request_for_replacement'   =>          lang('request_for_replacement'),
    'customer_reject'           =>          lang('customer_reject'),
    'prohibit_importing'        =>          lang('prohibit_importing'),
    'address_not_arrived'       =>          lang('address_not_arrived'),
    'customer_removal'          =>          lang('customer_removal'),
);

$filters = array(
    array(
        'type' => 'input',
        'field' => 'orl.recommend_no|orl.email_time',
    ),
    array(
        'type' => 'input',
        'field' => 'ol.item_no|ol.transaction_id',
    ),
    array(
        'type' => 'input',
        'field' => 'ol.sku_str|ol.qty_str|ol.item_id_str',
    ),
    array(
        'type' => 'input',
        'field' => 'orl.sku_str',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'cause',
        'options' => $options_cause,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'remark',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'status',
        'options' => $options_status,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'creator',
    ),
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);

echo block_header(lang('recommend_service_list_manage'));

echo $this->block->generate_pagination('recommend');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'recommend');

echo form_close();

echo $this->block->generate_pagination('recommend');
?>
