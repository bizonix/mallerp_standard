<?php
$head = array(
array('text' => lang('sku'), 'sort_key' => 'sku_str', 'id' => 'mytaobao_list'),
lang('picture'),
array('text' => lang('sale_status'), 'sort_key' => 'sale_status_str'),
array('text' => lang('product_title'), 'sort_key' => 'title'),
array('text' => lang('price'), 'sort_key' => 'price_str'),
array('text' => lang('shipping_cost'), 'sort_key' => 'shipping_cost'),
array('text' => lang('item_id'), 'sort_key' => 'item_id'),
array('text' => lang('stock_count'), 'sort_key' => 'stock_count_str'),
array('text' => lang('online_update_time'), 'sort_key' => 'created'),
array('text' => lang('local_updated_date'), 'sort_key' => 'created_date'),
);
$data = array();
foreach ($taobao_manage as $show_taobao)
{
    $sku_str = str_replace(',', '<br/>', $show_taobao->sku_str);
    $stock_count_str = str_replace(',', '<br/>',$show_taobao->stock_count_str);
    $row = array();
    $item_url = 'http://item.taobao.com/item.htm?id=';
    $item_html = '<a target="_blank" href="' . $item_url . $show_taobao->item_id . '">'. $show_taobao->item_id .'</a>';
    $row[] = $sku_str;
    $row[] =  '<img src="'.$show_taobao->image_url.'" with="100" height="100" />';
    
    if(strpos("$show_taobao->sale_status_str", '2') !== FALSE) {
        $status = lang('clear_stock');
        $title ='<font color=orange>'.$show_taobao->title.'</font>';
    } elseif (strpos("$show_taobao->sale_status_str", '1') !== FALSE) {
        $status = lang('out_of_stock');
        $title ='<font color=red>'.$show_taobao->title.'</font>';
    } else {
        $status = lang('in_stock');
        $title =$show_taobao->title;
    }
    $row[] = $status;
    $row[] = $title;
    $row[] = $show_taobao->price_str;
    $row[] = $show_taobao->shipping_cost;
    $row[] = $item_html;
    $row[] = $stock_count_str;
    $row[] = $show_taobao->created;
    $row[] = $show_taobao->created_date;
    $data[] = $row;
}
$title = lang('toabao_manage');
echo block_header($title);
echo $this->block->generate_pagination('mytaobao_list');


$options = array('' => lang('please_select'), '2' => lang('clear_stock'), '1' => lang('out_of_stock'), '3' => lang('in_stock'));
$filters = array(
    array(
        'type' => 'input',
        'field' => 'sku_str',
    ),
    NULL,
    array(
        'type' => 'dropdown',
        'options' => $options,
        'field' => 'sale_status_str',
    ),
    array(
        'type' => 'input',
        'field' => 'title',
    ),
    array(
        'type' => 'input',
        'field' => 'price_str',
    ),
     array(
        'type' => 'input',
        'field' => 'shipping_cost',
    ),
    array(
        'type' => 'input',
        'field' => 'item_id',
    ),
    array(
        'type' => 'input',
        'field' => 'stock_count_str',
    ),
     array(
        'type' => 'input',
        'field' => 'created',
    ),
    array(
        'type' => 'input',
        'field' => 'created_date',
    )
);

$config = array(
    'filters' => $filters,
);

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters,'mytaobao_list');

echo form_close();
echo $this->block->generate_pagination('mytaobao_list');