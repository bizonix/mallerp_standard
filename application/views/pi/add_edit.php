<?php

$base_url = base_url();

$head = array(
    lang('name'),
    lang('value'),
);

$data = array();
/**
 * Product base field .
 */

$config = array(
    'name'        => 'sku',
    'id'          => 'sku',
    'value'       => $product ? $product->sku : '',
    'maxlength'   => '30',
    'size'        => '20',
);
if (product_can_write('sku'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('sku')),
        form_input($config),
    );
}
else if (product_can_read('sku'))
{
    if (empty($product->sku))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('sku')),
        form_input($config),
    );
}
$config = array(
    'name'        => 'sku_other',
    'id'          => 'sku_other',
    'value'       => $product ? $product->sku_other : '',
    'maxlength'   => '30',
    'size'        => '20',
);
if (product_can_write('sku'))
{
    $data[] = array(
        lang('sku_other'),
        form_input($config),
    );
}
else if (product_can_read('sku'))
{
    if (empty($product->sku_other))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('sku_other')),
        form_input($config),
    );
}
$config = array(
    'name'        => 'name_cn',
    'id'          => 'name_cn',
    'value'       => $product ? $product->name_cn : '',
    'maxlength'   => '100',
    'size'        => '100',
);
if (product_can_write('name_cn'))
{

    $data[] = array(
        $this->block->generate_required_mark(lang('chinese_name')),
        form_input($config),
    );
}
else if (product_can_read('name_cn'))
{
    if (empty($product->name_cn))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('chinese_name')),
        form_input($config),
    );
}

$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'value'       => $product ? $product->name_en : '',
    'maxlength'   => '100',
    'size'        => '100',
);
if (product_can_write('name_en'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('english_name')),
        form_input($config),
    );
}
else if (product_can_read('name_en'))
{
    if (empty($product->name_en))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('english_name')),
        form_input($config),
    );
}

if (product_can_write('product_catalog'))
{
    $catalogs = $catalogs;
    $str = form_dropdown('parent', $catalogs, isset($product->catalog_id) ? $product->catalog_id : '0');

    $data[] = array(
        $this->block->generate_required_mark(lang('product_catalog')),
        $str,
    );
}

/**
 * Product more field .
 */
$config = array(
    'name'        => 'pure_weight',
    'id'          => 'pure_weight',
    'value'       => $product ? $product->pure_weight : '',
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_write('pure_weight'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('pure_weight').' (g)'),
        form_input($config),
    );
}
else if (product_can_read('pure_weight'))
{
    if (empty($product->pure_weight))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('pure_weight').' (g)'),
        form_input($config),
    );
}


$config = array(
    'name'        => 'video_url',
    'id'          => 'video_url',
    'value'       => $product ? $product->video_url : '',
    'maxlength'   => '200',
    'size'        => '100',
);
if (product_can_write('video_url'))
{
    $data[] = array(
        lang('video_url'),
        form_input($config),
    );
}
else if (product_can_read('video_url'))
{
    if (empty($product->video_url))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('video_url'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'buy_url',
    'id'          => 'buy_url',
    'value'       => $product ? $product->buy_url : '',
    'maxlength'   => '500',
    'size'        => '100',
);
if (product_can_write('buy_url'))
{
    $data[] = array(
        lang('buy_url'),
        form_input($config),
    );
}
else if (product_can_read('buy_url'))
{
    if (empty($product->buy_url))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('buy_url'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'market_model',
    'id'          => 'market_model',
    'value'       => $product ? $product->market_model : '',
    'maxlength'   => '250',
    'size'        => '100',
);
if (product_can_write('market_model'))
{
    $data[] = array(
        lang('market_model'),
        form_input($config),
    );
}
else if (product_can_read('market_model'))
{
    if (empty($product->market_model))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('market_model'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'box_contain_number',
    'id'          => 'box_contain_number',
    'value'       => $product ? $product->box_contain_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_write('box_contain_number'))
{
    $data[] = array(
        lang('box_contain_number'),
        form_input($config),
    );
}
else if (product_can_read('box_contain_number'))
{
    if (empty($product->box_contain_number))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('box_contain_number'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'box_total_weight',
    'id'          => 'box_total_weight',
    'value'       => $product ? $product->box_total_weight : '',
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_write('box_total_weight'))
{
    $data[] = array(
        lang('box_total_weight').' (g)',
        form_input($config),
    );
}
else if (product_can_read('box_total_weight'))
{
    if (empty($product->box_total_weight))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('box_total_weight').' (g)',
        form_input($config),
    );
}


$config = array(
    'name'        => 'stock_code',
    'id'          => 'stock_code',
    'value'       => $product ? $product->stock_code : '',
    'maxlength'   => '20',
    'size'        => '20',
);
if (product_can_write('stock_code'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('stock_code')),
        form_input($config),
    );
}
else if (product_can_read('stock_code'))
{
    if (empty($product->stock_code))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('stock_code')),
        form_input($config),
    );
}

