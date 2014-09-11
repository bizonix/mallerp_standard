<?php
$add_button = $this->block->generate_add_icon('order/confirm_order_condition/add_country_and_amount');
$head = array(
    lang('countries'),
    lang('money'),
    lang('created_date'),
    lang('options') . $add_button
);

$url = site_url('order/confirm_order_condition/update_country_and_amount');

$data = array();
foreach ($rows as $row)
{
    $drop_button = block_drop_icon(
        'order/confirm_order_condition/drop_country_and_amount',
        "{id: $row->id }",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("country_$row->id", !empty ($row->country) ? $row->country : 'Edit'),
        $this->block->generate_div("amount_{$row->id}", empty($row->amount) ?    '[edit]' : price($row->amount)),
        $row->created_date,
        $drop_button,
    );
        
    echo $this->block->generate_editor(
        "country_{$row->id}",
        'country_code_form',
        $url,
        "{id: $row->id, type: 'country'}"
    );
        
    echo $this->block->generate_editor(
        "amount_$row->id",
        'wait_confirm_sku_form',
        $url,
        "{id: $row->id, type: 'amount'}"
    );
}
echo block_header(lang('confirm_order_condition'));
echo block_table($head, $data);

?>
