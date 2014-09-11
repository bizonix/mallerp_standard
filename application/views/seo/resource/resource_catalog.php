<?php
$url = site_url('seo/resource/add_resource_category');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('resource_catalog'),
    lang('integral'),
    lang('release_limit'),
    lang('release_limit_wholelife'),
    lang('creator'),
    lang('created_date'),    
);
if('edit' == $action)
{
    $head[] = lang('options') . $add_button;
}
$data = array();
$code_url = site_url('seo/resource/verify_resource_category');
foreach ($resource_categories as $category)
{
    $drop_button = $this->block->generate_drop_icon(
        'seo/resource/drop_resource_category',
        "{id: $category->id}",
        TRUE
    );
    $item = array(
        $this->block->generate_div("name_{$category->id}", isset($category) ?  $category->name : '[edit]'),
        $this->block->generate_div("integral_{$category->id}", isset($category) ?  $category->integral : '[0]'),
        $this->block->generate_div("release_limit_{$category->id}", isset($category) ?  $category->release_limit : '[0]'),
        $this->block->generate_div("release_limit_wholelife_{$category->id}", isset($category) ?  $category->release_limit_wholelife : '[0]'),
        $category->creator,
        $category->created_date,       
    );
   if('edit' == $action)
   {
       $item[] =  $drop_button;
       echo $this->block->generate_editor(
            "name_{$category->id}",
            'resource_category_form',
            $code_url,
            "{id: $category->id, type: 'name'}"
        );
            
       echo $this->block->generate_editor(
            "integral_{$category->id}",
            'resource_category_form',
            $code_url,
            "{id: $category->id, type: 'integral'}"
        );

        echo block_editor(
            "release_limit_{$category->id}",
            'resource_category_form',
            $code_url,
            "{id: $category->id, type: 'release_limit'}"
        );

        echo block_editor(
            "release_limit_wholelife_{$category->id}",
            'resource_category_form',
            $code_url,
            "{id: $category->id, type: 'release_limit_wholelife'}"
        );
   }
   $data[] = $item;   
}
if('edit' == $action)
{
    $title = lang('resource_category_manage');
}
else
{
    $title = lang('resource_category_view');
}
    
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
