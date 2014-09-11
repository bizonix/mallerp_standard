<?php
$head = array(
    lang('name'),
    lang('value'),
);
$config = array(
    'name'        => 'contract_time',
    'id'          => 'contract_time',
    'maxlength'   => '20',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('contract_notice_day')),
    form_input($config).lang('day'),
);

$config = array(
    'name'        => 'probation_time',
    'id'          => 'probation_time',
    'maxlength'   => '20',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('probation_notice_day')),
    form_input($config).lang('day'),
);

$config = array(
    'name'        => 'birthday',
    'id'          => 'birthday',
    'maxlength'   => '20',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('birthday_notice_day')),
    form_input($config).lang('day'),
);
$create_date = date("Y-m-d H:i:s");
$data[] = form_hidden('create_date',$create_date);
$title =lang('day_notice') . $this->block->generate_back_icon(site_url('myinfo/myaccount/expire_day_manage'));
$back_button = $this->block->generate_back_icon(site_url('myinfo/myaccount/expire_day_manage'));
echo block_header($title);
$attributes = array(
    'id' => 'expire_day_add_form',
);
echo form_open(site_url('myinfo/myaccount/save_expire_day'), $attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('myinfo/myaccount/save_expire_day');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('expire_day_add_form').serialize(true), 1);",
);
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();


?>
