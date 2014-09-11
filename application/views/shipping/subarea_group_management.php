<?php
$head = array(
    lang('subarea_group_name'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($subarea_groups as $subarea_group )
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'shipping/shipping_subarea_group/drop_subarea_group',
            "{id: $subarea_group->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('shipping/shipping_subarea_group/add_edit', array($subarea_group->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('shipping/shipping_subarea_group/view', array($subarea_group->id)));
    }
    $data[] = array(
        $subarea_group->subarea_group_name,
        $subarea_group->created_date,
        $url,
    );
}

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'subarea_group_name',
	),
	array(
		'type'      => 'input',
		'field'     => 'created_date',
	),
);

$config = array(
	'filters'    => $filters,
);

if ($action == 'edit')
{
    $title = lang('shipping_subarea_group_management');
}
else
{
    $title = lang('shipping_subarea_group_view');
}
echo block_header($title);

echo $this->block->generate_pagination('shipping_subarea_group');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'shipping_subarea_group');

echo form_close();

echo $this->block->generate_pagination('shipping_subarea_group');
?>
