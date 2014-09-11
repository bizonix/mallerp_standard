<?php
$url = site_url('finance/rate/add_currency_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('currency_code'),
    lang('english_name'),
    lang('chinese_name'),
    lang('exchange_rate'),
    lang('update_date'),
    lang('update_user'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('finance/rate/verigy_exchange_rate');
foreach ($exchange_rates as $exchange_rate)
{
    
    $drop_button = $this->block->generate_drop_icon(
        'finance/rate/drop_exchange_rate',
        "{id: $exchange_rate->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("code_{$exchange_rate->id}", isset($exchange_rate) ?  $exchange_rate->code : '[edit]'),
        $this->block->generate_div("name_en_{$exchange_rate->id}", isset($exchange_rate) ?  $exchange_rate->name_en : '[edit]'),
        $this->block->generate_div("name_cn_{$exchange_rate->id}", isset($exchange_rate) ?  $exchange_rate->name_cn : '[edit]'),
        $this->block->generate_div("ex_rate_{$exchange_rate->id}", isset($exchange_rate) ?  $exchange_rate->ex_rate : '[0]'),
        $exchange_rate->update_date,
        $exchange_rate->update_user,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "code_{$exchange_rate->id}",
        'rate_form',
        $code_url,
        "{id: $exchange_rate->id, type: 'code'}"
    );
    echo $this->block->generate_editor(
        "name_en_{$exchange_rate->id}",
        'rate_form',
        $code_url,
        "{id: $exchange_rate->id, type: 'name_en'}"
    );
    echo $this->block->generate_editor(
        "name_cn_{$exchange_rate->id}",
        'rate_form',
        $code_url,
        "{id: $exchange_rate->id, type: 'name_cn'}"
    );
    echo $this->block->generate_editor(
        "ex_rate_{$exchange_rate->id}",
        'rate_form',
        $code_url,
        "{id: $exchange_rate->id, type: 'ex_rate'}"
    );
        
}
$title = lang('exchange_rate_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
