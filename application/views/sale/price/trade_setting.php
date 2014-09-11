<?php
$url = site_url('sale/setting/add_currency_trade');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('eshop_code'),
    lang('sale_mode'),
    lang('category'),
    lang('start_price'),
    lang('end_price'),
    lang('eshop_formula'),
    lang('remark'),
    lang('created_date'),
    lang('creator'),
    lang('options') . $add_button,
);

$select_all = lang('other_category');
$code_arr = array('other_category'=>$select_all);
$code_collection = "[";
foreach ($all_codes as $code)
{
    $code_collection .= "['{$code->code}', '{$code->name}'],";
    $code_arr[$code->code] = $code->name;
}
$code_collection .= "]";

$mode_collection = "[";
foreach ($all_modes as $mode)
{
    $mode_collection .= "['{$mode->mode}', '{$mode->name}'],";
}
$mode_collection .= "]";

$data = array();
$code_url = site_url('sale/setting/verigy_exchange_trade');
foreach ($trades as $trade)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/setting/drop_trade',
        "{id: $trade->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("eshop_code_{$trade->id}", isset($trade)&&$trade->eshop_code !='[edit]' ?  $trade->code_name : '[edit]'),
        $this->block->generate_div("sale_mode_{$trade->id}", isset($trade)&&$trade->sale_mode !='[edit]' ?  lang($trade->sale_mode) : '[edit]'),
        $this->block->generate_div("category_{$trade->id}", isset($trade)&&$trade->category !='[edit]'? ($trade->category_id ==0 ? $code_arr["other_category"] : $code_arr["$trade->ec_code"] .' : '. $trade->category) : '[edit]'),
        $this->block->generate_div("start_price_{$trade->id}", isset($trade) ?  $trade->start_price : 0),
        $this->block->generate_div("end_price_{$trade->id}", isset($trade) ?  $trade->end_price : 0),
        $this->block->generate_div("formula_{$trade->id}", isset($trade) ?  $trade->formula : '[edit]'),
        $this->block->generate_div("remark_{$trade->id}", isset($trade) ?  $trade->remark : '[edit]'),
        $trade->created_date,
        $trade->u_name,
        $drop_button,
    );

    $categories = get_categories_by_eshop_code($trade->eshop_code);

    $category_collection = "[['other_category','{$select_all}'],";
    foreach ($categories as $category)
    {
        $name =  $category->code_name .' : '. $category->category ;
        $category_collection .= "['{$category->id}', '{$name}'],";
    }
    $category_collection .= "]";
    
    echo $this->block->generate_editor(
        "eshop_code_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'eshop_code'}",
        $code_collection
    );
    echo $this->block->generate_editor(
        "sale_mode_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'sale_mode'}",
        $mode_collection
    );
    echo $this->block->generate_editor(
        "category_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'category_id'}",
        $category_collection
    );
    echo $this->block->generate_editor(
        "start_price_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'start_price'}"
    );
    echo $this->block->generate_editor(
        "end_price_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'end_price'}"
    );
    echo $this->block->generate_editor(
        "formula_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'formula'}"
    );
    echo $this->block->generate_editor(
        "remark_{$trade->id}",
        'trade_form',
        $code_url,
        "{id: $trade->id, type: 'remark'}"
    );
}
$title = lang('trade_fee_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
