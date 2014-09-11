<?php
$head = array(
    array('text' => lang('image_url'), 'sort_key' => 'image_url'),
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'product'),
    array('text' => lang('chinese_name') . '/' . lang('english_name'), 'sort_key' => 'name_en'),
    array('text' => lang('market_model') , 'sort_key' => 'market_model'),
	array('text' => lang('shelf_code') , 'sort_key' => 'shelf_code'),
);

if (product_can_write('price') OR product_can_read('price'))
{
    $head[] = array('text' => lang('price'), 'sort_key' => 'price');
}
if (product_can_write('stock_count') OR product_can_read('stock_count'))
{
    $head[] = array('text' => lang('stock_count'), 'sort_key' => 'stock_count');
	$head[] = array('text' => lang('make_sku_stock_count'));
}
if (product_can_write('min_stock_number') OR product_can_read('min_stock_number'))
{
    $head[] = array('text' => lang('min_stock_number'), 'sort_key' => 'min_stock_number');
}
if (product_can_write('sale_in_7_days') OR product_can_read('sale_in_7_days'))
{
    $head[] = array('text' => lang('7-days_sales_amounts'), 'sort_key' => 'sale_in_7_days');
}
if (product_can_write('sale_in_15_days') OR product_can_read('sale_in_15_days'))
{
    $head[] = array('text' => lang('15-days_sales_amounts'), 'sort_key' => 'sale_in_15_days');
}
if (product_can_write('sale_in_30_days') OR product_can_read('sale_in_30_days'))
{
    $head[] = array('text' => lang('30-days_sales_amounts'), 'sort_key' => 'sale_in_30_days');
}
if (product_can_write('sale_in_60_days') OR product_can_read('sale_in_60_days'))
{
    $head[] = array('text' => lang('60-days_sales_amounts'), 'sort_key' => 'sale_in_60_days');
}
if (product_can_write('ito_in_30_days') OR product_can_read('ito_in_30_days'))
{
    $head[] = array('text' => lang('ito_in_30_days'), 'sort_key' => 'ito_in_30_days');
}

$head[] = array('text' => lang('purchaser'), 'sort_key' => 'purchaser_id');
$head[] = array('text' => lang('add_dated'), 'sort_key' => 'updated_date');
$head[] = lang('options');

$users = $this->user_model->fetch_all_users();
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->id] = $user->name;
}

$data = array();

$forbidden_options = fetch_readable_statuses('ban_levels', TRUE);
foreach ($products as $product)
{
    if ($action == 'edit')
    {
        if ($can_delete)
        {
            $drop_button = $this->block->generate_drop_icon(
                'pi/product/drop_product',
                "{id: $product->pid}",
                TRUE
            );
        }
        else
        {
            $drop_button = '';
        }
        $edit_button = $this->block->generate_edit_link(site_url('pi/product/add_edit', array($product->pid)), TRUE);

        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(
            site_url('pi/product/view', array($product->pid)),
            array(),
            TRUE,
            'main-content-detail',
            'main-content'
        );
    }

    $item = array(
        block_image($product->image_url),
        $product->sku,
        $product->name_cn .br(). $product->name_en,
        $product->market_model,
		$product->shelf_code,
    );

    
    if (product_can_write('price') OR product_can_read('price'))
    {
        $item[] = $product->price;
    }
    if (product_can_write('stock_count') OR product_can_read('stock_count'))
    {        
        if(in_array($product->sku, $abroad_skus))
        {
            $stock_count_html = "SZ : $product->stock_count" . "<br/>" . "UK : $product->uk_stock_count" . "<br/>" . "DE : $product->de_stock_count" . "<br/>" . "AU : $product->au_stock_count" . "<br/>" . "YB : $product->yb_stock_count" . "<br/>";
            $item[] = $stock_count_html;
        }
        else
        {
            $item[] = "SZ : $product->stock_count" . "<br/>" ;
        }
		$item[] = fetch_makeup_sku_count($product->sku);
    }
    if (product_can_write('min_stock_number') OR product_can_read('min_stock_number'))
    {
        $item[] = $product->min_stock_number;
    }
    if (product_can_write('sale_in_7_days') OR product_can_read('sale_in_7_days'))
    {
        $item[] = $product->sale_in_7_days;
    }
	if (product_can_write('sale_in_15_days') OR product_can_read('sale_in_15_days'))
    {
        $item[] = $product->sale_in_15_days;
    }
    if (product_can_write('sale_in_30_days') OR product_can_read('sale_in_30_days'))
    {
        $item[] = $product->sale_in_30_days;
    }
    if (product_can_write('sale_in_60_days') OR product_can_read('sale_in_60_days'))
    {
        $item[] = $product->sale_in_60_days;
    }
    if (product_can_write('ito_in_30_days') OR product_can_read('ito_in_30_days'))
    {
        $item[] = $product->ito_in_30_days;
    }
    //$item[] = $product->name;
	$item[] = $this->user_model->fetch_user_name_by_id($product->purchaser_id);
    $item[] = $product->updated_date;
    $item[] = $url;

    $data[] = $item;
}
$values = array(
        'in_stock',
        'clear_stock',
        'out_of_stock',
    );
