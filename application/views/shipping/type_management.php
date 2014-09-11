<?php
$head = array(
    lang('type_name'),
    lang('shipping_code'),
    lang('arrival_time'),    
    lang('group_name'),
    lang('description'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($types as $type )
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'shipping/shipping_type/drop_type',
            "{id: $type->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('shipping/shipping_type/add_edit', array($type->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('shipping/shipping_type/view', array($type->id)));
    }
     
    $data[] = array(
        $type->type_name,
        $type->code,
        $type->arrival_time,
        $type->group_name,
        $type->description,
        $type->created_date,
        $url,
    );
}
$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'type_name',
	),
     array(
        'type'      => 'dropdown',
        'field'     => 'code',
        'options'   => $shipping_types,
        'method'    => '=',
    ),
	array(
		'type'      => 'input',
		'field'     => 'arrival_time',
	),
	array(
		'type'      => 'input',
		'field'     => 'g.subarea_group_name',
	),
	array(
		'type'      => 'input',
		'field'     => 'description',
	),
);

$config = array(
	'filters'    => $filters,
);


if ($action == 'edit')
{
    $title = lang('shipping_type_management');
}
else
{
    $title = lang('shipping_type_view');
}
echo block_header($title);

echo $this->block->generate_pagination('shipping_type');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'shipping_type');

echo form_close();

echo $this->block->generate_pagination('shipping_type');
?>
