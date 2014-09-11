<?php
$this->load->helper('product_permission');

$head = array(
    lang('name'),
    lang('value'),
);

$data = array();
/**
 * Product base field .
 */
if (product_can_read('name_cn'))
{
    $config = array(
        'name'        => 'name_cn',
        'id'          => 'name_cn',
        'value'       => $product ? $product->name_cn : '',
        'maxlength'   => '100',
        'size'        => '100',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('chinese_name')),
        form_input($config),
    );
}

if (product_can_read('name_en'))
{
    $config = array(
        'name'        => 'name_en',
        'id'          => 'name_en',
        'value'       => $product ? $product->name_en : '',
        'maxlength'   => '100',
        'size'        => '100',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('english_name')),
        form_input($config),
    );
}

if (product_can_read('sku'))
{
    $config = array(
        'name'        => 'sku',
        'id'          => 'sku',
        'value'       => $product ? $product->sku : '',
        'maxlength'   => '20',
        'size'        => '20',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('sku')),
        form_input($config),
    );
}

if (product_can_read('product_catalog'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('product_catalog')),
        $product_catalog->name_cn,
    );
}

/**
 * Product more field .
 */
if (product_can_read('pure_weight'))
{
    $config = array(
        'name'        => 'pure_weight',
        'id'          => 'pure_weight',
        'value'       => $product ? $product->pure_weight : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('pure_weight').' (g)'),
        form_input($config),
    );
}

if (product_can_read('length'))
{
    $config = array(
        'name'        => 'length',
        'id'          => 'length',
        'value'       => $product ? $product->length : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('length').' (cm)',
        form_input($config),
    );
}

if (product_can_read('width'))
{
    $config = array(
        'name'        => 'width',
        'id'          => 'width',
        'value'       => $product ? $product->width : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('width').' (cm)',
        form_input($config),
    );
}

if (product_can_read('height'))
{
    $config = array(
        'name'        => 'height',
        'id'          => 'height',
        'value'       => $product ? $product->height : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('height').' (cm)',
        form_input($config),
    );
}

if (product_can_read('image_url'))
{
    $config = array(
        'name'        => 'image_url',
        'id'          => 'image_url',
        'value'       => $product ? $product->image_url : '',
        'maxlength'   => '200',
        'size'        => '100',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('image_url')),
        $this->block->generate_image_input($config),
    );
}

if (product_can_read('video_url'))
{
    $config = array(
        'name'        => 'video_url',
        'id'          => 'video_url',
        'value'       => $product ? $product->video_url : '',
        'maxlength'   => '200',
        'size'        => '100',
    );
    $data[] = array(
        lang('video_url'),
        form_input($config),
    );
}

if (product_can_read('market_model'))
{
    $config = array(
        'name'        => 'market_model',
        'id'          => 'market_model',
        'value'       => $product ? $product->market_model : '',
        'maxlength'   => '250',
        'size'        => '100',
    );
    $data[] = array(
        lang('market_model'),
        form_input($config),
    );
}

if (product_can_read('box_contain_number'))
{
    $config = array(
        'name'        => 'box_contain_number',
        'id'          => 'box_contain_number',
        'value'       => $product ? $product->box_contain_number : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('box_contain_number'),
        form_input($config),
    );
}

if (product_can_read('box_total_weight'))
{
    $config = array(
        'name'        => 'box_total_weight',
        'id'          => 'box_total_weight',
        'value'       => $product ? $product->box_total_weight : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('box_total_weight').' (g)',
        form_input($config),
    );
}

if (product_can_read('box_length'))
{
    $config = array(
        'name'        => 'box_length',
        'id'          => 'box_length',
        'value'       => $product ? $product->box_length : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('box_length').' (cm)',
        form_input($config),
    );
}

if (product_can_read('box_width'))
{
    $config = array(
        'name'        => 'box_width',
        'id'          => 'box_width',
        'value'       => $product ? $product->box_width : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('box_width').' (cm)',
        form_input($config),
    );
}

if (product_can_read('box_height'))
{
    $config = array(
        'name'        => 'box_height',
        'id'          => 'box_height',
        'value'       => $product ? $product->box_height : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('box_height').' (cm)',
        form_input($config),
    );
}

//if (product_can_read('stock_code'))
//{
    $config = array(
        'name'        => 'stock_code',
        'id'          => 'stock_code',
        'value'       => $product ? $product->stock_code : '',
        'maxlength'   => '20',
        'size'        => '20',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('stock_code')),
        form_input($config),
    );
//}

if (product_can_read('shelf_code'))
{
    $config = array(
        'name'        => 'shelf_code',
        'id'          => 'shelf_code',
        'value'       => $product ? $product->shelf_code : '',
        'maxlength'   => '20',
        'size'        => '20',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('shelf_code')),
        form_input($config),
    );
}

if (product_can_read('bulky_cargo'))
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
        'checked'     => TRUE,
        'style'       => 'margin:10px',
    );
    $bulky_cargo .= form_radio($config) . form_label(lang('no'));
    $data[] = array(
        $this->block->generate_required_mark(lang('bulky_cargo')),
        $bulky_cargo,
    );
}

if (product_can_read('description'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('description_en')),
        $product->description,
    );
}

if (product_can_read('short_description'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('short_description_en')),
        $product->short_description,
    );
}

if (product_can_read('description_cn'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('description_cn')),
        $product->description_cn,
    );
}

if (product_can_read('short_description_cn'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('short_description_cn')),
        $product->short_description_cn,
    );
}

