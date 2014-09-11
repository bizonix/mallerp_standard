<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data = array();
$config = array(
    'name'        => 'name_cn',
    'id'          => 'name_cn',
    'value'       =>  isset($product_catalog) ? $product_catalog->name_cn : '',
    'maxlength'   => '50',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('chinese_name')),
    form_input($config),
);

$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'value'       =>  isset($product_catalog) ? $product_catalog->name_en : '',
    'maxlength'   => '50',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('english_name')),
    form_input($config),
);

$str = form_dropdown('parent', $parent, isset($product_catalog) ? $product_catalog->parent : '0');

$data[] = array(
    lang('parent_catalog'),
    $str,
);

$config = array(
    'name'        => 'lowest_profit',
    'id'          => 'lowest_profit',
    'value'       =>  isset($product_catalog) && $product_catalog->lowest_profit > 0 ? $product_catalog->lowest_profit : 0.3,
    'maxlength'   => '10',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('lowest_profit')),
    form_input($config),
);

$config = array(
    'name'        => 'packing_difficulty_factor',
    'id'          => 'packing_difficulty_factor',
    'value'       =>  isset($product_catalog) && $product_catalog->packing_difficulty_factor > 0 ? $product_catalog->packing_difficulty_factor : 0.6,
    'maxlength'   => '10',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('packing_difficulty_factor').lang('easy_to_difficult')),
    form_input($config),
);

$options = array(
    lang('all_platform')    => lang('all_platform'),
    lang('ebay_us')         => lang('ebay_us'),
    lang('ebay_uk')         => lang('ebay_uk'),
    lang('ebay_au')         => lang('ebay_au'),
);
$data[] = array(
    $this->block->generate_required_mark(lang('third_platform')),
    form_dropdown('third_platform', $options, isset($product_catalog) ? $product_catalog->third_platform : lang('all_platform')),
);

$options = array('-1' => lang('multi_users'));
foreach ($all_purchase_users as $purchase_user)
{
    $options[$purchase_user->u_id] = $purchase_user->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('purchaser')),
    form_dropdown('purchase_user', $options, isset($product_catalog) ? $product_catalog->purchaser_id : NULL),
);

$options = array();
foreach ($all_stock_users as $stock_user)
{
    $options[$stock_user->u_id] = $stock_user->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('stocker')),
    form_dropdown('stocker', $options, isset($product_catalog) && ( ! empty($product_catalog->stock_user_id)) ? $product_catalog->stock_user_id : $parent_catalog),
);


$options_testers = array();
foreach ($all_qt_users as $tester)
{
    $options_testers[$tester->u_id] = $tester->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('tester')),
    form_dropdown('tester', $options_testers, isset($product_catalog) && ( ! empty($product_catalog->tester_id)) ? $product_catalog->tester_id : $parent_catalog_tester,'id = "tester_id"'),
);

$options_seoers = array();
foreach ($all_seo_users as $seoer)
{
    $options_seoers[$seoer->u_id] = $seoer->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('seoer')),
    form_dropdown('seo_user_id', $options_seoers, isset($product_catalog) && ( ! empty($product_catalog->seo_user_id)) ? $product_catalog->seo_user_id : $parent_catalog_seoer,'id = "seo_user_id"'),
);

if ( ! isset($purchase_user_permissions))
{
    $purchase_user_permissions = array();
}
$saler_permission = $this->block->generate_permissions($all_sale_users, $purchase_user_permissions, 'saler_id', 'saler_permissions[]');
$data[] = array(
    $this->block->generate_required_mark(lang('saler_permission')),
     block_check_group('saler_permissions[]', $saler_permission),
);

if (isset($product_catalog))
{
    $title = lang('edit_a_catalog');
}
else 
{
    $title = lang('add_a_new_catalog');
}
$back_button = $this->block->generate_back_icon(site_url('pi/catalog/manage'));
echo block_header($title.$back_button);
$attributes = array(
    'id' => 'catalog_form',
);
echo form_open(site_url('pi/catalog/save_catalog'), $attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('pi/catalog/save_catalog');

$old_tester = isset($product_catalog) ? $product_catalog->tester_id : '';
$old_seo_user_id = isset($product_catalog) ? $product_catalog->seo_user_id: '';
$lang = lang('check_tester');
$lang_seo = lang('check_seo_user_id');

$config = array(
    'name'        => 'submit',
    'value'       => 'Save product!',
    'type'        => 'button',
    'style'       => 'margin:10px',
//    'onclick'     => "this.blur();helper.ajax('$url',$('catalog_form').serialize(true), 1);",
    'onclick'     => "check_tester_data('$url','$old_tester','$lang','$old_seo_user_id','$lang_seo')",
);
echo form_hidden('catalog_id', isset($product_catalog) ? $product_catalog->id : '-1');
echo block_button($config) . $back_button;

$js_array = '$A([';
if (isset($product_catalog))
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
    });
    
    function check_tester_data(url,old_tester,lang,old_seo_user,lang_seo)
    {
        if($('tester_id').value != old_tester)
        {
            if(confirm(lang))
            {
                this.blur();
                helper.ajax(url,$('catalog_form').serialize(true), 1);
                return;
            }
        }
        else if($('seo_user_id').value != old_seo_user)
        {
            if(confirm(lang_seo))
            {
                this.blur();
                helper.ajax(url,$('catalog_form').serialize(true), 1);
                return;
            }
        }
        else
        {
            this.blur();
            helper.ajax(url,$('catalog_form').serialize(true), 1);
            return;
        }
    }
    
    
</script>