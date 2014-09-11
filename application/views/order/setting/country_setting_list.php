<?php
$url = site_url('order/country_list/add_country_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    array('text'=>lang('code'), 'sort_key' => 'code', 'id' => 'country_code'),
    array('text'=>lang('country_name_en') ,'sort_key' => 'name_en'),
    lang('country_name_cn'),
    array('text'=>lang('continent') ,'sort_key' => 'continent'),
    lang('post_p').lang('order_check_address'),
    lang('post_p').lang('taobao_deliver_code'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('order/country_list/verigy_country_code');

foreach ($applys as $apply)
{
    $drop_button = $this->block->generate_drop_icon(
        'order/country_list/drop_country_code',
        "{id: $apply->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("code_{$apply->id}", empty($apply->code) ?    '[edit]' : $apply->code),
        $this->block->generate_div("name_en_{$apply->id}", empty($apply->name_en) ?  '[edit]': $apply->name_en),
        $this->block->generate_div("name_cn_{$apply->id}", empty($apply->name_cn) ?    '[edit]' : $apply->name_cn),
        $this->block->generate_div("continent_id_{$apply->id}",empty($apply->continent_id) ?    '[edit]' : $apply->continent_name),
        $this->block->generate_div("regular_check_url_{$apply->id}",empty($apply->regular_check_url) ?    '[edit]' : $apply->regular_check_url),
        $this->block->generate_div("regular_carrier_{$apply->id}",empty($apply->regular_carrier) ?    '[edit]' : $apply->regular_carrier),
        $drop_button,    
    );

        
    $option_continent = array();
    foreach ($continents as $continent)
    {
        $option_continent["$continent->id"] = $continent->name_cn;
    }
    $option_continent = to_js_array($option_continent);
        
    echo $this->block->generate_editor(
        "regular_check_url_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'regular_check_url'}"
//        $option_continent
    );
    echo $this->block->generate_editor(
        "regular_carrier_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'regular_carrier'}"
//        $option_continent
    );
    echo $this->block->generate_editor(
        "continent_id_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'continent_id'}",
        $option_continent
    );
    echo $this->block->generate_editor(
        "code_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'code'}"
    );
    echo $this->block->generate_editor(
        "name_en_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'name_en'}"
    );
    echo $this->block->generate_editor(
        "name_cn_{$apply->id}",
        'country_code_form',
        $code_url,
        "{id: $apply->id, type: 'name_cn'}"
    );
}

$title = lang('country_setting_list');
echo block_header($title);

$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'code',
    ),
    array(
        'type'      => 'input',
        'field'     => 'cc.name_en',
    ),
     array(
        'type'      => 'input',
        'field'     => 'cc.name_cn',
    ),
);

echo $this->block->generate_pagination('country_code');

$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'country_code');
echo form_close();
echo $this->block->generate_pagination('country_code');
?>
