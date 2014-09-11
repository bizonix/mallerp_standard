<?php
$head = array(
    array('text' => lang('service_company'), 'sort_key' => 'name', 'id' => 'seo_content'),
    array('text' => 'type', 'sort_key' => 'seo_content_type.name'),
    array('text' => 'title', 'sort_key' => 'seo_content.title'),
    lang('outgoing_resource_number'),
    array('text' => lang('created_date'), 'sort_key' => 'seo_content.update_date'),
    lang('options'),
);

$data = array();

foreach ($contents as $content)
{
    $names = $this->seo_model->get_company_name($content->id);
//    var_dump($names);
    $name_str = '';
    foreach ($names as $name)
    {
        $name_str .= $name->name . ',';
    }

    $release_url = site_url('seo/release/manage', array($content->id));
    $attributes = array(
        'title'     => 'view detail',
        'onclick'   => "return helper.modal(this, 'content detail')",
    );
    $view_url = $this->block->generate_view_link(site_url('seo/content_edit/view', array('popup', $content->id)), $attributes);
    $options = $view_url;
    $content_detail_url =  site_url('seo/content_edit/content_detail',array($content->id, -1));
    $link = anchor($content_detail_url, $content->title);
    $data[] = array(
        trim($name_str, ","),
        $content->type_name,
        $link,
        fetch_content_resources_count($content->id),
        $content->update_date,
        $options,
    );
}

$attributes = array(
    'id' => 'release_form',
);
$categorys = $this->seo_model->fetch_all_content_type();

$options = array(''=>lang('all'));
foreach ($categorys as $category)
{
    $options[$category->id] = $category->name;
}
$save_url = site_url('seo/release/save_verifying');

$company_options = array();
$companys = $this->seo_service_company_model->fetch_all_service_companys();
$company_options[''] = lang('all');
foreach($companys as $company)
{
    $company_options[$company->id] = $company->name;
}
$filters = array(
    array(
                'type'      => 'dropdown',
                'field'     => 'cm.company_id',
                'options'   => $company_options,
                'method'    => '=',
    ),
    array(
                'type'      => 'dropdown',
                'field'     => 'seo_content_type.id',
                'options'   => $options,
                'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'seo_content.title',
        'size' => 6,
    ),
    NULL,
    array(
        'type'      => 'date',
        'field'     => 'seo_content.update_date',
        'method'    => 'from_to'
    ),
);
$config = array(
    'filters' => $filters,
);

echo block_header(lang('release_management'));

echo form_open($save_url, $attributes);

echo $this->block->generate_pagination('seo_content');
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'seo_content');
echo $this->block->generate_pagination('seo_content');
echo form_close();

?>