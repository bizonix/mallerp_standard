<?php
$CI = & get_instance();
$url = site_url('order/ebay_message/add_new_template');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('message_template'),
    lang('content'),
    lang('creator'),
    lang('created_date'),
	lang('email_title'),
	lang('options') . $add_button,
);

$data = array();
$url = site_url('order/ebay_message/update_template');

foreach ($templates as $template)
{
	$drop_button = $this->block->generate_drop_icon(
        'order/ebay_message/drop_template_by_id',
        "{id: $template->id}",
        TRUE
    );
	$user_info = $CI->user_model->fetch_user_by_id($template->user);
    $data[] = array(
		$this->block->generate_div("template_name_{$template->id}", isset($template) && $template->template_name  ?  $template->template_name : '[edit]'),
		$this->block->generate_div("template_content_{$template->id}", isset($template) && $template->template_content  ?  $template->template_content : '[edit]'),
		$user_info->name,
		$template->created_date,
		$this->block->generate_div("template_subject_{$template->id}", isset($template) && $template->template_subject  ?  $template->template_subject : '[edit]'),
		$drop_button,
    );
    echo $this->block->generate_editor(
        "template_name_{$template->id}",
        'ebay_message_template_form',
        $url,
        "{id: $template->id, type: 'template_name'}"
    );
	echo $this->block->generate_editor(
        "template_content_{$template->id}",
        'ebay_message_template_form',
        $url,
        "{id: $template->id, type: 'template_content'}"
    );
	echo $this->block->generate_editor(
        "template_subject_{$template->id}",
        'ebay_message_template_form',
        $url,
        "{id: $template->id, type: 'template_subject'}"
    );
    
}
$title = lang('ebay_message_template_management');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
$note = lang('note') . ': ' . '<br/>' .
    lang('message_template_note');
echo block_notice_div($note);

?>
