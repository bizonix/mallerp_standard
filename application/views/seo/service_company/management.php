<?php
$url = site_url('seo/service_company/add');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('service_company'),
    lang('website'),
    lang('description'), 
    lang('created_date'),
    lang('creator'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('seo/service_company/verify');
foreach ($service_companys as $service_company)
{

    $drop_button = $this->block->generate_drop_icon(
        'seo/service_company/drop',
        "{id: $service_company->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("name_{$service_company->id}", isset($service_company) ?  $service_company->name : '[edit]'),
        $this->block->generate_div("website_{$service_company->id}", isset($service_company) ?  $service_company->website : '[edit]'),
        $this->block->generate_div("description_{$service_company->id}", isset($service_company) ?  $service_company->description : '[edit]'),
        $service_company->created_date,
        fetch_user_name_by_id($service_company->creator_id),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "name_{$service_company->id}",
        'service_company_form',
        $code_url,
        "{id: $service_company->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "website_{$service_company->id}",
        'service_company_form',
        $code_url,
        "{id: $service_company->id, type: 'website'}"
    );
    echo $this->block->generate_editor(
        "description_{$service_company->id}",
        'service_company_form',
        $code_url,
        "{id: $service_company->id, type: 'description'}"
    );       
}
$title = lang('service_company_manage');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
