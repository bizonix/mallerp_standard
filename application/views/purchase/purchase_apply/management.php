<?php

$head = array(
	lang('sort_num'),
    lang('product_name'),
    lang('product_image_url'),
    lang('apply_status'),
    lang('applyer'),
    lang('developer'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($applys as $apply)
{
    $drop_button = $this->block->generate_drop_icon(
        'purchase/purchase_apply/delete_apply',
        "{id: $apply->id}",
        TRUE
    );

    if ($apply->sm_status_name != 'in_proccess')
    {
        $url = $this->block->generate_view_link(site_url('purchase/purchase_apply/view', array($apply->id)));
    }
    else
    {
        $url = $this->block->generate_edit_link(site_url('purchase/purchase_apply/edit', array($apply->id)));
    }

    if($apply->sm_status_name == 'in_proccess')
    {
        $apply_status = lang('in_proccess');
    }
    elseif($apply->sm_status_name == 'approved')
    {
        $apply_status = lang('approved');
    }
    elseif($apply->sm_status_name == 'approved_and_edited')
    {
        $apply_status = lang('approved_and_edited');
    }
    else
    {
        $apply_status = lang('rejected');
    }

    $data[] = array(
		$apply->id,
        $apply->product_name,
        $this->block->generate_image($apply->product_image_url),
        $apply_status,
        $apply->u_name,
        $apply->ub_name,
        $apply->created_date,
        $drop_button . $url,
    );
}

$title = lang('purchase_apply_management');

echo block_header($title);

$filters = array(
	array(
		'type'      => 'input',
        'field'     => 'ppa.id',
        'method'    => 'from_to'
	),
    array(
        'type'      => 'input',
        'field'     => 'product_name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'product_image_url',
    ),
    $this->block->generate_search_dropdown('apply_status', 'purchase_apply_status'),
    array(
        'type'      => 'input',
        'field'     => 'u.name',
    ),
);

echo $this->block->generate_pagination('purchase_apply');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'purchase_apply');
echo form_close();

echo $this->block->generate_pagination('purchase_apply');
?>
