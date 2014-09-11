<?php
$url = site_url('sale/ebay_manage/add_saler_ebay_id');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('saler'),
    lang('ebay_id'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('sale/ebay_manage/verigy_exchange_saler_ebay_id');
foreach ($ebays as $ebay)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/ebay_manage/drop_saler_ebay_id_by_id',
        "{id: $ebay->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("saler_id_{$ebay->id}", isset($ebay) &&$ebay->u_name  ?  $ebay->u_name : '[edit]'),
        $this->block->generate_div("ebay_id_str_{$ebay->id}", isset($ebay) ?  $ebay->ebay_id_str : '[edit]'),
        $drop_button,
    );

    $sale_users = $this->user_model->fetch_users_by_system_code('sale');
    $salers_options = array();
    foreach($sale_users as $sale_user)
    {
        $salers_options[$sale_user->u_id] = $sale_user->u_name;
    }
    $collection = to_js_array($salers_options);
    
    echo $this->block->generate_editor(
        "saler_id_{$ebay->id}",
        'ebay_form',
        $code_url,
        "{id: $ebay->id, type: 'saler_id'}",
        $collection
    );
    echo $this->block->generate_editor(
        "ebay_id_str_{$ebay->id}",
        'ebay_form',
        $code_url,
        "{id: $ebay->id, type: 'ebay_id_str'}"
    );
}
$title = lang('saler_ebay_id_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
