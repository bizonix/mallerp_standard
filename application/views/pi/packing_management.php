<?php
$head = array(
    lang('chinese_name'),
    lang('english_name'),
    lang('image_url'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($product_packings as $packing)
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'pi/packing/drop_product_packing',
            "{id: $packing->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('pi/packing/edit', array($packing->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('pi/packing/view', array($packing->id)));
    }
    $data[] = array(
        $packing->name_cn,
        $packing->name_en,
        block_image($packing->image_url),
        $packing->created_date,
        $url,
    );
}

echo block_header(lang('packing_management'));
echo $this->block->generate_table($head, $data);


?>
