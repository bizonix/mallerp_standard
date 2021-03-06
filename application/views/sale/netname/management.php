<?php

$head = array(
    array('text' => lang('net_name'), 'sort_key' => 'net_name', 'id' => 'product_net_name'),
    array('text' => lang('sku'), 'sort_key' => 'sku'),
    array('text' => lang('shipping_code'), 'sort_key' => 'shipping_code'),
    array('text' => lang('editor'), 'sort_key' => 'u_name'),
    array('text' => lang('update_date'), 'sort_key' => 'update_date'),
    lang('options'),
);

$data = array();
foreach ($netnames as $netname) {
    if ($action == 'edit') {
        $drop_button = $this->block->generate_drop_icon(
                        'sale/netname/drop_netname',
                        "{id: $netname->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('sale/netname/add_edit', array($netname->id)),'_blank');
        $url = $drop_button . $edit_button;
    } else {
//        $url = $this->block->generate_view_link(site_url('sale/netname/view', array($netname->id)));
    }
    $user_names = $this->user_model->get_user_name_by_id($netname->user_id);
    $data[] = array(  
        $netname->net_name,
        $netname->sku ? get_status_image($netname->sku) . $netname->sku : '' ,
        $netname->shipping_code,
        $user_names,
        $netname->update_date,
        $url,
    );
}

$shipping_codes = array('' => lang('please_select'));

foreach ($ship_codes as $ship_code)
{
    $shipping_codes[$ship_code->code] = $ship_code->code;
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'net_name',
    ),
    array(
        'type' => 'input',
        'field' => 'sku',
    ), 
    array(
        'type' => 'dropdown',
        'field' => 'shipping_code',
        'options' =>  $shipping_codes,
        'method' => '=',
    ),
    array(
        'type'  => 'input',
        'field' => 'u.name'
    ),
    array(
        'type' => 'input',
        'field' => 'update_date',
    ),
);

echo block_header(lang('product_net_name_manage'));

echo $this->block->generate_pagination('product_net_name');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'product_net_name');

echo form_close();

echo $this->block->generate_pagination('product_net_name');
?>
