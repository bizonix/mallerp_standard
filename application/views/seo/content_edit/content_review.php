<?php
$head = array(
    array('text' => lang('content_catalog'), 'sort_key' => 'seo_content_catalog.name', 'id' => 'content_integral'),
    array('text' => lang('title'), 'sort_key' => 'title'),
    array('text' => lang('integral'), 'sort_key' => 'integral'),
    array('text' => lang('creator'), 'sort_key' => 'owner_id'),
    array('text' => lang('created_date'), 'sort_key' => 'update_date'),
    lang('options'),
);

$data = array();
$code_url = site_url('seo/content_edit/verify_content');
foreach($contents as $content)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/content_edit/drop_content_review',
        "{id: $content->content_id}",
        TRUE
    );
    $edit_button = $this->block->generate_edit_link(site_url('seo/content_edit/add_edit', array($content->content_id)));
    $url = $drop_button . $edit_button;
    $data[] = array(
        $content->catalog_name,
        $content->title,
        $content->integral,
        fetch_user_name_by_id($content->owner_id),
        $content->update_date,
        $url,
    );  
}
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'seo_content_catalog.name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'title',
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

$title = lang('content_pending');
echo block_header($title);
echo form_open();
echo $this->block->generate_pagination('content_integral');
$config = array(
    'filters'    => $filters,
);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'content_integral');
echo $this->block->generate_pagination('content_integral');
echo form_close();
?>
