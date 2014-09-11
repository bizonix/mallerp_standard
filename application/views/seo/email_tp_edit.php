<?php
$title =  block_header(lang('email_template'));
$back_button = $this->block->generate_back_icon(site_url('seo/email_marketing/email_marketing_manage'));
echo $title.$back_button;
$content = $email_infos->content;
echo block_clickable_fieldset(lang('view_template'), $content, 'view');
echo br(), br();


$config = array(
    'name'       => 'mail_title',
    'id'         => 'mail_title',
    'value'      => $email_infos->title,
    'size'       => '100',
);
$email_title = $this->block->generate_required_mark(lang('email_title'));
$email_title.= form_input($config)."<br>";

$config = array(
    'name'       => 'mail_code',
    'id'         => 'mail_code',
    'value'      => $email_infos->code,
    'size'       => '100',
);

if($email_infos->code)
{
    $config = array_merge($config, array('readonly'  => 'readonly'));
}

$email_code = $this->block->generate_required_mark(lang('email_code'));
$email_code.= form_input($config)."<br>";

$config = array(
    'name'        => 'mail_content',
    'id'          => 'mail_content',
    'value'       => $email_infos->content,
    'cols'        => '100',
    'rows'        => '25',
);
$textarea = $this->block->generate_required_mark(lang('email_content'))."<br>";
$textarea.= form_textarea($config);
$url = site_url('seo/email_marketing/edit_save');
$config = array(
    'name'      => 'save_template',
    'id'        => 'save_template',
    'value'     => lang('save_template'),
    'onclick'   => "return save_email_template('$url', '$email_infos->id')",
);
$button = block_button($config);

$content = $email_title . br() .$email_code . br() .$textarea . br() . $button;
echo block_clickable_fieldset(lang('edit_template'), $content, 'edit');
echo $back_button;

?>
