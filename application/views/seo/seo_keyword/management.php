<?php

$head = array(
    array('text' => lang('service_company'), 'sort_key' => 'seo_service_company.name', 'id' => 'seo_keyword'),
    array('text' => lang('keyword'), 'sort_key' => 'keyword'),
    array('text' => lang('link_url'), 'sort_key' => 'link_url'),
    array('text' => lang('global_search_monthly'), 'sort_key' => 'global_search_monthly'),
    array('text' => lang('usa_search'), 'sort_key' => 'usa_search'),
    array('text' => lang('search_result'), 'sort_key' => 'search_result'),
    array('text' => lang('search_intitle'), 'sort_key' => 'search_intitle'),
    array('text' => lang('compete_index'), 'sort_key' => 'compete_index'),
    array('text' => lang('compete_price'), 'sort_key' => 'compete_price'),
    array('text' => lang('intitle'), 'sort_key' => 'intitle'),
    array('text' => lang('price_per_click'), 'sort_key' => 'price_per_click'),
    array('text' => lang('page_first_ten'), 'sort_key' => 'page_first_ten'),
    array('text' => lang('com_ranking'), 'sort_key' => 'com_ranking'),
    array('text' => lang('keyword_level'), 'sort_key' => 'level'),
    array('text' => lang('creator'), 'sort_key' => 'name'),
    array('text' => lang('created_date'), 'sort_key' => 'created_date'),
    lang('options'),
);

$data = array();

foreach ($keywords as $keyword) {
    if ($action == 'edit') {
        $drop_button = $this->block->generate_drop_icon(
                        'seo/seo_keyword/drop_keyword',
                        "{id: $keyword->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('seo/seo_keyword/add_edit', array($keyword->id)));
        $url = $drop_button . $edit_button;
    } else {
        $url = $this->block->generate_view_link(site_url('seo/seo_keyword/view', array($keyword->id)));
    }
    if('1' == $keyword->level)
    {
        $keyword_level = lang('height');
    }
    else if('2' == $keyword->level)
    {
        $keyword_level = lang('medium');
    }
    else if('3' == $keyword->level)
    {
        $keyword_level = lang('general');
    }
    else
    {
        $keyword_level = '';
    }
    $data[] = array(
        $keyword->name,
        $keyword->keyword,
        "<a href='$keyword->link_url' target='_blank'>". $keyword->link_url . "</a>",
        $keyword->global_search_monthly,
        $keyword->usa_search,
        $keyword->search_result,
        $keyword->search_intitle,
        $keyword->compete_index,
        $keyword->compete_price,
        $keyword->intitle,
        $keyword->price_per_click,
        $keyword->page_first_ten,
        $keyword->com_ranking,
        $keyword_level,
        $keyword->name,
        $keyword->created_date,
        $url,
    );
}

$level_options = array(
            ''      => lang('all'),
            '1'     => lang('height'),
            '2'     => lang('medium'),
            '3'     => lang('general'),
);
$company_options = array();
$companys = $this->seo_service_company_model->fetch_all_service_companys();
$company_options[''] = lang('all');
foreach($companys as $company)
{
    $company_options[$company->name] = $company->name;
}
$filters = array(
    array(
                'type'      => 'dropdown',
                'field'     => 'seo_service_company.name',
                'options'   => $company_options,
                'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'keyword',
    ),
    array(
        'type' => 'input',
        'field' => 'link_url',
    ),
    array(
        'type' => 'input',
        'field' => 'global_search_monthly',
    ),
    array(
        'type' => 'input',
        'field' => 'usa_search',
    ),
    array(
        'type' => 'input',
        'field' => 'search_result',
    ),
    array(
        'type' => 'input',
        'field' => 'search_intitle',
    ),
    array(
        'type' => 'input',
        'field' => 'compete_index',
    ),
    array(
        'type' => 'input',
        'field' => 'compete_price',
    ),
    array(
        'type' => 'input',
        'field' => 'intitle',
    ),
    array(
        'type' => 'input',
        'field' => 'price_per_click',
    ),
    array(
        'type' => 'input',
        'field' => 'page_first_ten',
    ),
    array(
        'type' => 'input',
        'field' => 'com_ranking',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'seo_keyword.level',
        'options'   => $level_options,
        'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'name',
    ),
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);

echo $this->block->generate_pagination('seo_keyword');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'seo_keyword');

echo form_close();

echo $this->block->generate_pagination('seo_keyword');
?>
