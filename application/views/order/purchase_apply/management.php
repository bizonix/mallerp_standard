<?php

$head = array(
    lang('product_name'),
    lang('product_image_url'),
    lang('apply_status'),
    lang('name'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($applys as $apply)
{
    if ($apply->sm_status_name == 'in_proccess')
    {
        $edit_button = $this->block->generate_edit_link(site_url('order/purchase_apply/edit', array($apply->id)));
        $url = $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('order/purchase_apply/view', array($apply->id)));
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
        $apply->product_name,
        $this->block->generate_image($apply->product_image_url),
        $apply_status,
        $apply->u_name,
        $apply->created_date,
        $url,
    );
}

if ($action == 'edit')
{
    $title = lang('purchase_apply_management');
}
else
{
    $title = lang('purchase_apply_view');
}

echo block_header($title);

$filters = array(
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