$config = array(
    'name'        => 'shelf_code',
    'id'          => 'shelf_code',
    'value'       => $product ? $product->shelf_code : '',
    'maxlength'   => '20',
    'size'        => '20',
);
if (product_can_write('shelf_code'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('shelf_code')),
        form_input($config),
    );
}
else if (product_can_read('shelf_code'))
{
    if (empty($product->shelf_code))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('shelf_code')),
        form_input($config),
    );
}

if (product_can_write('bulky_cargo'))
{
    $config = array(
        'name'        => 'bulky_cargo',
        'value'       => 1,
        'checked'     => (isset($product->bulky_cargo) && $product->bulky_cargo == 1) ? TRUE : FALSE,
        'style'       => 'margin:10px',
    );
    $bulky_cargo = form_radio($config) . form_label(lang('yes'));
    $config = array(
        'name'        => 'bulky_cargo',
        'value'       => 0,
        'checked'     => (isset($product->bulky_cargo) && $product->bulky_cargo == 0) ? TRUE : FALSE,
        'style'       => 'margin:10px',
    );
    $bulky_cargo .= form_radio($config) . form_label(lang('no'));
    $data[] = array(
        $this->block->generate_required_mark(lang('bulky_cargo')),
        $bulky_cargo,
    );
}
else if (product_can_read('bulky_cargo'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('bulky_cargo')),
        empty($product->bulky_cargo) ? 'No' : 'Yes',
    );
}

if (product_can_write('description'))
{
    $config = array(
        'name'        => 'description',
        'id'          => 'description',
        'value'       => $product ? $product->description : '',
        'rows'        => '8',
        'cols'        => '80',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('description_en')),
        form_textarea($config),
    );
}
else if (product_can_read('description'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('description_en')),
        $product->description,
    );
}

if (product_can_write('short_description'))
{
    $config = array(
        'name'        => 'short_description',
        'id'          => 'short_description',
        'value'       => $product ? $product->short_description : '',
        'rows'        => '8',
        'cols'        => '80',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('short_description_en')),
        form_textarea($config),
    );
}
else if (product_can_read('short_description'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('short_description_en')),
        $product->short_description,
    );
}

if (product_can_write('description_cn'))
{
    $config = array(
        'name'        => 'description_cn',
        'id'          => 'description_cn',
        'value'       => $product ? $product->description_cn : '',
        'rows'        => '8',
        'cols'        => '80',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('description_cn')),
        form_textarea($config),
    );
}
else if (product_can_read('description_cn'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('description_cn')),
        $product->description_cn,
    );
}


if (product_can_write('description') OR product_can_write('short_description') OR product_can_write('description_cn'))
{
    echo $this->block->generate_tinymce(array('description', 'short_description', 'description_cn'));
}


