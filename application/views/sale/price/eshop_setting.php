<?php
$url = site_url('sale/setting/add_currency_eshop');
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
$code_collection = "[['other_category','{$select_all}'],";
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
$code_url = site_url('sale/setting/verigy_exchange_eshop');

foreach ($eshops as $eshop)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/setting/drop_eshop',
        "{id: $eshop->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("eshop_code_{$eshop->id}", isset($eshop)&&$eshop->eshop_code !='[edit]' ?  $eshop->code_name : '[edit]'),
        $this->block->generate_div("sale_mode_{$eshop->id}", isset($eshop)&&$eshop->sale_mode !='[edit]' ?  lang($eshop->sale_mode) : '[edit]'),
        $this->block->generate_div("category_{$eshop->id}", isset($eshop)&&$eshop->category !='[edit]'? ($eshop->category_id ==0 ? $code_arr["other_category"] : $code_arr["$eshop->ec_code"] .' : '. $eshop->category) : '[edit]'),
        $this->block->generate_div("start_price_{$eshop->id}", isset($eshop) ?  $eshop->start_price : 0),
        $this->block->generate_div("end_price_{$eshop->id}", isset($eshop) ?  $eshop->end_price : 0),
        $this->block->generate_div("formula_{$eshop->id}", isset($eshop) ?  $eshop->formula : '[edit]'),
        $this->block->generate_div("remark_{$eshop->id}", isset($eshop) ?  $eshop->remark : '[edit]'),
        $eshop->created_date,
        $eshop->u_name,
        $drop_button,
    );

    $categories = get_categories_by_eshop_code($eshop->eshop_code);

    $select_all = lang('other_category');

    $category_collection = "[['0','{$select_all}'],";
    foreach ($categories as $category)
    {
        $name =  $category->code_name .' : '. $category->category ;
        $category_collection .= "['{$category->id}', '{$name}'],";
    }
    $category_collection .= "]";

    echo $this->block->generate_editor(
        "eshop_code_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'eshop_code'}",
        $code_collection
    );
    echo $this->block->generate_editor(
        "sale_mode_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'sale_mode'}",
        $mode_collection
    );
    echo $this->block->generate_editor(
        "category_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'category_id'}",
        $category_collection
    );
    echo $this->block->generate_editor(
        "start_price_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'start_price'}"
    );
    echo $this->block->generate_editor(
        "end_price_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'end_price'}"
    );
    echo $this->block->generate_editor(
        "formula_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'formula'}"
    );
    echo $this->block->generate_editor(
        "remark_{$eshop->id}",
        'eshop_form',
        $code_url,
        "{id: $eshop->id, type: 'remark'}"
    );
}
$title = lang('eshop_fee_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
