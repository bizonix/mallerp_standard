<?php
$head = array(
    lang('subarea_name'),
    lang('group_name'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($subareas as $subarea )
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'shipping/shipping_subarea/drop_subarea',
            "{id: $subarea->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('shipping/shipping_subarea/add_edit', array($subarea->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('shipping/shipping_subarea/view', array($subarea->id)));
    }
    $data[] = array(
        $subarea->subarea_name,
        $subarea->group_name,
        $subarea->created_date,
        $url,
    );
}

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'subarea_name',
	),
    array(
        'type'      => 'input',
        'field'     => 'shipping_subarea_group.subarea_group_name',
    ),
);

$config = array(
	'filters'    => $filters,
);


if ($action == 'edit')
{
    $title = lang('shipping_subarea_management');
}
else
{
    $title = lang('shipping_subarea_view');
}
echo block_header($title);

echo $this->block->generate_pagination('shipping_subarea');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'shipping_subarea');

echo form_close();

echo $this->block->generate_pagination('shipping_subarea');
?>
