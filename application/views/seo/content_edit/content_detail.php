<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    'Creator',
    $content->user_name,
);

$data[] = array(
    'Title',
    $content->title,
);

$data[] = array(
    'Type',
    $content->type_name,
);

foreach ($catalogs as $catalog)
{
    $options[$catalog->id] = $catalog->name;
}
$cat_count = count($options);
$data[] = array(
    $this->block->generate_required_mark('Category'),
    form_dropdown(
        'catalogs[]',
        $options,
        $catalog_ids ? object_to_array($catalog_ids, 'catalog_id') : array_keys(($options)),
        "id='catalogs' size=" . "'$cat_count'"
    ),
);

$config = array(
    'name'        => 'content_area',
    'id'          => 'content_area',
    'rows'        => '30',
    'value'       => $content ? $content->content : '',
    'cols'        => '80',
    'style'       => 'width:98%',
);

$data[] = array(
    $this->block->generate_required_mark('Content'),
    form_textarea($config),
);

echo $this->block->generate_tinymce(array('content_area'), TRUE);

$permission = '';
$permission_array = array();
if ($permissions)
{
    foreach ($permissions as $p)
    {
        $permission_array[] = $p->user_id;
    }
}
foreach ($seo_users as $seo_user)
{
    $config = array(
        'name'        => 'permissions[]',
        'value'       => $seo_user->u_id,
        'checked'     => in_array($seo_user->u_id, $permission_array) ? TRUE : FALSE,
        'style'       => 'margin:10px',
        'disabled'    => TRUE,
    );
    $permission .= form_checkbox($config) . form_label($seo_user->u_name);
}
$data[] = array(
    $this->block->generate_required_mark('resource permission'),
    $permission,
);

$back_button = $this->block->generate_back_icon(site_url('seo/release/manage'));
$title = lang('content_detail'). $back_button;

echo block_header($title);

echo $this->block->generate_table($head, $data, array(), NULL, 'width: 40%; float:left');

$head = array(
    lang('username'),
    lang('password'),
    'URL',
    lang('category'),
    'language',
    'Verification URL',
    lang('options'),
);
$data = array();
foreach ($resources as $resource)
{
    if (isset($url_count[$resource->url]) && $url_count[$resource->url] > 3)
    {
        continue;
    }
    
    $submit_url = site_url('seo/release/save_verifying');
    $url = anchor($resource->url, 'Resource URL', array('target' =>  '_blank', 'title' => $resource->url));
    $config_drop = array(
        'name'        => 'drop',
        'value'       => lang('close'),
        'type'        => 'button',
        'style'       => 'margin:10px;padding:5px;',
        'onclick'     => "this.blur();submit_release('$submit_url', $resource->id, $content->id,'drop', this);return true;",
    );
    $drop_button = form_input($config_drop);

    $config_save = array(
        'name'        => 'save',
        'value'       => lang('release'),
        'type'        => 'button',
        'style'       => 'margin:10px;padding:5px;',
        'onclick'     => "this.blur();submit_release('$submit_url', $resource->id, $content->id,'save', this);return true;",
    );
    $save_button = block_button($config_save);

    $config_input = array(
        'name'        => 'validate_url_' . $resource->id,
        'id'          => 'validate_url_' . $resource->id,
        'value'       => $resource ? $resource->validate_url : '',
        'maxlength'   => '200',
        'size'        => '20',
    );

    $data[] = array(
        $resource->username,
        $resource->password,        
        $url,
        $resource->cat_name,
        $resource->language,
        form_input($config_input).$save_button,
        $drop_button,
    );
}
$attributes = array(
    'id' => 'release_form',
);
$save_url = site_url('seo/release/save_verifying');
echo form_open($save_url, $attributes);
$options = array();
$companys = $this->seo_service_company_model->fetch_all_service_companys();
$options[-1] = lang('all');
foreach($companys as $company)
{
    $options[$company->id] = $company->name;
}

$url = site_url_no_key('seo/content_edit/content_detail');
$url_key = site_key('seo/content_edit/content_detail');
$js = "onChange='filter_company_resource(\"$url\", \"$url_key\", $content->id, this.value);'";
echo '<div style= "float:right">'. $select_purchaser = form_dropdown('select_purchaser', $options, $company_id, $js) . '</div>';
$sortable = array(
    'default',
    'default',
    'default',
    'default',
    'default',
    'default',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 60%; float:left;border-collapse: collapse;");
echo '<div style="clear: both;"></div>';
echo '<h2>'.$back_button.'</h2>';
echo form_close();
?>


