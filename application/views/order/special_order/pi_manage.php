<?php

$head = array(
    array('text' => lang('do_user'), 'sort_key' => 'user_id', 'id' => 'order_pi'),
    array('text' => lang('item_no'), 'sort_key' => 'order_id'),
    array('text' => lang('good_name'), 'sort_key' => 'sku_str'),
    array('text' => lang('quantity'), 'sort_key' => 'qty_str'),
    lang('order_option'),
    array('text' => lang('custom_date'), 'sort_key' => 'created_date'),
    lang('options'),
);

$data = array();

foreach ($order_pis as $order_pi) {
    $drop_button = $this->block->generate_drop_icon(
                    'order/special_order/drop_pi',
                    "{id: $order_pi->id}",
                    TRUE
    );

     $items_no = $this->order_model->get_order_item($order_pi->order_id);
     if($items_no){
         $show_item_no = $items_no->item_no;
     }else{
        $show_item_no = lang('no_order_item_no');
     }
     
    if (empty($order_pi->order_id)) {
       $url = anchor(site_url('order/pi_detail/pi_detail_list_before/', array($order_pi->pi_file_name)), lang('view'), array('title' => lang('view'), 'target' => '_blank'));
    } else {       
        $url = anchor(site_url('order/pi_detail/pi_detail_list/', array($order_pi->pi_file_name)), lang('view'), array('title' => lang('view'), 'target' => '_blank'));
    }
    $data[] = array(
        $order_pi->u_name,
        $show_item_no,
        $order_pi->sku_str,
        $order_pi->qty_str,
        $url,
        $order_pi->created_date,
        $drop_button,
    );
}

$title = lang('pi_manage');
echo block_header($title);

$filters = array(
    array(
        'type' => 'input',
        'field' => 'u.name',
    ),
    NULL,
    array(
        'type' => 'input',
        'field' => 'sku_str',
    ),
    array(
        'type' => 'input',
        'field' => 'qty_str',
    ),
    NULL,
    array(
        'type'      => 'date',
        'field'     => 'created_date',
        'method'    => 'from_to'
    ),
);
//if (isset($label_type) && $label_type = 'before_late_print_label')
//{
//    $filters[] = array(
//        'type'      => 'date',
//        'field'     => 'print_label_date',
//        'method'    => 'from_to'
//    );
//}
//else
//{
//    $filters[] = array(
//        'type'      => 'date',
//        'field'     => 'input_date',
//        'method'    => 'from_to'
//    );
//}

echo $this->block->generate_pagination('order_pi', array(), 'order_pi');

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order_pi');
echo form_close();

echo $this->block->generate_pagination('order_pi', array(), 'order_pi');
?>
