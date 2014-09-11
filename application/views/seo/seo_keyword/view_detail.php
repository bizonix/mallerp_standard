<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('keyword')),
    $keyword->keyword ,
);

$data[] = array(
    $this->block->generate_required_mark(lang('link_url')),
    $keyword->link_url,
);

$data[] = array(
    lang('global_search_monthly'),
    $keyword->global_search_monthly,
);

$data[] = array(
    lang('usa_search'),
    $keyword->usa_search,
);

$data[] = array(
    lang('search_result'),
    $keyword->search_result,
);

$data[] = array(
    lang('search_intitle'),
    $keyword->search_intitle,
);

$data[] = array(
    lang('compete_price'),
    $keyword->compete_price,
);

$data[] = array(
    lang('compete_index'),
    $keyword->compete_index,
);

$data[] = array(
    lang('intitle'),
    $keyword->intitle,
);

$data[] = array(
    lang('price_per_click'),
    $keyword->price_per_click,
);

$data[] = array(
    lang('page_first_ten'),
    $keyword->page_first_ten,
);

$data[] = array(
    lang('com_ranking'),
    $keyword->com_ranking,
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
    $this->block->generate_required_mark(lang('note')),
    form_textarea($config),
);

echo $this->block->generate_tinymce(array('note'), TRUE);

$permission = $this->block->generate_permissions($seo_users, $permissions);

$data[] = array(
    $this->block->generate_required_mark('resource permission'),
     block_check_group('permissions[]', $permission),
);

$title = lang('seo_keyword_view_list');

$back_button = $this->block->generate_back_icon(site_url('seo/seo_keyword/view_list'));

$title .= $back_button;

echo block_header($title);

echo $this->block->generate_table($head, $data);

echo '<h2>' . $back_button . '</h2>' ;

?>