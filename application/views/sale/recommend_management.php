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

$options_finish = array(
    'wait_for_proccess'                     =>          lang('wait_for_proccess'),
    'resend_new_ones_and_close'             =>          lang('resend_new_ones_and_close'),
    'send_fixed_item_back_and_close'        =>          lang('send_fixed_item_back_and_close'),
    'refund_and_close'                      =>          lang('refund_and_close'),
    'other_situations_close'                =>          lang('other_situations_close'),
);

$collection_cause = to_js_array($options_cause);
$collection_status = to_js_array($options_status);
$collection_finish = to_js_array($options_finish);

$head = array(
    array('text' => lang('recommend_number_and_time'), 'sort_key' => 'recommend_no', 'id' => 'recommend'),
    array('text' => lang('old_order_address_info'), 'sort_key' => 'order_id'),
    array('text' => lang('old_order_list_and_money'), 'sort_key' => 'order_id'),
    array('text' => lang('receive_product_list'), 'sort_key' => 'sku_str'),
    array('text' => lang('recommend_cause'), 'sort_key' => 'cause'),
    array('text' => lang('sys_remark')),
    array('text' => lang('ship_remark')),
    array('text' => lang('recommend_remark'), 'sort_key' => 'remark'),
    array('text' => lang('recommend_status'), 'sort_key' => 'status'),
    lang('options'),
);

$data = array();

foreach ($recommends as $recommend) {
//    $order = get_order_info($recommend->order_id);

    $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
    $skus = explode(',', $recommend->sku_str);
    $qties = explode(',', $recommend->qty_str);
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
    $count = count($item_ids);

    $item_sku_html = '';
    $item_sku_html .= "<div id='item_div_$recommend->order_id'>";
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
        $item_sku_html .= $recommend->gross .'('.$recommend->currency. ')'  . '</div>';
    }
    $item_sku_html .= '</div>';

$old_product_info =<<<PRODUCT
    <div style='padding: 10px;'>
    $item_title_str<br/>
    $item_sku_html
    </div>
PRODUCT;
//}

$instant_url= site_url('sale/recommend/instant_save_recommend_order');

    $data_value = array(
        $recommend->recommend_no.'<br/>'.$recommend->email_time,
        $old_order_info,
        $old_product_info,
        $product_info,
        $this->block->generate_div("cause_{$recommend->rid}", empty($recommend->cause) ?  '[edit]' : lang($recommend->cause)),
        $recommend->sys_remark,
        $recommend->ship_remark,
        $this->block->generate_div("remark_{$recommend->rid}", empty($recommend->remark) ?  '[edit]' : $recommend->remark),
        lang($recommend->status),
    );

    if($recommend->status =='perfect' || $recommend->status =='fixed' ||$recommend->status =='beyond_repair' ||$recommend->status =='warehousing' )
    {
        $data_value[] = $this->block->generate_div("finish_status_{$recommend->rid}", empty($recommend->finish_status) ?  lang('wait_for_proccess') : lang($recommend->finish_status));
    }
    else
    {
        $data_value[] ='';
    }

    $data[] = $data_value;
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
    echo $this->block->generate_editor(
        "finish_status_{$recommend->rid}",
        'finish_status_orders_form',
        $instant_url,
        "{id: $recommend->rid, type: 'finish_status'}",
        "$collection_finish"
    );
}

$options_status = array(
    ''                          =>          lang('all'),
    'waiting_for_testing'       =>          lang('waiting_for_testing'),
    'perfect'                   =>          lang('perfect'),
    'repairing'                 =>          lang('repairing'),
    'fixed'                     =>          lang('fixed'),
    'beyond_repair'             =>          lang('beyond_repair'),
    'warehousing'               =>          lang('warehousing'),
);

$options_cause = array(
    ''                          =>          lang('all'),
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
        'field' => 'ol.sys_remark',
    ),   
    array(
        'type' => 'input',
        'field' => 'orl.descript',
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
        'type' => 'dropdown',
        'field' => 'finish_status',
        'options' => $options_finish,
        'method' => '=',
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
