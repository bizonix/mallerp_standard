<?php
$url = site_url('stock/inout/add_oustock_type');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('outstock_type'),
    lang('is_saled_order'),
    lang('created_date'),
    lang('creator'),
    lang('options') . $add_button,
);

$data = array();
$update_outstock_url = site_url('stock/inout/verify_outstock_type');
foreach ($outstock_types as $outstock_type)
{

    $drop_button = $this->block->generate_drop_icon(
        'stock/inout/drop_outstock_type',
        "{id: $outstock_type->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("type_{$outstock_type->id}", isset($outstock_type) ?  $outstock_type->type: '[edit]'),
        $this->block->generate_div("saled_{$outstock_type->id}", empty ($outstock_type->is_saled) ? lang('no') : lang('yes')),
        $outstock_type->created_date,
        $outstock_type->creator,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "type_{$outstock_type->id}",
        'type_form',
         $update_outstock_url,
        "{id: $outstock_type->id, type: 'type'}"
    );

    $collection = array(
        '1'=>lang('yes'),
        '0'=>lang('no')
    );

    echo $this->block->generate_editor(
        "saled_{$outstock_type->id}",
        'type_form',
        $update_outstock_url,
        "{id: $outstock_type->id, type: 'is_saled'}",
        to_js_array($collection)
    );

}
$title = lang('outstock_type_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
