<?php

$today_all_orders_detail = '';
if(empty ($apply_list))
{
    $today_all_orders_detail .= lang('no_orders');
}
else 
{
    foreach ($apply_list as $apply) 
    {
        $today_all_orders_detail .= "【 $apply->sign 】 <br/>";
        foreach ($all_list_detail["$apply->id"]['boxes_info'] as $box) 
        {
            $today_all_orders_detail .= '------'.$box->case_no . ' : ';
            foreach ($all_list_detail["$apply->id"]['products_info']["$box->id"] as $product) 
            {
                $today_all_orders_detail .= "SKU : $product->title ,  Qty : $product->quantity,  申报名称 : $product->declared_name,  申报价值 : $product->declared_price ;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            $today_all_orders_detail .= '<br/>';
        }
    }
}

$head = array(
    lang('name'),
    lang('value'),
);

$options = array(
    lang('ocean_shipping') => lang('ocean_shipping'),
    lang('air_express') => lang('air_express'),
    'EMS' => 'EMS',
    'DHL' => 'DHL',
    'UPS' => 'UPS',
);

$data[] = array(
    $this->block->generate_required_mark(lang('log_type')),
    form_dropdown('log_type', $options, isset ($apply_obj) ? $apply_obj->log_type : lang('air_express'))
);

$options = array(
    'UK' => 'UK',
    'DE' => 'DE',
    'AU' => 'AU',
	'YB' => 'YB',
);

$data[] = array(
    $this->block->generate_required_mark(lang('storage_code')),
    form_dropdown('storage_code', $options,  isset ($apply_obj) ? $apply_obj->storage_code : '1')
);

$data[] = array(
    $this->block->generate_required_mark(lang('arrive_time')),
    block_time_picker('arrive_time', (isset ($apply_obj) ? $apply_obj->arrive_time : (date('Y-m-d', mktime(00, 00, 00, date("m"), date("d")+3, date("Y")))))),
);

$options = array(
	lang('GuangZhou') => lang('GuangZhou'),
    lang('ShenZhen') => lang('ShenZhen'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('locale')),
    form_dropdown('locale', $options, isset ($apply_obj) ? $apply_obj->locale : '1')
);

$options = array(
    '1' => lang('yes'),
    '0' => lang('no'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('is_collect')),
    form_dropdown('is_collect', $options, isset ($apply_obj) ? $apply_obj->is_collect : '1')
);

$tomorrow  = mktime(00, 00, 00, date("m")  , date("d")+1, date("Y"));

$data[] = array(
    $this->block->generate_required_mark(lang('collect_time')),
    block_time_picker('collect_time', isset ($apply_obj) ? $apply_obj->collect_time : date('Y-m-d',$tomorrow)." 12:00:00"),
);

$config = array(
      'name'        => 'collect_address',
      'id'          => 'collect_address',
      'value'       => isset ($apply_obj) ? $apply_obj->collect_address : lang('mallerp_company_address'),
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('collect_address')),
    form_input($config),
);

$options = array(
    '许先生' => '许先生',
    '发货员' => '发货员',
);

$data[] = array(
    $this->block->generate_required_mark(lang('collect_contact')),
    form_dropdown('collect_contact', $options, isset ($apply_obj) ? $apply_obj->collect_contact : '1')
);

$options = array(
    '0755-83998006-8020' => '0755-83998006-8020',
    '0755-83998006-8021' => '0755-83998006-8021',
);

$data[] = array(
    $this->block->generate_required_mark(lang('collect_phone')),
    form_dropdown('collect_phone', $options, isset ($apply_obj) ? $apply_obj->collect_phone : '1')
);

$config = array(
      'name'        => 'abroad_stock_remark',
      'id'          => 'abroad_stock_remark',
      'value'       => isset ($apply_obj) ? $apply_obj->remark : '',
      'maxlength'   => '200',
      'size'        => '80',
);
$data[] = array(
    lang('abroad_stock_remark'),
    form_input($config),
);

echo "<div id='in_store_list_div'>";

echo block_notice_div($today_all_orders_detail);
echo '<br/>';
$title = lang('bfe_in_store_apply');

echo block_header($title);

$attributes = array(
    'id' => 'in_store_list_info_form',
);

$url = site_url('stock/abroad_stock/in_store_case');

echo form_open($url, $attributes);

echo form_hidden('list_id', isset ($apply_obj) ? $apply_obj->id : '-1');

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('next'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$url',$('in_store_list_info_form').serialize(true), 'in_store_list_div',1);",
);

echo '<h2>'.block_button($config).'</h2>';

echo form_close();

echo '</div>';

?>


<script>

window.onbeforeunload=function ()
{
    if( ! confirm("<?php echo lang('before_refurbish_info')?>"))
    {
        return false;
    }
}

</script>