<?php
$head = array(
    lang('name'),
    lang('value'),
);

$company_permission = '';
$company_permissions_array = array();
if($company_permissions)
{
    foreach ($company_permissions as $cp)
    {
        $company_permissions_array[] = $cp->company_id;
    }
}
foreach ($keyword_companys as $keyword_company)
{
    $config = array(
        'name'        => 'company_permissions[]',
        'value'       => $keyword_company->id,
//        'checked'     => in_array($keyword_company->id, $company_permissions_array) ? TRUE : FALSE,
        'checked'     => empty ($company_permissions_array) && $keyword_company->name =='mallerp' ? TRUE : (in_array($keyword_company->id, $company_permissions_array) ? TRUE : FALSE),
        'style'       => 'margin:10px',
    );
    $company_permission  .= form_checkbox($config) . form_label($keyword_company->name);
}
$data[] = array(
    $this->block->generate_required_mark('service company'),
     block_check_group('company_permissions[]', $company_permission),
);

$config = array(
      'name'        => 'keyword',
      'id'          => 'keyword',
      'value'       => $keyword ? $keyword->keyword : '',
      'maxlength'   => '200',
      'size'        => '120',
);
$data[] = array(
    $this->block->generate_required_mark(lang('keyword')),
    form_input($config),
);

$config = array(
      'name'        => 'link_url',
      'id'          => 'link_url',
      'value'       => $keyword ? $keyword->link_url : '',
      'maxlength'   => '200',
      'size'        => '120',
);
$data[] = array(
    $this->block->generate_required_mark(lang('link_url')),
    form_input($config),
);

$config = array(
      'name'        => 'global_search_monthly',
      'id'          => 'global_search_monthly',
      'value'       => $keyword ? $keyword->global_search_monthly : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    lang('global_search_monthly'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'usa_search',
      'id'          => 'usa_search',
      'value'       => $keyword ? $keyword->usa_search : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    lang('usa_search'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'search_result',
      'id'          => 'search_result',
      'value'       => $keyword ? $keyword->search_result : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    lang('search_result'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'search_intitle',
      'id'          => 'search_intitle',
      'value'       => $keyword ? $keyword->search_intitle : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    lang('search_intitle'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'compete_price',
      'id'          => 'compete_price',
      'value'       => $keyword ? $keyword->compete_price : '',
      'maxlength'   => '20',
      'size'        => '10',
);
$data[] = array(
    lang('compete_price'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'compete_index',
      'id'          => 'compete_index',
      'value'       => $keyword ? $keyword->compete_index : '',
      'maxlength'   => '20',
      'size'        => '10',
);
$data[] = array(
    lang('compete_index'),
    form_input($config).lang('one_to_ten').','.lang('ten_is_the_most_competitive').','.lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'intitle',
      'id'          => 'intitle',
      'value'       => $keyword ? $keyword->intitle : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    lang('intitle'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'price_per_click',
      'id'          => 'price_per_click',
      'value'       => $keyword ? $keyword->price_per_click : '',
      'maxlength'   => '20',
      'size'        => '10',
);
$data[] = array(
    lang('price_per_click'),
    form_input($config).lang('google_api_gets_data_automatically'),
);
$config = array(
      'name'        => 'page_first_ten',
      'id'          => 'page_first_ten',
      'value'       => $keyword ? $keyword->page_first_ten : '',
      'maxlength'   => '20',
      'size'        => '10',
);
$data[] = array(
    lang('page_first_ten'),
    form_input($config).lang('google_api_gets_data_automatically'),
);

$config = array(
      'name'        => 'com_ranking',
      'id'          => 'com_ranking',
      'value'       => $keyword ? $keyword->com_ranking : '',
      'maxlength'   => '20',
      'size'        => '10',
);
$data[] = array(
    lang('com_ranking'),
    form_input($config).lang('google_api_gets_data_automatically'),
);
$level_options = array(
            '1'     => lang('height'),
            '2'     => lang('medium'),
            '3'     => lang('general'),
);
$data[] = array(
    lang('keyword_level'),
    form_dropdown('level', $level_options, $keyword ? $keyword->level : '3'),
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
        $catalog_ids ? object_to_array($catalog_ids, 'catalog_id') : array_keys($options),
        "id='catalogs' size=" . "'$cat_count'"
    ),
);

$config = array(
    'name'        => 'note',
    'id'          => 'note',
    'value'       => $keyword ? $keyword->note : '',
    'style'       => 'width:98%',
    'rows'        => '5',
    'cols'        => '5',
);
$data[] = array(
    lang('note'),
    form_textarea($config),
);

echo $this->block->generate_tinymce(array('note'), TRUE);

$permission = $this->block->generate_permissions($seo_users, $permissions);
$data[] = array(
    $this->block->generate_required_mark('resource permission'),
     block_check_group('permissions[]', $permission),
);

if ($keyword)
{
    $title = lang('edit_keyword');
}
else
{
    $title = lang('add_keyword');
}
$back_button = $this->block->generate_back_icon(site_url('seo/seo_keyword/manage'));

$title .= $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'keyword_form',
);


$url = site_url('seo/seo_keyword/save');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('save_keyword'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_content(this, '$url');",
);

echo form_hidden('keyword_id', $keyword ? $keyword->id : '-1');
echo '<h2>'.block_button($config).$back_button.'</h2>';
echo form_close();

?>