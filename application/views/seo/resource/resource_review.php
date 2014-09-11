<?php
$head = array(
    array('text' => lang('resource_category'), 'sort_key' => 'seo_resource_category.name', 'id' => 'resource_integral'),
    array('text' => lang('resources'), 'sort_key' => 'url'),
    array('text' => lang('integral'), 'sort_key' => 'integral'),
    array('text' => lang('creator'), 'sort_key' => 'owner_id'),
    array('text' => lang('created_date'), 'sort_key' => 'update_date'),
    lang('options'),
);

$data = array();
$code_url = site_url('seo/resource_edit/verify_resource');
foreach($resources as $resource)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/resource/drop_resource_review',
        "{id: $resource->resource_id}",
        TRUE
    );
    $edit_button = $this->block->generate_edit_link(site_url('seo/resource/add_edit', array($resource->resource_id)));
    $url = $drop_button . $edit_button;
    $data[] = array(
        $resource->category_name,
        $resource->url,
        $resource->integral,
        fetch_user_name_by_id($resource->owner_id),
        $resource->update_date,
        $url,
    );
}
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'seo_resource_category.name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'url',
    ),
    array(
        'type'      => 'input',
        'field'     => 'integral',
    ),
    NULL,
    array(
        'type'      => 'input',
        'field'     => 'update_date',
    ),
);
$title = lang('resource_pending');
echo block_header($title);
echo form_open();
echo $this->block->generate_pagination('resource_integral');
$config = array(
    'filters'    => $filters,
);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'resource_integral');
echo $this->block->generate_pagination('resource_integral');
echo form_close();
?>