if (product_can_read('min_stock_number'))
{
    $config_gz = array(
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
    foreach ($stock_code as $code)
    {
        switch ($code->stock_code) {
            case 'SZ':
                $min_stock_number_html .= ' SZ : '.form_input($config_gz).'<br/><br/>';
                break;
            case 'UK':
                $min_stock_number_html .= ' UK : '.form_input($config_uk).'<br/><br/>';
                break;
            case 'US':
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
        $min_stock_number_html,
    );
	$data[] = array(
        lang('make_sku_stock_count'),
        fetch_makeup_sku_count($product->sku),
    );
}

$options_p = array();

foreach ($all_out_packing as $out_packing)
{
    $options_p[$out_packing->status_id] = lang($out_packing->status_name);
}

if (product_can_read('packing_or_not'))
{
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_or_not')),
        isset($product->packing_or_not) && isset($options_p[$product->packing_or_not]) ? $options_p[$product->packing_or_not] : lang('no_packing'),
    );
}

if (product_can_read('packing_material'))
{
    $options = array(
        '01'         => '01',
        '02'         => '02',
        '03'         => '03',
        '04'         => '04',
        '05'         => '05',
        '06'         => '06',
        '07'         => '07',
        '08'         => '08',
        '09'         => '09',
        '10'         => '10',
        '11'         => '11',
        '12'         => '12',
        '13'         => '13',
        '14'         => '14',
        '15'         => '15',
        '16'         => '16',
        '17'         => '17',
        '18'         => '18',
        '19'         => '19',
        '20'         => '20',
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('packing_material')),
        form_dropdown('packing_material', $options, $product ? $product->packing_material : '01'),
    );
}
/**
 * Product ebay field .
 */
if (product_can_read('in_stock'))
{
    $options = array(
        '3'         => lang('in_stock'),
        '2'         => lang('clear_stock'),
        '1'         => lang('out_of_stock'),
    );
    $data[] = array(
        lang('sale_status'),
        form_dropdown('sale_status', $options, $product ? $product->sale_status : 'in_stock'),
    );
}

if (product_can_read('free_sale'))
{
    $forbidden_options = fetch_readable_statuses('ban_levels', TRUE);
       
    $forbidden_level_html = '';
    
    foreach (get_forbidden_level_obj($product->id) as $value)
    {
        $forbidden_level_html .= (isset ($forbidden_options[$value->ban_level]) ? $forbidden_options[$value->ban_level] : '') . '<br/>';
    }

    $data[] = array(
        lang('forbidden_level'),
        $forbidden_level_html,
    );
}

if (product_can_read('sale_amount_level'))
{
    $config = array(
        'name'        => 'sale_amount_level',
        'id'          => 'sale_amount_level',
        'value'       => $product ? $product->sale_amount_level : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('sale_amount_level'),
        form_input($config),
    );
}

if (product_can_read('sale_quota_level'))
{
    $config = array(
        'name'        => 'sale_quota_level',
        'id'          => 'sale_quota_level',
        'value'       => $product ? $product->sale_quota_level : '',
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('sale_quota_level'),
        form_input($config),
    );
}

$html = '';
foreach ($coefficient as $v) {
    $html .= '<label onclick="get_coefficient('.$v->lowest_profit.')">  '.$v->name_cn.' : '.$v->lowest_profit.'  </label>';
}

if (product_can_read('lowest_profit'))
{
    $config = array(
        'name'        => 'lowest_profit',
        'id'          => 'lowest_profit',
        'value'       => $product->lowest_profit == 0 ? '' : $product->lowest_profit,
        'maxlength'   => '10',
        'size'        => '10',
    );
    $data[] = array(
        lang('lowest_profit'),
        form_input($config).$html,
    );
}

if (product_can_read('product_images_view'))
{
    $base_url = base_url();
    
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

        $image_html .= "<span style='margin-right: 10px;'><a href='$src' target='_blank'>" . img($image_properties) . "</a></span>";
    }
    $image_html .= '</div>';
    $data[] = array(
        lang('product_images_view'),
        $image_html,
    );
}
//显示广告模版代码的文件
if (product_can_read('ebay_images_view'))
{
    $ad_html = '<div style="height: 120px;" id="ad_code_uploaded">';
    foreach ($ad_code_uploaded as $ad_code)
    {
        $src = trim($base_url, '/') . trim($upload_file_path, '.') . $ad_code;
        $param = "{file_name: '$ad_code', 'sku': '{$product->sku}'}";
        $delete_link = block_drop_icon(site_url('pi/product/delete_ad_code'), $param, TRUE, NULL, '$(this).previous(0)');

        $ad_html .= "<span style='margin-right: 10px;'><a href='$src' target='_blank'>" . $ad_code . "</a></span>";
    }
    $ad_html .= '</div>';
    $data[] = array(
        lang('ad_code_view'),
        $ad_html,
    );
}
//结束了
if (product_can_read('picture_url'))
{
    $config = array(
        'name'        => 'picture_url',
        'id'          => 'picture_url',
        'value'       => $product ? $product->picture_url : '',
        'maxlength'   => '200',
        'size'        => '100',
    );
    $data[] = array(
        lang('picture_url'),
        form_input($config),
    );
}


$back_button = $this->block->generate_back_icon(site_url('pi/product/view_list'), 'main-content-detail', 'main-content');
$title = lang('product_detail') . $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'product_form',
);
echo form_open(site_url('pi/product/save_edit'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('pi/product/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save product!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_content(this, '$url');",
);
echo form_hidden('product_id', $product ? $product->pid : '-1');
if( ! isset ($action))
{
   echo '<h2>'.form_input($config).$back_button.'</h2>';
}

echo form_close();

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
        extand_catalog(<?=$js_array?>);
    });
</script>