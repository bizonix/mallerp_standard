<?php
$CI = & get_instance();
$head = array(
    lang('level'),
    array('text' => lang('title'), 'sort_key' => 'title', 'id' => 'document_content'),
    lang('type'),
    array('text' => lang('author'), 'sort_key' => 'u_name'),
    array('text' => lang('custom_date'), 'sort_key' => 'custom_date'),
    lang('options'),
);
$data = array();
foreach ($contents as $content) {

    if ($action == 'edit') {

        $drop_button = $this->block->generate_drop_icon(
                        'edu/content/drop_content',
                        "{id: $content->dc_id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('edu/content/edit', array($content->dc_id)), TRUE);
        $view_button = $this->block->generate_view_link(
                        site_url('edu/content/view', array($content->dc_id)),
                        array(),
                        FALSE,
                        'main-content-detail',
                        'main-content'
        );
        if ($CI->is_super_user()) {
            $url = $drop_button . $edit_button . $view_button;
        } else {
            $dc_owner_id = $content->dc_owner_id;
            $user_id = $CI->get_current_user_id();
            if ($dc_owner_id == $user_id) {
                $url = $drop_button . $edit_button . $view_button;
            } else {
                $url = $view_button;
            }
        }
    } else {
        $url = $this->block->generate_view_link(
                        site_url('edu/content/view', array($content->dc_id)),
                        array(),
                        FALSE,
                        'main-content-detail',
                        'main-content'
        );
    }
    $image_url = base_url()."static/images/icons/flag/level-$content->level.gif";
    
    $title = '';
    if($content->level == 1)
    {
        $title = lang('normal');
    }
    else if ($content->level == 2)
    {
        $title = lang('important');
    }
    else if ($content->level == 3)
    {
        $title = lang('very_important');
    }
    $view_link = site_url('edu/content/view', array($content->dc_id));
    $data[] = array(
        "<image height=20 width=20 title='$title' src='$image_url' />",
        generate_ajax_view_link($view_link, $content->dc_title),
        $content->dcata_path,
        $content->u_name,
        $content->dc_custom_date ? $content->dc_custom_date : $content->dc_edited_date,
        $url,
    );
}
$title = lang('document_content_management');

$name = '';
if (isset($cat_name->name)) {
    $name = ' / ' . $cat_name->name;
}
echo block_header($title . $name);

$options = array(
    '1'  =>  lang('normal'),
    '2'  =>  lang('important'),
    '3'  =>  lang('very_important'),
);
$filters = array(
    array(
        'type' => 'dropdown',
        'field' => 'dc.level',
        'options' => $options,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'title',
    ),
    NULL
    ,
    array(
        'type' => 'input',
        'field' => 'u.name',
    ),
    array(
        'type' => 'input',
        'field' => 'dc.edited_date|dc.custom_date',
    ),
);

echo $this->block->generate_pagination('document_content', array(), 'main-content');

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config, 'main-content');
echo $this->block->generate_table($head, $data, $filters, 'document_content');
echo form_close();

echo $this->block->generate_pagination('document_content', array(), 'main-content');
?>
