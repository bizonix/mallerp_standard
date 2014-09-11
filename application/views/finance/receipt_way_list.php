<?php
$url = site_url('finance/receipt_way/add_receipt_way');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('receipt_way'),
    lang('update_date'),    
    lang('options') . $add_button,
);

$data = array();
$receipt_way_url = site_url('finance/receipt_way/verify_receipt_way');
foreach ($receipt_ways as $receipt_way)
{

    $drop_button = $this->block->generate_drop_icon(
        'finance/receipt_way/drop_receipt_way',
        "{id: $receipt_way->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("receipt_name_{$receipt_way->id}", isset($receipt_way) ?  $receipt_way->receipt_name: '[edit]'),     
        $receipt_way->created_date,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "receipt_name_{$receipt_way->id}",
        'receipt_way_form',
        $receipt_way_url,
        "{id: $receipt_way->id, type: 'receipt_name'}"
    );
   
}
$title = lang('receipt_way_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
