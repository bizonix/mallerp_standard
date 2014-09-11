<?php
$head = array(
    lang('content_catalog'),
    lang('resource_category'),  
);

$data = array();
$code_url = site_url('seo/content_edit/verify_content_type');
$url = site_url('seo/release/resource_categoried_search');
$integral_url = site_url('seo/release/verify_content_resource_catalog_integral');
foreach ($content_catalogs as $catalog)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/content_edit/drop_content_catalog',
        "{id: $catalog->id}",
        TRUE
    );

    $resource_categories_html = '<div>';
    foreach($resource_categories as $resource_category)
    {
        $content_resource = $this->seo_model->fetch_content_resource_category($catalog->id,$resource_category->id);
        $attributes = array(
            'name'        => 'resource_category_'.$catalog->id.'_'.$resource_category->id,
            'id'          => 'resource_category_'.$catalog->id.'_'.$resource_category->id,
            'value'       => $resource_category->id,
            'checked'     => isset($content_resource->resource_category_id) ? TRUE : FALSE,
            'style'       => 'margin:10px',
            'onclick'     => "helper.ajax('$url',{content_catalog_id:$catalog->id, resource_category_id:$resource_category->id, checked:this.checked}, 1);",
        );
        $integral_str = '';
        if(isset($content_resource->resource_category_id))
        {
            $integral_str .= repeater('&nbsp;', 10) . lang('integral') . ': ';
            $integral_str .= "<span id = 'integral_{$content_resource->id}'>" . (isset($content_resource->integral) ?  $content_resource->integral : '1') . "</span>";           
            echo $this->block->generate_editor(
                "integral_{$content_resource->id}",
                'integral_form',
                $integral_url,
                "{id: $content_resource->id, type: 'integral'}"
            );
        }
        
        $resource_categories_html .= '<div style="margin: 5px;">';
        $resource_categories_html .= form_checkbox($attributes) . $resource_category->name  .  $integral_str;
        $resource_categories_html .= '</div>';
    }
    
    $resource_categories_html .= '</div>';

    $data[] = array(
        $this->block->generate_div("name_{$catalog->id}", isset($catalog) ?  $catalog->name : '[edit]'),
        $resource_categories_html,   
    );
    echo $this->block->generate_editor(
        "name_{$catalog->id}",
        'content_catalog_form',
        $code_url,
        "{id: $catalog->id, type: 'name'}"
    );
  
}
$title = lang('content_resource_map');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
