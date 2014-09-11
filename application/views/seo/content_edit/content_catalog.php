<?php
$url = site_url('seo/content_edit/add_content_catalog');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('content_catalog'),
    lang('integral'),
    lang('creator'),
    lang('created_date'),  
);
if($action == 'edit')
{
    $head[] = lang('options') . $add_button;
}
$data = array();
$code_url = site_url('seo/content_edit/verify_content_catalog');
foreach ($content_catalogs as $catalog)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/content_edit/drop_content_catalog',
        "{id: $catalog->id}",
        TRUE
    );
    $item = array(
        $this->block->generate_div("name_{$catalog->id}", isset($catalog) ?  $catalog->name : '[edit]'),
        $this->block->generate_div("integral_{$catalog->id}", isset($catalog) ?  $catalog->integral : '[0]'),
        $catalog->creator,
        $catalog->created_date,      
    );
    if($action == 'edit')
    {
        $item[] = $drop_button;
        echo $this->block->generate_editor(
            "name_{$catalog->id}",
            'content_catalog_form',
            $code_url,
            "{id: $catalog->id, type: 'name'}"
        );
        echo $this->block->generate_editor(
            "integral_{$catalog->id}",
            'content_catalog_form',
            $code_url,
            "{id: $catalog->id, type: 'integral'}"
        );

    }
    $data[] = $item;
}
if($action == 'edit' )
{
    $title = lang('content_catalog_manage');
}
else
{
    $title = lang('content_catalog_view');
}
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
