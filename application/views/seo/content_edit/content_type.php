<?php
$url = site_url('seo/content_edit/add_content_type');
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
$code_url = site_url('seo/content_edit/verify_content_type');
foreach ($content_types as $content_type)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/content_edit/drop_content_type',
        "{id: $content_type->id}",
        TRUE
    );
    $item = array(
        $this->block->generate_div("name_{$content_type->id}", isset($content_type) ?  $content_type->name : '[edit]'),
        $this->block->generate_div("integral_{$content_type->id}", isset($content_type) ?  $content_type->integral : '[0]'),
        $content_type->creator,
        $content_type->created_date,
    );
    if($action == 'edit')
    {
        $item[] = $drop_button;
        echo $this->block->generate_editor(
            "name_{$content_type->id}",
            'content_type_form',
            $code_url,
            "{id: $content_type->id, type: 'name'}"
        );
        echo $this->block->generate_editor(
            "integral_{$content_type->id}",
            'content_type_form',
            $code_url,
            "{id: $content_type->id, type: 'integral'}"
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
