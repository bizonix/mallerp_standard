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

$head = array("");

$config_sku = array(
      'name'        => 'sku[]',
      'value'       => '',
      'maxlength'   => '20',
      'size'        => '20',
);

$config_qty = array(
      'name'        => 'qty[]',
      'value'       => '',
      'maxlength'   => '10',
      'size'        => '10',
);

$config_name = array(
      'name'        => 'declared_name[]',
      'id'          => 'declared_name',
      'value'       => '',
      'maxlength'   => '50',
      'size'        => '50',
);

$config_price = array(
      'name'        => 'declared_price[]',
      'id'          => 'declared_price',
      'value'       => '',
      'maxlength'   => '15',
      'size'        => '15',
);

$base_url = base_url();

$sku_and_qty_html = '&nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty);

if(isset ($products) && $products)
{
    $n= 1;
    $counts = count($products);
    
    foreach ($products as $product)
    { 	
        $config_sku['value'] = $product->title;
        $config_qty['value'] = $product->quantity;
        $config_name['value'] = $product->declared_name;
        $config_price['value'] = $product->declared_price;
        if($counts>1)
        {
            $cancel_icon_span = <<<SPAN
<span style="cursor:pointer;" onclick="$(this.parentNode.parentNode).remove();"><img src="{$base_url}static/images/icons/cancel.gif"/></span>
SPAN;
            if($n == 1 )
            {
                $data[] = array(
                    '<div style="margin: 5px;"> &nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty).'&nbsp;&nbsp;&nbsp;'.lang('declared_name').' : '.form_input($config_name).'&nbsp;&nbsp;&nbsp;'.lang('declared_price').' : '.form_input($config_price).$this->block->generate_add_icon_only("add_sku_and_qty('$base_url');").'</div>',
                ); 
                   
                $n++;
            }
            else if($n ==  $counts)
            {
                $data[] = array(
                    '<div style="margin: 5px;"> &nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty).'&nbsp;&nbsp;&nbsp;'.lang('declared_name').' : '.form_input($config_name).'&nbsp;&nbsp;&nbsp;'.lang('declared_price').' : '.form_input($config_price).$cancel_icon_span.'</div><div id="sku_and_qty"></div>',
                );
            }
            else
            {
                $data[] = array(
                    '<div style="margin: 5px;"> &nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty).'&nbsp;&nbsp;&nbsp;'.lang('declared_name').' : '.form_input($config_name).'&nbsp;&nbsp;&nbsp;'.lang('declared_price').' : '.form_input($config_price).$cancel_icon_span.'</div>',
                ); 
            
                $n++;
            }
            
        }
        else
        {
            $data[] = array(
            '&nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty).'&nbsp;&nbsp;&nbsp;'.lang('declared_name').' : '.form_input($config_name).'&nbsp;&nbsp;&nbsp;'.lang('declared_price').' : '.form_input($config_price).$this->block->generate_add_icon_only("add_sku_and_qty('$base_url');").'<div id="sku_and_qty"></div>',
        ); 
        }
       
    }
}
else
{
    $data[] = array(
        '&nbsp;SKU : '.form_input($config_sku).' Qty : '.form_input($config_qty).'&nbsp;&nbsp;&nbsp;'.lang('declared_name').' : '.form_input($config_name).'&nbsp;&nbsp;&nbsp;'.lang('declared_price').' : '.form_input($config_price).$this->block->generate_add_icon_only("add_sku_and_qty('$base_url');").'<div id="sku_and_qty"></div>',
    );
}

$title = lang('in_store_product_detail').'('.lang('case_number')."$case_no".')';
echo '<br/>';
echo block_header($title);

$attributes = array(
    'id' => 'in_store_product_info_form',
);

$url = site_url('stock/abroad_stock/seccuss_or_failure');

echo form_open($url, $attributes);

echo form_hidden('list_id', $list_id);
echo form_hidden('case_id', $case_id);
echo form_hidden('case_no', $case_no);

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('next'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$url', $('in_store_product_info_form').serialize(true), 'in_store_list_div',1);",
);

$back_url = site_url('stock/abroad_stock/in_store_case', array('list_id'=>$case_id, 'status' => 0));

$config_back = array(
    'name'        => 'submit_back',
    'value'       => lang('renext'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "clear_div();helper.update_content('$back_url', null, 'in_store_list_div',1);",
);

echo '<h2>'.block_button($config_back).block_button($config).'</h2>';
echo form_close();

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