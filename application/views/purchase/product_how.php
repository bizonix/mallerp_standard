<?php
$data = array(
                '100' => '100%',
                '10'  => '10%',
                '20'  => '20%',
                '30'  => '30%',
                '40'  => '40%',
                '50'  => '50%',
                '60'  => '60%',
                '70'  => '70%',
                '80'  => '80%',
                '90'  => '90%',
                

            );
$collection = to_js_array($data);
$head = array(
    array('text' => lang('item_no'), 'sort_key' => 'o_item_no', 'id' => 'purchase_how'),
    array('text' => lang('provider'), 'sort_key' => 'pp_name'),
    '',
    array('text' => lang('purchaser'), 'sort_key' => 'u_name'),
);

$data = array();
foreach ($review_orders as $review_order)
{

    $skus_head = array(
        lang('picture'),
        lang('sku'),
        lang('chinese_name'),
        lang('purchase_cost'),
        lang('purchase_quantity'),
        lang('fcommitqty'),
        lang('how_way'),
        lang('qualified_number'),
        lang('options')
    );
    $review_skus = $this->purchase_order_model->fetch_how_skus($review_order->o_id);
    $skus_data = array();
    $sku_url = site_url('purchase/order/update_how_number');
    foreach( $review_skus as $review_sku)
    {     
        $reset_url = site_url('purchase/order/cancel_how', array('sku_id' => $review_sku->s_id));
        $reset_button = anchor($reset_url, form_button('name',lang('reject')));
        $hows =  $this->purchase_order_model->fetch_purchase_how($review_sku->s_id);
        if(isset($hows->qualified_number) && ($hows->qualified_number > 0))
        {
            $qualified_number = $hows->qualified_number;
            $how_number = $qualified_number.'+';
        }
        else
        {
            $how_number = '';
        }
        $how_way = $this->purchase_order_model->fetch_how_way($review_sku->s_id);
        $skus_data[] = array(
            "<img src='{$review_sku->m_image_url}' width=40 height=30 />",
            get_status_image($review_sku->s_sku) . $review_sku->s_sku,
            $review_sku->b_name_cn,
            price($review_sku->s_sku_price*$review_sku->s_quantity),
            $review_sku->s_quantity,
            $review_sku->s_arrival_quantity,
            $this->block->generate_div("how_way_{$review_sku->s_id}", isset($how_way->how_way) ? $how_way->how_way.'%' : '100%'),
            $how_number.$this->block->generate_div("qualified_number_{$review_sku->s_id}", '[0]'),
            '<div style="float:right;margin:5px;" >'.$reset_button.'</div>',

        );
       echo $this->block->generate_editor(
            "how_way_{$review_sku->s_id}",
            'product_how_form',
            $sku_url,
            "{id: $review_sku->s_id, type: 'how_way'}",
            "$collection"
        );
        echo $this->block->generate_editor(
            "qualified_number_{$review_sku->s_id}",
            'product_how_form',
            $sku_url,
            "{id: $review_sku->s_id, type: 'qualified_number'}"
        );

    }
    $skus_table = $this->block->generate_table($skus_head, $skus_data);   
    $drop_button = $this->block->generate_drop_icon(
            'purchase/order/drop_order',
            "{id: $review_order->o_id}",
            TRUE
        );   
    $data[] = array(
        $review_order->o_item_no,
        $review_order->pp_name,
        $skus_table,
        $review_order->u_name,
    );
}

$title = lang('how_table_of_orders');
echo block_header($title);
$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'purchase_order.item_no',
    ),
    array(
        'type'      => 'input',
        'field'     => 'purchase_provider.name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'purchase_order_sku.sku',
    ),
    array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
		),
);
echo $this->block->generate_pagination('purchase_how');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'purchase_how');
echo form_close();
echo $this->block->generate_pagination('purchase_how');


?>

