<?php
$head = array(
    lang('name'),
    lang('telephone'),
    lang('contact_person'),
    lang('remark'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($companys as $company )
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'shipping/shipping_company/drop_company',
            "{id: $company->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('shipping/shipping_company/add_edit', array($company->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('shipping/shipping_company/view', array($company->id)));
    }
    $data[] = array(
        $company->name,
        $company->telephone,
        $company->contact_person,
        $company->remark,
        $company->created_date,
        $url,
    );
}

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'name',
	),
	array(
		'type'      => 'input',
		'field'     => 'telephone',
	),
	array(
		'type'      => 'input',
		'field'     => 'contact_person',
	),
	array(
		'type'      => 'input',
		'field'     => 'remark',
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
    $title = lang('shipping_company_management');
}
else
{
    $title = lang('shipping_company_view');
}
echo block_header($title);

echo $this->block->generate_pagination('shipping_company');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'shipping_company');

echo form_close();

echo $this->block->generate_pagination('shipping_company');

?>