$type = 'sale_status';
$options = array();
$options[''] = lang('all');
foreach ($values as $value)
{
    $key = fetch_status_id($type, $value);
    $options[$key] = lang($value);
}
$filters = array(
    array(
		'type'      => 'input',
		'field'     => 'image_url',
	),
	array(
		'type'      => 'input',
		'field'     => 'sku',
	),
	array(
		'type'      => 'input',
		'field'     => 'product_basic.name_cn|product_basic.name_en',
	),
    array(
		'type'      => 'input',
		'field'     => 'market_model',
	),
	array(
		'type'      => 'input',
		'field'     => 'shelf_code',
	),
);


if (product_can_write('price') OR product_can_read('price'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'price',
	);
}

if (product_can_write('stock_count') OR product_can_read('stock_count'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'stock_count',
	);
};
$filters[] = NULL;
if (product_can_write('min_stock_number') OR product_can_read('min_stock_number'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'min_stock_number',
	);
};

if (product_can_write('sale_in_7_days') OR product_can_read('sale_in_7_days'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'sale_in_7_days',
	);
};
if (product_can_write('sale_in_15_days') OR product_can_read('sale_in_15_days'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'sale_in_15_days',
	);
};
if (product_can_write('sale_in_30_days') OR product_can_read('sale_in_30_days'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'sale_in_30_days',
	);
};

if (product_can_write('sale_in_60_days') OR product_can_read('sale_in_60_days'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'sale_in_60_days',
	);
};
if (product_can_write('ito_in_30_days') OR product_can_read('ito_in_30_days'))
{
    $filters[] = array(
		'type'      => 'input',
		'field'     => 'ito_in_30_days',
        'method'    => 'from_to'
	);
};

$filters[] = array(
        'type'      => 'dropdown',
        'field'     => 'purchaser_id',
        'options'   => $user_options,
        'method'    => '=',
    );

$filters[] = array(
    'type'      => 'input',
    'field'     => 'updated_date',
);

if ($action == 'edit')
{
    $title = lang('product_management');
}
else
{
    $title = lang('product_view');
}

$forbidden_level_html = '';

unset ($forbidden_options['']);

foreach ($forbidden_options as $key => $value)
{
    $config = array(
        'name'        => 'forbidden_level',
        'value'       => $key,
//        'checked'     => empty ($company_permissions_array) && $resource_company->name =='mallerp' ? TRUE : (in_array($resource_company->id, $company_permissions_array) ? TRUE : FALSE),
        'style'       => 'margin:10px',
    );
    $forbidden_level_html  .= form_checkbox($config) . $value;
}

echo block_header($title);
echo $this->block->generate_pagination('product', array(), 'main-content');
$config = array(
	'filters'    => $filters,
);
echo form_open();
//echo '<br/><B>' . lang('forbidden_level_sreach') . ' : </B>' . $forbidden_level_html;
echo $this->block->generate_reset_search($config, 'main-content');
echo $this->block->generate_table($head, $data, $filters, 'product');
echo $this->block->generate_pagination('product', array(), 'main-content');
echo form_close();
echo $this->block->generate_ac('filter_sku', array('product_basic', 'sku'));
?>
