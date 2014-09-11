<?php

$head = array(
	lang('ship_order_no'),
    lang('sku'),
    lang('box_contain_number'),
    lang('processing_status'),
    lang('storage_code'),
    lang('created_date'),
	lang('instock_num'),
    lang('options'),
);

$data = array();

foreach ($move_stock_lists as $move_stock_list)
{
    if ($move_stock_list->status == 0)
    {
        
        $url = site_url('stock/move_stock/confirm_arrival_received', array($move_stock_list->id));
		//$url = site_url('stock/move_stock/confirm_arrival_received');
		$config = array(
			'name'		=>'name',
			'value'     => lang('confirm_arrival'),
			'type'		=>'button',
			'onclick' => "confirm_arrival_received(this, '$url',$move_stock_list->id);",
			);
		$edit_button = '';
		$edit_button .= block_button($config);
		
		$config = array(
						'type'      => 'input',
						'name'        => 'received_count_' . $move_stock_list->id,
						'id'          => 'received_count_' . $move_stock_list->id,
						'maxlength'   => '50',
						'size'        => '14',
						'value'		=>$move_stock_list->qty_str,
						);
		$edit_button .= '<br/>'. lang('instock_num') . ' ' . form_input($config). '<br/>' .lang('multi_products_seperate_by_comma');
    }
    else
    {
        $edit_button='';
    }

    if($move_stock_list->status == 0)
    {
        $status = lang('in_transit');
    }
    elseif($move_stock_list->status == 1)
    {
        $status = lang('received');
    }
    
    $data[] = array(
        $move_stock_list->ship_order_no,
        $move_stock_list->sku_str,
		$move_stock_list->qty_str,
        $status,
        $move_stock_list->storage_code,
        $move_stock_list->created_date,
		$move_stock_list->received_count,
        $edit_button,
    );
}

$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'ship_order_no',
    ),
    array(
        'type'      => 'input',
        'field'     => 'sku_str',
    ),
    '',
	$this->block->generate_search_dropdown('status', 'status'),
    '',
);

$config = array(
    'filters'    => $filters,
);

$title = lang('confirm_arrival');
echo block_header($title);
echo $this->block->generate_pagination('move_stock_list');
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters);
echo $this->block->generate_pagination('move_stock_list');
?>