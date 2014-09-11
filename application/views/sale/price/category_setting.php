<?php
$url = site_url('sale/setting/add_currency_category');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('eshop_code'),
    lang('category'),
//    lang('start_price'),
//    lang('end_price'),
//    lang('eshop_formula'),
//    lang('remark'),
    lang('created_date'),
    lang('creator'),
    lang('options') . $add_button,
);

$code_collection = "[";
foreach ($all_codes as $code)
{
    $code_collection .= "['{$code->code}', '{$code->name}'],";
}
$code_collection .= "]";

//$mode_collection = "[";
//foreach ($all_modes as $mode)
//{
//    $mode_collection .= "['{$mode->mode}', '{$mode->name}'],";
//}
//$mode_collection .= "]";

//$auction = lang('auction');
//$buy_now = lang('buy_now');
//$model_collection = "[['buy_now', '$buy_now'],['auction', '$auction']]";


$data = array();
$code_url = site_url('sale/setting/verigy_exchange_category');
foreach ($categories as $category)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/setting/drop_category',
        "{id: $category->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("eshop_code_{$category->id}", isset($category)&&$category->eshop_code !='[edit]' ?  $category->code_name : '[edit]'),
        $this->block->generate_div("category_{$category->id}", isset($category)&&$category->category !='[edit]' ?  $category->category : '[edit]'),
//        $this->block->generate_div("start_price_{$category->id}", isset($category) ?  $category->start_price : 0),
//        $this->block->generate_div("end_price_{$category->id}", isset($category) ?  $category->end_price : 0),
//        $this->block->generate_div("formula_{$category->id}", isset($category) ?  $category->formula : '[edit]'),
//        $this->block->generate_div("remark_{$category->id}", isset($category) ?  $category->remark : '[edit]'),
        $category->created_date,
        $category->u_name,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "eshop_code_{$category->id}",
        'category_form',
        $code_url,
        "{id: $category->id, type: 'eshop_code'}",
        $code_collection
    );
    echo $this->block->generate_editor(
        "category_{$category->id}",
        'category_form',
        $code_url,
        "{id: $category->id, type: 'category'}"
//        $mode_collection
    );
//    echo $this->block->generate_editor(
//        "start_price_{$category->id}",
//        'trade_form',
//        $code_url,
//        "{id: $category->id, type: 'start_price'}"
//    );
//    echo $this->block->generate_editor(
//        "end_price_{$category->id}",
//        'trade_form',
//        $code_url,
//        "{id: $category->id, type: 'end_price'}"
//    );
//    echo $this->block->generate_editor(
//        "formula_{$category->id}",
//        'trade_form',
//        $code_url,
//        "{id: $category->id, type: 'formula'}"
//    );
//    echo $this->block->generate_editor(
//        "remark_{$category->id}",
//        'trade_form',
//        $code_url,
//        "{id: $category->id, type: 'remark'}"
//    );
}
$title = lang('product_category_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
