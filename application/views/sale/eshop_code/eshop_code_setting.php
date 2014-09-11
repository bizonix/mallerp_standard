<?php
foreach($currencys as $currency)
{
    $currencys_options[$currency->code] = $currency->code;
}
$currency_collection = to_js_array($currencys_options);

foreach (range(1, 16) as $number)
{
   $order_options[$number] = $number;
}
$order_collection = to_js_array($order_options);

$url = site_url('sale/setting/add_eshop_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('code'),
    lang('currency_code'),
    lang('chinese_name'),
    lang('serial'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('sale/setting/verigy_eshop_code');
foreach ($eshop_codes as $eshop_code)
{

    $drop_button = $this->block->generate_drop_icon(
        'sale/setting/drop_eshop_code',
        "{id: $eshop_code->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("code_{$eshop_code->id}", isset($eshop_code) ?  $eshop_code->code : '[edit]'),
        $this->block->generate_div("currency_{$eshop_code->id}", isset($eshop_code) ?  $eshop_code->currency : '[edit]'),
        $this->block->generate_div("name_{$eshop_code->id}", isset($eshop_code) ?  $eshop_code->name : '[edit]'),
        $this->block->generate_div("order_{$eshop_code->id}", isset($eshop_code) ?  $eshop_code->order : '0'),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "code_{$eshop_code->id}",
        'eshop_code_form',
        $code_url,
        "{id: $eshop_code->id, type: 'code'}"
    );
    echo $this->block->generate_editor(
        "currency_{$eshop_code->id}",
        'eshop_code_form',
        $code_url,
        "{id: $eshop_code->id, type: 'currency'}",
        "$currency_collection"
    );
    echo $this->block->generate_editor(
        "name_{$eshop_code->id}",
        'eshop_code_form',
        $code_url,
        "{id: $eshop_code->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "order_{$eshop_code->id}",
        'eshop_code_form',
        $code_url,
        "{id: $eshop_code->id, type: 'order'}",
        "$order_collection"
    );

}
$title = lang('ebay_platform_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
