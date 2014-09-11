<?php

$add_button = $this->block->generate_add_icon('order/confirm_order_condition/add_wait_confirm_sku');
$head = array(
    lang('sku'),
    lang('continent'),
    lang('created_date'),
    lang('options') . $add_button
);

$url = site_url('order/confirm_order_condition/update_wait_confirm_sku');

$data = array();
foreach ($skus as $sku)
{
    $drop_button = block_drop_icon(
        'order/confirm_order_condition/drop_wait_confirm_sku',
        "{id: $sku->id }",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("sku_$sku->id", !empty ($sku->sku) ? $sku->sku : 'Edit'),
        $this->block->generate_div("continent_id_{$sku->id}", empty($sku->continent_id) ?    '[edit]' : $sku->continent_name),
        $sku->created_date,
        $drop_button,
    );
  
    $option_continent = array();
    foreach ($continents as $continent)
    {
        $option_continent["$continent->id"] = $continent->name_cn;
    }
    $option_continent = to_js_array($option_continent);
        
    echo $this->block->generate_editor(
        "continent_id_{$sku->id}",
        'country_code_form',
        $url,
        "{id: $sku->id, type: 'continent_id'}",
        $option_continent
    );
        
    echo $this->block->generate_editor(
        "sku_$sku->id",
        'wait_confirm_sku_form',
        $url,
        "{id: $sku->id, type: 'sku'}"
    );
}
echo block_header(lang('confirm_order_condition'));
echo block_table($head, $data);

?>
