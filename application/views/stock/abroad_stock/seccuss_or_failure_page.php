<?php

$box_and_product_html = '';
if(empty ($boxes_info))
{
    $box_and_product_html = "------ ".lang('null')." ------ <br/>";
}
else
{
    foreach ($boxes_info as $box)
    {
        $box_and_product_html .= " <br/> ".lang('case_number')." $box->case_no,　".lang('in_store_weight')." : $box->weight, ".lang('packing')." : $box->packing ; <br/>";

        if(empty ($products_info["$box->id"]))
        {
            $box_and_product_html .= "------ ".lang('product_info').lang('null')." ------ <br/>";
        }
        else
        {
            foreach ($products_info["$box->id"] as $value) 
            {
                $box_and_product_html .= "------ 【Skus : ".$value->title."】,【 Qties : ".$value->quantity."】,【 ".lang('declared_name')." : ".$value->declared_name."】,【 ".lang('declared_price')." : ".$value->declared_price."】<br/>";
            }
        }
    }

}

empty ($list_info->is_collect)?$is_collect=lang('no'):$is_collect=lang('yes');

if($list_info->status == 1)
{
    $status = lang('success');
}
else if($list_info->status == 0)
{
    $status = lang('not_validate');
}
else
{
    $status = lang('failure');
}

$list_info_html =<<<INFO
    <h4> 【入库申请表信息】 </h4>
    入库单号: $list_info->sign <br/>
    发送方式: $list_info->log_type <br/>
    目的仓库代码: $list_info->storage_code <br/>
    交货日期: $list_info->arrive_time <br/>
    海外仓储处理点: $list_info->locale <br/>
    是否需要上门收货: $is_collect <br/>
    收货时间: $list_info->collect_time <br/>
    收货地址: $list_info->collect_address <br/>
    收货联系人: $list_info->collect_contact <br/>
    收货联系电话: $list_info->collect_phone <br/>
    申请表状态: $status <br/>
    备注信息: $list_info->remark <br/><br/>

    <h4> 【入库装箱信息】 </h4>
    $box_and_product_html

INFO;

echo block_notice_div($list_info_html);
echo '<br/>';
echo block_header(lang('in_store_apply_reslut'));

$again_completed = site_url('stock/abroad_stock/in_store_apply_page', array('list_id'=>$list_id, 'status' => 1));
$config_completed = array(
    'name'        => 'submit',
    'value'       => lang('completed'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$again_completed', null, 'in_store_list_div',1);",
);

$url = site_url('stock/abroad_stock/in_store_case', array('again_list_id'=>$list_id, 'case_no'=>$case_no));
$config_again = array(
    'name'        => 'submit',
    'value'       => lang('again_add_box'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$url', null, 'in_store_list_div',1);",
);

$back_url = site_url('stock/abroad_stock/in_store_product', array('case_id'=>$case_id, 'tag' => 'product_back'));

$config_back = array(
    'name'        => 'submit_back',
    'value'       => lang('renext'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$back_url', null, 'in_store_list_div',1);",
);

echo '<h2 align="center">'.block_button($config_back).block_button($config_again).block_button($config_completed).'</h2>';

echo block_notice_div(lang('in_store_apply_notice'));

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