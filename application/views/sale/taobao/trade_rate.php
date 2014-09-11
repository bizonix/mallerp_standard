<?php
$CI = & get_instance();
$head = array(
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'rate'),
    array('text' => lang('product_name'), 'sort_key' => 'item_title', 'id' => 'rate'),
    array('text' => lang('price'), 'sort_key' => 'item_price', 'id' => 'rate'),
    array('text' => lang('comment_list'), 'sort_key' => 'content', 'id' => 'rate'),
    array('text' => lang('comment_results'), 'sort_key' => 'result', 'id' => 'rate'),
    array('text' => lang('comment_time'), 'sort_key' => 'created', 'id' => 'rate'),
    array('text' => lang('nick'), 'sort_key' => 'nick', 'id' => 'rate'),
    array('text' => lang('review_state'), 'sort_key' => 'review', 'id' => 'rate'),
);
$data = array();
$confirm_review_url = site_url('sale/taobao/confirm_review');
$bad_type_confirm_url = site_url('sale/taobao/bad_type_confirm');
$role = $this->user_model->fetch_user_priority_by_system_code('sale');
$options = array();
$bad_comment_id = array();
foreach ($bad_comment_types as $bad_comment_type)
{
    $options[''] = lang('please_select');
    $options[$bad_comment_type->id] = $bad_comment_type->type;
}
foreach ($rate as $rate )
{
    $result = lang($rate->result);
    $row = array();
    $row[] = $rate->sku;
    $row[] =  $rate->item_title;
    $row[] = $rate->item_price;
    $row[] = $rate->content;
    $row[] = $result;
    $row[] = $rate->created;
    $row[] = $rate->nick;

    $comment_type_ = 'comment_type_';
    $give_back = '';
    if (in_array($rate->result, array('bad', 'bad_verify')))
    {
        $give_back .= form_dropdown('comment_type_' . $rate->id, $options, $rate->bad_type, 'id="'.$comment_type_.$rate->id.'"');
        $give_back .= br();
        $config = array(
            'name'        => 'remark_' . $rate->id,
            'id'          => 'remark_' . $rate->id,
            'value'          => $rate->review,
            'rows'        => '2',
            'cols'        => '14',
        );
        $give_back .= form_textarea($config);
        $config = array(
            'name' => 'confirm_' . $rate->id,
            'id' => 'confirm_' . $rate->id,
            'value' => lang('confirm_review'),
            'onclick' => "confirm_review('$confirm_review_url', $rate->id);",
        );

        if((($role > 1)|| ($CI->is_super_user()))){
            $give_back .= '<br/>' . block_button($config);
        }
        else if (($rate->result == 'bad'))
        {
            $give_back .= '<br/>' . block_button($config);
        }
    }

    $row[] = $give_back;

    $data[] = $row;
}

$results = array(
    '' => lang('all'),
    'good' => lang('good'),
    'neutral' => lang('neutral'),
    'bad' => lang('bad'),
    'good_verify' => lang('good_verify'),
    'neutral_verify' => lang('neutral_verify'),
    'bad_verify' => lang('bad_verify'),
);

$roles = array(
    '' => lang('all'),
    'buyer' => lang('buyer'),
    'seller' => lang('seller'),
);


$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'sku',
	),
	array(
		'type'      => 'input',
		'field'     => 'item_title',
	),
	array(
		'type'      => 'input',
		'field'     => 'item_price',
	),
	array(
		'type'      => 'input',
		'field'     => 'content',
	),
	array(
		'type'      => 'dropdown',
		'field'     => 'result',
                'options'   => $results,
                'method'   => '=',
	),
        array(
		'type'      => 'input',
		'field'     => 'created',
	),
        array(
		'type'      => 'input',
		'field'     => 'nick',
	),
);

$config = array(
	'filters'    => $filters,
);

$title = lang('comment_list');
echo block_header($title);

echo $this->block->generate_pagination('rate');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'rate');

echo form_close();

echo $this->block->generate_pagination('rate');

?>