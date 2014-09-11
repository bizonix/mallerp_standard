<?php
$CI = & get_instance();
$head = array(
	lang('select'),
    lang('message_id'),
	lang('status_id'),
	lang('category'),
    lang('message_type'),
	lang('message_sendid'),
	lang('email_title'),
	lang('product'),
	lang('created_date'),
	array('text' => lang('ebay_id'), 'sort_key' => 'ebay_user'),
	lang('options'),
);

$data = array();

foreach ($messages as $message)
{
	$edit_button = $this->block->generate_edit_link(site_url('order/ebay_message/ebay_message_reply', array($message->id)));
    $data[] = array(
		$this->block->generate_select_checkbox($message->id),
        $message->message_id,
		lang($CI->order_model->fetch_status_name('message_status', $message->status)),
		$CI->ebay_model->get_message_catalog_name($message->classid),
        $message->message_type, 
		$message->sendid,
		$message->subject,
		'<a href='.$message->itemurl.' target=_blank>'.$message->title.'</a>',
		$message->createtime,
		$message->ebay_user,
		$edit_button,
    );
}
$ops = $this->base_model->fetch_statuses('message_status');
$options = array();
$options[' '] = lang('all');
foreach ($ops as $op)
{
	$options[$op->status_id] = lang($op->status_name);
}

$ebay_id_str = $CI->sale_order_model->get_one('saler_ebay_id_map', 'ebay_id_str', array('saler_id'=> get_current_user_id()));
$ebay_ids = explode(',', $ebay_id_str);
if ($CI->is_super_user())
{
	$configs = $CI->config->item('ebay_id');
	$ebay_ids = array_values($configs);
}
$ebay_collection = array('' => lang('please_select'));
foreach ($ebay_ids as $ebay_id)
{
    $ebay_collection[$ebay_id] = $ebay_id;
}
$ebay_message_categorys=$CI->ebay_model->fetch_ebay_message_catalog();
$ebay_message_category = array('' => lang('please_select'),0 => lang('unknown'));
foreach ($ebay_message_categorys as $category)
{
	$ebay_message_category[$category->id] = $category->category_name;
}

$title = lang('ebay_message_reply');
$filters = array(
	NULL,
	array(
		'type'      => 'input',
		'field'     => 'message_id',
	),
	array(
        'type'      => 'dropdown',
        'field'     => 'status',
        'options'   => $options,
        'method'    => '=',
    ),
	array(
        'type'      => 'dropdown',
        'field'     => 'classid',
        'options'   => $ebay_message_category,
        'method'    => '=',
    ),
	array(
		'type'      => 'input',
		'field'     => 'message_type',
	),
	array(
		'type'      => 'input',
		'field'     => 'sendid',
	),
	array(
		'type'      => 'input',
		'field'     => 'subject|body|replaycontent',
	),
	array(
		'type'      => 'input',
        'field'     => 'title|itemid',
	),
	array(
    'type'      => 'date',
    'field'     => 'add_time',
    'method'    => 'from_to'
	),
	array(
        'type' => 'dropdown',
        'field' => 'ebay_id',
        'options' => $ebay_collection,
        'method' => '=',
    ),
	
);
echo block_header($title);

echo $this->block->generate_pagination('ebay_message');
$config = array(
	'filters'    => $filters,
);

$message_status_hold_url=site_url('order/ebay_message/message_status_hold');
$attributes = array(
    'id' => 'message_form',
);
echo form_open($message_status_hold_url, $attributes);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'ebay_message');
echo $this->block->generate_check_all();


$config = array(
    'name'      => 'message_status_hold',
    'id'        => 'message_status_hold',
    'value'     => lang('message_status_hold'),
	'type'      => 'submit',
    'onclick'   => "change_action('$message_status_hold_url');",
);
$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;

$message_status_needless_url=site_url('order/ebay_message/message_status_needless');
$config = array(
    'name'      => 'message_status_needless',
    'id'        => 'message_status_needless',
    'value'     => lang('message_status_needless'),
	'type'      => 'submit',
    'onclick'   => "change_action('$message_status_needless_url');",
);
$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;


echo form_close();
echo $this->block->generate_pagination('ebay_message');


?>
