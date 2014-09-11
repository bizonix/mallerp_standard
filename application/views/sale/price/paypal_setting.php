<?php
$url = site_url('sale/setting/add_currency_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('paypal_name'),
    lang('paypal_function'),
    lang('created_date'),
    lang('creator'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('sale/setting/verigy_exchange_paypal');
foreach ($paypals as $paypal)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/setting/drop_paypal',
        "{id: $paypal->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("name_{$paypal->id}", isset($paypal) ?  $paypal->name : '[edit]'),
        $this->block->generate_div("formula_{$paypal->id}", isset($paypal) ?  $paypal->formula : '[0]'),
        $paypal->created_date,
        $paypal->u_name,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "name_{$paypal->id}",
        'paypal_form',
        $code_url,
        "{id: $paypal->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "formula_{$paypal->id}",
        'paypal_form',
        $code_url,
        "{id: $paypal->id, type: 'formula'}"
    );
}
$title = lang('paypal_fee_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
