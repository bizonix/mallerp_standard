<?php
$CI = &get_instance();
$head = array(
    array('text' =>lang('sku'), 'sort_key' => 'sku', 'id' => 'differences_review' ),
    lang('product_image_url'),
    array('text' =>lang('stock_checker'), 'sort_key' => 'stock_checker'),
    array('text' => lang('person_responsible'), 'sort_key' => 'duty'),
    array('text' => lang('review_state'), 'sort_key' => 'review_status'),
    array('text' => lang('before_change_count'), 'sort_key' => 'before_change_count'),
    array('text' => lang('change_count'), 'sort_key' => 'change_count'),
    array('text' => lang('after_change_count'), 'sort_key' => 'after_change_count'),
    array('text' => lang('differences_remark'), 'sort_key' => 'differences_remark'),
    array('text' => lang('remark'), 'sort_key' => 'remark'),
    array('text' => lang('created_date'), 'sort_key' => 'update_time'),
);
$data = array();
$options = array(
    ''                       => lang('please_select'),
    'correct'                => lang('correct'),
    'actual_stock_more'      => lang('actual_stock_more'),
    'actual_low_stock'       => lang('actual_low_stock'),
    'instock_error'          => lang('instock_error'),
    'shelf_code_error'       => lang('shelf_code_error'),
);

$confirmed = array(
    '0' =>              lang('not_review'),
    '1'                     => lang('director_review'),
//    'selected'      => lang('not_review'),
);

$confirm_review_url = site_url('stock/stock_check/confirm_review');

foreach ($records as $record) {
    $config = array(
        'name' => 'type_extra_' . $record->id,
        'id' => 'type_extra_' . $record->id,
        'value' => $record->remark,
        'rows' => '2',
        'cols' => '14',
    );
    $button_config = array(
            'name' => 'confirm_' . $record->id,
            'id' => 'confirm_' . $record->id,
            'value' => lang('confirm_review'),
            'onclick' => "confirm_review('$confirm_review_url', $record->id);",
        );
    $stock_checker = $this->user_model->get_user_name_by_id($record->stock_checker);
    $product_image_url = $this->stock_model->get_one('product_basic', 'image_url', array('sku' => $record->sku));
    if($record->duty > 0)
    {
        $duty = $this->user_model->get_user_name_by_id($record->duty);
    } else {
        $duty = '#';
    }
    $row = array();
    $row[] = $record->sku;
    $row[] = $this->block->generate_image($product_image_url);
    $row[] = $stock_checker;
    $row[] = $duty;
    $row[] = $confirmed[$record->review_status];
    $row[] = $record->before_change_count;
    $row[] = $record->change_count;
    $row[] = $record->after_change_count;
    $row[] = lang($record->differences_remark);
    

    if((($role > 1) || ($CI->is_super_user()))){
        $row[] = lang('person_responsible') . form_dropdown('duty_' . $record->id, $all_stock_user_ids, $record->duty, "id='duty_$record->id'") . '<br/>' . form_textarea($config) . '<br/>' . block_button($button_config);
    } else {
        $row[] = form_textarea($config);
    }
    $row[] = $record->update_time;
    
    $data[] = $row;
}

echo block_header(lang('stock_differences_review'));
$stock_checkers = $this->stock_model->fetch_stock_checkers_and_duty();
$checkers = array();
$checkers[''] = lang('all');
$duty = array();
$duty[''] = lang('all');
foreach ($stock_checkers as $rows)
{
    if($rows->stock_checker > 0 && $rows->duty > 0)
    {
        $checkers[$rows->stock_checker] = $this->user_model->get_user_name_by_id($rows->stock_checker);
        $duty[$rows->duty] = $this->user_model->get_user_name_by_id($rows->duty);
    } else {
        $duty[$rows->duty] = '#';
    }
}
$filters = array(
    array(
        'type'   => 'input',
        'field'  => 'sku',      
    ),
    NULL,
    array(
        'type'      => 'dropdown',
        'field'     => 'stock_checker',
        'options'   => $checkers,
        'method'    => '=',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'duty',
        'options'   => $duty,
        'method'    => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'review_status',
        'options'   => $confirmed,
        'method'    => '=',
    ),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    array(
    'type'      => 'date',
    'field'     => 'update_time',
    'method'    => 'from_to'
	),
);

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_pagination('differences_review');
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'differences_review');
echo form_close();
echo $this->block->generate_pagination('differences_review');
?>
