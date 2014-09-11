<?php

$head = array(
    array('text' => lang('name'), 'sort_key' => 'name', 'id' => 'purchase') ,
    array('text' => lang('boss'), 'sort_key' => 'boss'),
    array('text' => lang('address'), 'sort_key' => 'address'),
    array('text' => lang('phone'), 'sort_key' => 'phone'),
    array('text' => lang('contact_person'), 'sort_key' => 'contact_person'),
    lang('sku_count'),
    lang('order_statistics'),
    lang('options'),
);

$data = array();

foreach ($provider as $provider)
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'purchase/provider/drop_provider',
            "{id: $provider->p_id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('purchase/provider/edit', array($provider->p_id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('purchase/provider/view', array($provider->p_id)));
    }

    $into_view = anchor(site_url('purchase/order_statistic/order_statistic_show',array($provider->p_id)), lang('into_view'), array('title' => lang('into_view')));

    $sku_count = $this->purchase_model->fetch_provider_sku_count($provider->p_id);
    $data[] = array(
        $provider->p_name,
        $provider->p_boss,
        $provider->p_address,
        $provider->p_phone,
        $provider->p_contact_person,
        $sku_count,
        $into_view,
        $url,
    );
}
if($action == 'edit')
{
    $title = lang('provider_management');
}
else
{
    $title = lang('provider_view');
}
echo block_header($title);

$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'boss',
    ),
    array(
        'type'      => 'input',
        'field'     => 'address',
    ),
    array(
        'type'      => 'input',
        'field'     => 'phone',
    ),
    array(
        'type'      => 'input',
        'field'     => 'contact_person',
    ),
);

echo $this->block->generate_pagination('purchase');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'purchase');
echo $this->block->generate_pagination('purchase');
echo form_close();
?>
