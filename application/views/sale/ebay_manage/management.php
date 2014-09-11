<?php
$base_url = base_url();

$head = array(
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'myebay_list'),
    array('text' => lang('picture'), 'sort_key' => 'image_url'),
    array('text' => lang('sku_sale_status'), 'sort_key' => 'sku_sale_status'),
    array('text' => lang('item_sale_status'), 'sort_key' => 'active_status'),
    array('text' => lang('product_title'), 'sort_key' => 'title'),
    array('text' => lang('sale_mode'), 'sort_key' => 'listing_type'),
    array('text' => lang('currency_code'), 'sort_key' => 'currency'),
    array('text' => lang('price') . '/' . lang('postage_cost') . '/' . lang('qty_str'), 'sort_key' => 'price'),
//    array('text' => lang('postage_cost'), 'sort_key' => 'shipping_price'),
//    array('text' => lang('qty_str'), 'sort_key' => 'qty'),
    lang('product_information'),
    array('text' => lang('item_id'), 'sort_key' => 'item_id'),
    array('text' => lang('ebay_brother'), 'sort_key' => 'alarm'),
    array('text' => lang('ebay_id'), 'sort_key' => 'ebay_id'),
    array('text' => lang('updated_date'), 'sort_key' => 'updated_date'),
);

$data = array();

foreach ($ebay_products as $ebay_product)
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
                        'sale/ebay_manage/drop_ebay_product',
                        "{id: $ebay_product->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('sale/ebay_manage/add_edit', array($ebay_product->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('sale/ebay_manage/view', array($ebay_product->id)));
    }

    $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
	if($ebay_product->currency=='EUR'){
		$ebay_url = 'http://www.ebay.de/itm/ws/eBayISAPI.dll?ViewItem&item=';
	}
	if($ebay_product->currency=='GBP'){
		$ebay_url = 'http://www.ebay.co.uk/itm/ws/eBayISAPI.dll?ViewItem&item=';
	}
	if($ebay_product->currency=='AUD'){
		$ebay_url = 'http://www.ebay.com.au/itm/ws/eBayISAPI.dll?ViewItem&item=';
	}

    $ebay_html = '<a target="_blank" href="' . $ebay_url . $ebay_product->item_id . '">'. $ebay_product->item_id .'</a>';

    $product = NULL;
    $product_information = '';
    if ($ebay_product->sku)
    {
        $ebausku = explode(",", $ebay_product->sku);
        $product = $this->product_model->fetch_product_by_sku($ebausku[0], NULL, FALSE);
		if($product)
		{
			$product_information .= lang('stock_count') . ": " . $product->stock_count . "<br/>";
        	$product_information .= lang('sale_amount_level') . ": " . $product->sale_amount_level . "<br/>";
        	$product_information .= lang('sale_quota_level') . ": " . $product->sale_quota_level;
		}
    }
    
    $edit_button = block_edit_link(site_url('sale/ebay_competitor/edit_competitor', array($ebay_product->item_id)), TRUE);
    $ebay_bother_str = '';
    $competitors = $this->ebay_model->fetch_competitors_item_id($ebay_product->item_id);
    
    $alarm_image = '<img width="12" src="' . $base_url . 'static/images/icons/alarm.gif"' . '/>';
    foreach ($competitors as $competitor)
    {
        $seller_text = $competitor->seller_id;
        if ( ! empty($competitor->balance))
        {
            $seller_text .= '(' . $competitor->balance . ')';
        }
        if ($competitor->status)
        {
            $seller_text .= $alarm_image;
        }
        $attributes = array(
            'target' => '_blank',
            'title'  => empty($competitor->track_time) ? lang('not_track_yet') : lang('track_time') . ':' . $competitor->track_time
        );
        $ebay_bother_str .= anchor($competitor->url, $seller_text, $attributes)  . nbs();
        $ebay_bother_str .= br();
    }
    $ebay_bother_str .= br();
    $ebay_bother_str .= $edit_button;
    $data[] = array(
        $ebay_product->sku,
        '<img src="'.$ebay_product->image_url.'" />',
        lang($sale_statuses[$ebay_product->sku_sale_status]),
        $ebay_product->active_status ? lang('in_stock') : lang('sale_out_of_stock'),
        $ebay_product->title,
        lang($ebay_product->listing_type),
        $ebay_product->currency,
        $ebay_product->price . '/' . $ebay_product->shipping_price . '/' . $ebay_product->qty,
        $product_information,
        $ebay_html,
        $ebay_bother_str,
        $ebay_product->ebay_id,
        $ebay_product->updated_date,
    );
}

$mode_collection = array(
    'buy_now'  => lang('buy_now'),
    'auction'  => lang('auction'),
);

$code_collection = array(''=>lang('please_select'));
foreach ($currency_code as $v)
{
    $code_collection[$v->code] = $v->code;
}

$mode_collection = array(
    ''         => lang('please_select'),
    'buy_now'  => lang('buy_now'),
    'auction'  => lang('auction'),
);

$ebay_collection = array('' => lang('please_select'));
foreach ($ebay_ids as $ebay_id)
{
    $ebay_collection[$ebay_id] = $ebay_id;
}

$sku_sale_statuses = array('' => lang('please_select'));
foreach ($sale_statuses as $id => $name)
{
    $sku_sale_statuses[$id] = lang($name);
}

$alarm_statuses = array('' => lang('please_select'));
$alarm_statuses[-1] = lang('not_alarm');
$alarm_statuses[0] = lang('not_set');
$alarm_statuses[1] = lang('alarming');

//$active_statuses = array('' => lang('please_select'));
//$active_statuses[0] = lang('out_of_stock');
//$active_statuses[1] = lang('in_stock');

$active_statuses = array(
    '1' => lang('in_stock'),
    '0' => lang('sale_out_of_stock'),
    );

$filters = array(
    array(
        'type' => 'input',
        'field' => 'sku',
    ),
    NULL,
    array(
        'type' => 'dropdown',
        'field' => 'sku_sale_status',
        'options' => $sku_sale_statuses,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'active_status',
        'options' => $active_statuses,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'title',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'listing_type',
        'options' => $mode_collection,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'currency',
        'options' => $code_collection,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'price|shipping_price|qty',
    ),
    NULL,

    array(
        'type' => 'input',
        'field' => 'item_id',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'alarm',
        'options' => $alarm_statuses,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'ebay_id',
        'options' => $ebay_collection,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'updated_date',
    ),
);

echo block_header(lang('myebay_product_manage'));

echo $this->block->generate_pagination('myebay_list');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'myebay_list');

echo form_close();

echo $this->block->generate_pagination('myebay_list');
?>
