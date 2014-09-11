<?php

$head = array(
    array('text' => lang('product_makeup_sku'), 'sort_key' => 'makeup_sku', 'id' => 'product_makeup_sku'),
    array('text' => lang('sku'), 'sort_key' => 'sku'),
	array('text' => lang('qty_str'), 'sort_key' => 'sty'),
    array('text' => lang('editor'), 'sort_key' => 'u_name'),
    array('text' => lang('update_date'), 'sort_key' => 'update_date'),
    lang('options'),
);

$data = array();
foreach ($makeup_skus as $makeup_sku) {
    if ($action == 'edit') {
        $drop_button = $this->block->generate_drop_icon(
                        'sale/makeup_sku/drop_makeup_sku',
                        "{id: $makeup_sku->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('sale/makeup_sku/add_edit', array($makeup_sku->id)),'_blank');
        $url = $drop_button . $edit_button;
    } else {
//        $url = $this->block->generate_view_link(site_url('sale/netname/view', array($netname->id)));
    }
    $user_names = $this->user_model->get_user_name_by_id($makeup_sku->user_id);
    $data[] = array(  
        $makeup_sku->makeup_sku,
        $makeup_sku->sku ? $makeup_sku->sku : '' ,
		$makeup_sku->qty ? $makeup_sku->qty : '' ,
        $user_names,
        $makeup_sku->update_date,
        $url,
    );
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'makeup_sku',
    ),
    array(
        'type' => 'input',
        'field' => 'sku',
    ),
	NULL,
    array(
        'type'  => 'input',
        'field' => 'u.name'
    ),
    array(
        'type' => 'input',
        'field' => 'update_date',
    ),
);

echo block_header(lang('product_makeup_sku_manage'));

echo $this->block->generate_pagination('product_makeup_sku');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'product_makeup_sku');

echo form_close();

echo $this->block->generate_pagination('product_makeup_sku');
?>