$config_sz = array(
    'name'        => 'min_stock_number',
    'id'          => 'min_stock_number',
    'value'       => $product ? $product->min_stock_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
$config_uk = array(
    'name'        => 'uk_min_stock_number',
    'id'          => 'uk_min_stock_number',
    'value'       => $product ? $product->uk_min_stock_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
$config_de = array(
    'name'        => 'de_min_stock_number',
    'id'          => 'de_min_stock_number',
    'value'       => $product ? $product->de_min_stock_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
$config_au = array(
    'name'        => 'au_min_stock_number',
    'id'          => 'au_min_stock_number',
    'value'       => $product ? $product->au_min_stock_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
$config_yb = array(
    'name'        => 'yb_min_stock_number',
    'id'          => 'yb_min_stock_number',
    'value'       => $product ? $product->yb_min_stock_number : '',
    'maxlength'   => '10',
    'size'        => '10',
);
$min_stock_number_html = '';
if (product_can_write('min_stock_number'))
{
}
else if (product_can_read('min_stock_number'))
{
    if (empty($product->min_stock_number))
    {
        $config_sz['disabled'] = true;
		$config_uk['disabled'] = true;
		$config_de['disabled'] = true;
		$config_au['disabled'] = true;
    }
    else
    {
        $config_sz['readonly'] = true;
		$config_uk['readonly'] = true;
		$config_de['readonly'] = true;
		$config_au['readonly'] = true;
    }

}

foreach ($stock_code as $code)
{
    switch ($code->stock_code) {
        case 'SZ':
            $min_stock_number_html .= ' SZ : '.form_input($config_sz).'<br/><br/>';
            break;
        case 'UK':
            $min_stock_number_html .= ' UK : '.form_input($config_uk).'<br/><br/>';
            break;
        case 'DE':
            $min_stock_number_html .= ' DE : '.form_input($config_de).'<br/><br/>';
            break;
        case 'AU':
            $min_stock_number_html .= ' AU : '.form_input($config_au).'<br/><br/>';
            break;
		case 'YB':
            $min_stock_number_html .= ' YB : '.form_input($config_yb).'<br/><br/>';
            break;
        default:
            break;
    }
}
    $data[] = array(
        $this->block->generate_required_mark(lang('min_stock_number')),
//        form_input($config),
        $min_stock_number_html,
    );
$data[] = array(
        lang('make_sku_stock_count'),
        fetch_makeup_sku_count($product->sku),
    );

$options_p = array();

foreach ($all_out_packing as $out_packing)
{
    $options_p[$out_packing->status_id] = lang($out_packing->status_name);
}

if (product_can_write('packing_or_not'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_or_not')),
        form_dropdown('packing_or_not', $options_p, empty($product->packing_or_not) ? '' : $product->packing_or_not),
    );
}
else if (product_can_read('packing_or_not'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_or_not')),
        isset($product->packing_or_not) && isset($options_p[$product->packing_or_not]) ? $options_p[$product->packing_or_not] : lang('no_packing'),
    );
}

$options['0'] = lang('please_select');

foreach ($product_packing as $type)
{
    $options[$type->id] = $type->name_cn;
}

$url = site_url('pi/packing/get_packing_by_id');

if (product_can_write('packing_material'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_material')),
        form_dropdown('packing_material', $options, isset($product->packing_material) ? $product->packing_material : '','onchange=get_packing(this,"'.$url.'")').'<div id="packing_show"></div>',
    );
}
else if (product_can_read('packing_material'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_material')),
        isset($product->packing_material) ? $options[$product->packing_material] : '',
    );
}

$config = array(
    'name'        => 'pack_cost',
    'id'          => 'pack_cost',
    'value'       => $product ? $product->pack_cost : '',
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_write('pack_cost'))
{
    $data[] = array(
        lang('pack_cost'),
        form_input($config),
    );
}
else if (product_can_read('pack_cost'))
{
    if (empty($product->pack_cost))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('pack_cost'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'total_weight',
    'id'          => 'total_weight',
    'value'       => $product ? $product->total_weight : '',
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_read('total_weight') || product_can_write('total_weight'))
{
    if (empty($product->gross_weight))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('total_weight').' (g)',
        form_input($config),
    );
}

$config = array(
    'name'        => 'price',
    'id'          => 'price',
    'value'       => $product ? $product->price : '',
    'maxlength'   => '10',
    'size'        => '10',
);

if (product_can_read('price') || product_can_write('price'))
{
    if (empty($product->price))
    {
        //$config['disabled'] = true;
    }
    else
    {
        //$config['readonly'] = true;
    }
    $data[] = array(
        lang('price'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'sale_price',
    'id'          => 'sale_price',
    'value'       => $product ? $product->sale_price : '',
    'maxlength'   => '10',
    'size'        => '10',
);

if (product_can_read('sale_price') || product_can_write('sale_price'))
{
    if (empty($product->sale_price))
    {
        //$config['disabled'] = true;
    }
    else
    {
        //$config['readonly'] = true;
    }
    $data[] = array(
        lang('sale_price'),
        form_input($config),
    );
}

$config = array(
    'name'        => 'fill_material_heavy',
    'id'          => 'fill_material_heavy',
    'value'       => empty($product->fill_material_heavy) ? '' : $product->fill_material_heavy,
    'maxlength'   => '10',
    'size'        => '10',
);
if (product_can_write('fill_material_heavy'))
{
    $data[] = array(
        lang('fill_material_heavy').' (g)',
        form_input($config),
    );
}
else if (product_can_read('fill_material_heavy'))
{
    if (empty($product->fill_material_heavy))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('fill_material_heavy').' (g)',
        form_input($config),
    );
}

/**
 * Product ebay field .
 */

$in_stock = fetch_status_id('sale_status', 'in_stock');
$options = fetch_statuses('sale_status');
foreach ($options as $key => $value)
{
    $options[$key] = lang($value);
}
if (product_can_write('sale_status'))
{
    $data[] = array(
        lang('sale_status'),
        form_dropdown('sale_status', $options, empty($product->sale_status) ? $in_stock : $product->sale_status),
    );
}
else if (product_can_read('sale_status'))
{
    $data[] = array(
        lang('sale_status'),
        !empty ($product->sale_status) ? $options[$product->sale_status] : '',
    );
}

$options = fetch_statuses('ban_levels');

$forbidden_level_html = '';

    $forbidden_level_arr = array();
    
    foreach (get_forbidden_level_obj($product->id) as $value)
    {
        $forbidden_level_arr[] = $value->ban_level;
    }
    
foreach ($options as $key => $value)
{
    $config = array(
        'name'        => 'forbidden_level[]',
        'value'       => $key,
        'checked'     => !empty ($forbidden_level_arr) && in_array($key, $forbidden_level_arr) ? TRUE : FALSE,
        'style'       => 'margin:10px',
    );
	if(product_can_write('forbidden_level')){
		$forbidden_level_html  .= form_checkbox($config) . lang($value);
	}
	if(product_can_read('forbidden_level')){
		if(!empty ($forbidden_level_arr) && in_array($key, $forbidden_level_arr)){
			$forbidden_level_html  .= lang($value);
		}
		
	}
}

$data[] = array(
    lang('forbidden_level'),
    block_check_group('forbidden_level[]', $forbidden_level_html),
);




$config = array(
    'name'        => 'lowest_profit',
    'id'          => 'lowest_profit',
    'value'       => $product->lowest_profit == 0 ? '' : $product->lowest_profit,
    'maxlength'   => '10',
    'size'        => '10',
);

$html = '';
foreach ($coefficient as $v) {
    $html .= '<label onclick="get_coefficient('.$v->lowest_profit.')">  '.$v->name_cn.' : '.$v->lowest_profit.'  </label>';
}

if (product_can_write('lowest_profit'))
{
    $data[] = array(
        lang('lowest_profit'),
        form_input($config).$html,
    );
}
else if (product_can_read('lowest_profit'))
{
    if (empty($product->lowest_profit))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('lowest_profit'),
        form_input($config).' <label onclick="get_coefficient('.$coefficient.')">  '.lang('inheritance_profit_factor').' : '.$coefficient.'  </label>',
    );
}


$config = array(
    'name'        => 'picture_url',
    'id'          => 'picture_url',
    'value'       => $product ? $product->picture_url : '',
    'maxlength'   => '200',
    'size'        => '100',
);
if (product_can_write('picture_url'))
{
    $data[] = array(
        lang('picture_url'),
        form_input($config),
    );
}
else if (product_can_read('picture_url'))
{
    if (empty($product->picture_url))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        lang('picture_url'),
        form_input($config),
    );
}


$options = array();
$options[-1] = lang('please_select');
foreach ($all_purchase_users as $purchase_user)
{
    $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$purchaser_id = $this->product_model->fetch_product_purchaser_id($product->pid);
if (product_can_write('purchaser'))
{
   
    $data[] = array(
        lang('purchaser'),
        form_dropdown('purchaser_id', $options, $purchaser_id),
    );
}
else if (product_can_read('purchaser'))
{
    $data[] = array(
        lang('purchaser'),
        $options[$purchaser_id],
    );
}

$options = array();
$name_arr = array();
$options[-1] = lang('please_select');
foreach ($all_users as $purchase_user)
{
    $options[$purchase_user->id] = $purchase_user->name;
    $name_arr[] = $purchase_user->id;
}
$purchaser_id = $this->product_model->fetch_product_purchaser_id($product->pid);


if (product_can_write('product_develper'))
{
    $name = form_dropdown('product_develper_by_purchase', $options, $product->product_develper_id?$product->product_develper_id : '-1');

    if(!in_array($product->product_develper_id, $name_arr))
    {
        $name .= '<input type="hidden" name="product_develper_id" value="'.$product->product_develper_id.'" readonly="true" >'.$dev_name.'</input>';
    }
    $data[] = array(
        lang('product_develper'),
        $name,
    );
}
else if (product_can_read('product_develper'))
{
    $data[] = array(
        lang('product_develper'),
        element("$product->product_develper_id", $options),
    );
}


if (product_can_write('provider_management'))
{
    $block = greate_provider_block($this, $product_id, $providers);

    $data[] = array(
        lang('provider_management'),
        $block,
    );
}

$config = array(
    'name'        => 'image_url',
    'id'          => 'image_url',
    'value'       => $product ? $product->image_url : '',
    'maxlength'   => '200',
    'size'        => '100',
);
if (product_can_write('image_url'))
{
    $image_html = $this->block->generate_image_input($config);
    $data[] = array(
        $this->block->generate_required_mark(lang('small_image_url')),
        $image_html,
    );
}
else if (product_can_read('image_url'))
{
    if (empty($product->image_url))
    {
        $config['disabled'] = true;
    }
    else
    {
        $config['readonly'] = true;
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('small_image_url')),
        $this->block->generate_image_input($config),
    );
}

if (product_can_read('product_images_view'))
{
    $image_html = '<div style="" id="uploaded_images">';
    foreach ($uploaded_images as $img)
    {
        $src = trim($base_url, '/') . trim($uploaded_path, '.') . $img;
        $image_properties = array(
            'src'     => $src,
            'width'   => '100',
            'height'  => '100',
            'style'   => 'margin: 5px;',
            'title'   => 'Click to copy the image url',
        );
        $param = "{image_name: '$img', 'sku': '{$product->sku}'}";
        $delete_link = block_drop_icon('pi/product/delete_uploaded_image', $param, TRUE, NULL, '$(this).previous(0)');

        $image_html .= "<span style='margin-right: 10px;'><a href='$src' target='_blank'>" . img($image_properties) . "</a>$delete_link</span>";
    }
    $image_html .= '</div>';
    $data[] = array(
        lang('product_images_view'),
        $image_html,
    );
}
/*
if (product_can_read('ebay_images_view'))
{
    $image_html = '<div style="height: 120px;" id="ebay_uploaded_images">';
    foreach ($ebay_uploaded_images as $img)
    {
        $src = trim($base_url, '/') . trim($ebay_uploaded_path, '.') . $img;
        $image_properties = array(
            'src'     => $src,
            'width'   => '100',
            'height'  => '100',
            'style'   => 'margin: 5px;',
            'title'   => 'Click to copy the image url',
        );
        $param = "{image_name: '$img', 'sku': '{$product->sku}'}";
        $delete_link = block_drop_icon(site_url('pi/product/delete_uploaded_image', array('gallery')), $param, TRUE, NULL, '$(this).previous(0)');

        $image_html .= "<span style='margin-right: 10px;'><a href='$src' target='_blank'>" . img($image_properties) . "</a>$delete_link</span>";
    }
    $image_html .= '</div>';
    $data[] = array(
        lang('ebay_images_view'),
        $image_html,
    );
}*/
//显示广告模版代码的文件
if (product_can_read('ebay_images_view'))
{
    $ad_html = '<div style="height: 120px;" id="ad_code_uploaded">';
    foreach ($ad_code_uploaded as $ad_code)
    {
        $src = trim($base_url, '/') . trim($upload_file_path, '.') . $ad_code;
        $param = "{file_name: '$ad_code', 'sku': '{$product->sku}'}";
        $delete_link = block_drop_icon(site_url('pi/product/delete_ad_code'), $param, TRUE, NULL, '$(this).previous(0)');

        $ad_html .= "<span style='margin-right: 10px;'><a href='$src' target='_blank'>" . $ad_code . "</a>$delete_link</span>";
    }
    $ad_html .= '</div>';
    $data[] = array(
        lang('ad_code_view'),
        $ad_html,
    );
}
//结束了


$back_button = $this->block->generate_back_icon(site_url('pi/product/manage'));
$title = lang('edit_product'). $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'product_form',
);

echo form_open(site_url('pi/product/save_edit'), $attributes);

echo $this->block->generate_table($head, $data);

$url = site_url('pi/product/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_product'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_content(this, '$url');",
);
echo form_hidden('product_id', $product ? $product->pid : '-1');
if( ! isset ($action))
{
   echo '<h2>'.block_button($config).$back_button.'</h2>';
}
echo form_close();

if (product_can_write('product_images_management'))
{
    $image_html = '';
    $image_url = site_url('pi/product/upload_images');
    $config = array(
        'name'  => 'product_id',
        'value' => $product->pid,
        'type'  => 'hidden',
    );
    $hidden = form_input($config);
    $image_html .= '<br/>' . block_form_uploads('upload_images[]', $image_url, $hidden);
    $image_html .= '&nbsp;' . lang('upload_image_notice');
    $image_html .= '<br/>';


    $head = array(
        lang('product_images_management'),
    );
    $data = array();
    $data[] = array(
        $image_html,
    );
    echo block_table($head, $data);
}

/*
if (product_can_write('ebay_images_management'))
{
    echo br();
    $image_html = '';
    $image_url = site_url('pi/product/upload_images', array('gallery'));
    $config = array(
        'name'  => 'product_id',
        'value' => $product->pid,
        'type'  => 'hidden',
    );
    $hidden = form_input($config);
    $image_html .= '<br/>' . block_form_uploads('upload_images[]', $image_url, $hidden);
    $image_html .= '&nbsp;' . lang('upload_image_notice');
    $image_html .= '<br/>';


    $head = array(
        lang('ebay_images_management'),
    );
    $data = array();
    $data[] = array(
        $image_html,
    );
    echo block_table($head, $data);
}*/

//添加广告模版上传
if (product_can_write('ebay_images_management'))
{
    echo br();
    $image_html = '';
    $image_url = site_url('pi/product/upload_file');
    $config = array(
        'name'  => 'product_id',
        'value' => $product->pid,
        'type'  => 'hidden',
    );
    $hidden = form_input($config);
    $image_html .= '<br/>' . block_form_uploads('upload_file[]', $image_url, $hidden,'upload_file');
    $image_html .= '&nbsp;' . lang('upload_ad_code_notice');
    $image_html .= '<br/>';


    $head = array(
        lang('ad_code_management'),
    );
    $data = array();
    $data[] = array(
        $image_html,
    );
    echo block_table($head, $data);
}
//添加广告模版上传结束
$js_array = '$A([';
if (isset($product_catalog) && isset($product_catalog->path))
{
    $items = explode('>', $product_catalog->path);
    $count = count($items);
    for ($i = 0; $i < $count; $i++)
    {
        $js_array .= $items[$i];
        if ($i < $count -1)
        {
            $js_array .= ',';
        }
    }
}
$js_array .= '])';
?>

<script>
    document.observe('dom:loaded', function(){
        //extand_catalog(<?=$js_array?>);
        
        var id = '<?= $product->packing_material ?>';
        var url = '<?= site_url('pi/packing/get_packing_by_id'); ?>';

        param = {'id' : id};
        return helper.update_content(url, param, 'packing_show');

    });

    function get_coefficient(value)
    {
        if(value)
        {
            $('lowest_profit').value = value;
        }
        else
        {
            alert('所有继承利润系数均为空值');
        }
    }


</script>


<?php
function greate_provider_block($object, $product_id, $providers = array())
{
    $data = array(
        '1' => '1',
        '2' => '2',
        '3' => '3',
    );
    $collection = to_js_array($data);

    $provider_id = 1;
    $url = site_url('purchase/provider/add_provider_sku', array('provider_id' => -1));
    $add_button = $object->block->generate_add_icon($url, "{product_id: $product_id}");
    $head = array(
        lang('provider_name'),
        lang('1_to_9_price'),
        lang('10_to_99_price'),
        lang('100_to_999_price'),
        lang('1000_price'),
        lang('provider_sequence'),
        lang('separating_shipping_cost'),
        lang('options') . $add_button,
    );
    $sku_url = site_url('purchase/provider/update_provider_sku', array('provider_id' => -1));
    $data = array();
    foreach ($providers as $provider)
    {
        $drop_button = $object->block->generate_drop_icon(
                        'purchase/provider/drop_provider_sku',
                        "{id: $provider->m_id}",
                        TRUE
        );

        $provider_name = $object->purchase_model->fetch_provider_name_by_id($provider->m_provider_id);
        $id = 'provider_name_' . $provider->m_id;
        $config = array(
            'name'        => $id,
            'id'          => $id,
            'value'       => ! empty($provider_name) ? $provider_name : '',
            'maxlength'   => '100',
            'size'        => '30',
        );
        $provider_name_html = form_input($config);
        $config_save = array(
            'name'        => 'save_provider_name',
            'value'       => lang('save'),
            'type'        => 'button',
            'onclick'     => "hide_label('$id');helper.ajax('$sku_url', {id: $provider->m_id, product_id: $product_id, provider_name: \$('$id').value});",
        );

        $url = site_url('purchase/provider/add', array('popup', "provider_name_$provider->m_id"));

        $anchor = anchor(
            $url,
            lang('create_provider'),
            array('title' => 'click to set up!','onclick' => 'helper.modal(this); return false;')
        );

        $attributes = array(
            'id' => 'label_content',
        );

        if(! empty($provider_name))
        {
            $provider_name_html .= form_input($config_save);
        }
        else
        {
            $provider_name_html .= form_input($config_save).'&nbsp;&nbsp;&nbsp;'.form_label($anchor,null, $attributes);
        }

        echo $object->block->generate_ac($id, array('purchase_provider', 'name'));

        $data[] = array(
            $object->block->generate_div("provider_$provider->m_id", $provider_name_html),
            $object->block->generate_div("price1to9_{$provider->m_id}", $provider->m_price1to9),
            $object->block->generate_div("price10to99_{$provider->m_id}", $provider->m_price10to99),
            $object->block->generate_div("price100to999_{$provider->m_id}", $provider->m_price100to999),
            $object->block->generate_div("price1000_{$provider->m_id}", $provider->m_price1000),
            $object->block->generate_div("provide_level_{$provider->m_id}", $provider->m_provide_level),
            $object->block->generate_div("separating_shipping_cost_{$provider->m_id}", $provider->m_separating_shipping_cost),
            $drop_button,
        );
        echo $object->block->generate_editor(
                "price1to9_{$provider->m_id}",
                'price1to9_form',
                $sku_url,
                "{id: $provider->m_id, type: 'price1to9'}"
        );
        echo $object->block->generate_editor(
                "price10to99_{$provider->m_id}",
                'price10to99_form',
                $sku_url,
                "{id: $provider->m_id, type: 'price10to99'}"
        );
        echo $object->block->generate_editor(
                "price100to999_{$provider->m_id}",
                'price100to999_form',
                $sku_url,
                "{id: $provider->m_id, type: 'price100to999'}"
        );
        echo $object->block->generate_editor(
                "price1000_{$provider->m_id}",
                'price1000_form',
                $sku_url,
                "{id: $provider->m_id, type: 'price1000'}"
        );
        echo $object->block->generate_editor(
                "provide_level_{$provider->m_id}",
                'provide_level_form',
                $sku_url,
                "{id: $provider->m_id, type: 'provide_level'}",
                "$collection"
        );
        echo $object->block->generate_editor(
                "separating_shipping_cost_{$provider->m_id}",
                'separating_shipping_cost_form',
                $sku_url,
                "{id: $provider->m_id, type: 'separating_shipping_cost'}"
        );
    }    

    return $object->block->generate_table($head, $data);
}
?>

<script type="text/javascript">

function pass_name(id)
{
    $(id).value = $('name').value ;
    $('label_content').innerHTML = '';
}

function hide_label(id)
{
    if($(id).value)
    {
        $('label_content').innerHTML = '';
    }
}

function validate()
{
    var selects = $$('input[name="permissions[]"]');
    var tag = true;
    selects.each(function(e){
        if (e.checked)
        {
            tag = false;
        }
    });

    if($('name').value == '')
    {
        alert("<?=lang('name_cannot_be_empty'); ?>");
        return false;
    }
    else if($('contact_person').value == '')
    {
        alert("<?=lang('contact_cannot_be_empty'); ?>");
        return false;
    }
    else if($('address').value == '')
    {
        alert("<?=lang('address_cannot_be_empty'); ?>");
        return false;
    }
    else if(tag)
    {
        alert("<?=lang('procurement_authority_cannot_be_empty'); ?>");
        return false;
    }
    else
    {
        return true;
    }    
}

function save(id,url)
{
    if(validate())
    {
        this.blur();
        pass_name(id);
        Modalbox.hide();
        helper.ajax(url,$('add_provider_form').serialize(true), 1);
    }
    else
    {
        return ;
    }
}

</script>