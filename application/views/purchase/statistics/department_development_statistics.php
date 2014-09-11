<?php
$head = array(
    lang('developer'),
    lang('sku'),
    lang('order_count'),
    lang('sale_product_count'),
    lang('sale_amount'),
);

$CI = & get_instance();

if($personal == get_current_user_id()) {
    $priority = 1;
} else
{
    $priority = $CI->user_model->fetch_user_priority_by_system_code('purchase');
}
if('developer' == $role)
{
    $developer_groups = $this->user_model->fetch_users_by_group_name('开发员');
    $groups = $developer_groups;
}
else
{
    $purchaser_groups = $this->user_model->fetch_users_by_group_name('采购员');
    $up_groups = $this->user_model->fetch_users_by_group_name('采购主管');
    $purchaser_groups = array_merge($purchaser_groups, $up_groups);
    
    $groups = $purchaser_groups;
}
$data = array();
$qties = array();
$orders = array();
$sku_prices = array();


foreach($groups as $group)
{
    if(!isset($statistics[$group->u_id]))
    {
        continue;
    }
    $index = 0;
    $total_price = 0;
    foreach ($statistics[$group->u_id]['skus'] as $sku)
    {

        $qty = $product['qty'][$sku];
        if (isset($sku_prices[$sku]))
        {
            $price = $sku_prices[$sku] * $qty;
        }
        else
        {
            $price = $CI->product_model->fetch_cost_by_sku($sku) * $qty;
        }
        $total_price += $price;


        $data[] = array(
            fetch_user_name_by_id($group->u_id),
            $sku,
            $product['order'][$sku],
            $qty,
            $price,
        );
        
        if( !isset($orders[$group->u_id]))
        {
            $orders[$group->u_id] = $product['order'][$sku];
        }
        else
        {
            $orders[$group->u_id] += $product['order'][$sku];
        }

        if( !isset($qties[$group->u_id]))
        {
            $qties[$group->u_id] = $qty;
        }
        else
        {
            $qties[$group->u_id] += $qty;
        }



        $index++;
    }
    $data[] = array(
        fetch_user_name_by_id($group->u_id) . '(' .lang('statistics').')',
        lang('develop_product_count') . ': ' . $index,
        lang('total_order_count') . ': '. $orders[$group->u_id],
        lang('sale_total_product_count') . ': '. $qties[$group->u_id],
        lang('total_sale_amount') . ': '. $total_price,
);

}

if($priority >1 || $CI->is_super_user())
{
    if('developer' == $role)
    {
        echo block_header(lang('develop_department_statistics'));
    }
    else
    {
        echo block_header(lang('purchase_department_statistics'));
    }
}
else
{
    echo block_header(lang('personal_development_statistical'));
}

echo '<br/>';
echo form_open(site_url('purchase/statistics/department_development_statistics', array('role' => $role)));
echo lang('develop_time') . ': '. lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '<br/><br/>';
echo lang('sale_time') .': '. lang('from') . ' ' . block_time_picker('sale_begin_time', $sale_begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('sale_end_time', $sale_end_time) . '&nbsp;&nbsp;';
if($personal == get_current_user_id()) {
    echo form_hidden('personal',  get_current_user_id());
}


$developer_options = array(
    ''   => lang('all'),
);
if('developer' == $role)
{
    foreach ($developer_groups as $group)
    {
        $developer_options[$group->u_id] = $group->u_name;
    }
}
else
{    
    foreach ($purchaser_groups as $group)
    {
        $developer_options[$group->u_id] = $group->u_name;
    }  
}

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
if($priority >1 || $CI->is_super_user())
{
    echo form_dropdown('developer_id', $developer_options, $current_purchaser_id) . block_button($config);
}
else
{
    echo  block_button($config);
}
echo form_close();
$sortable = array(
    'default',
    'default',
    'default',
    'default',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
