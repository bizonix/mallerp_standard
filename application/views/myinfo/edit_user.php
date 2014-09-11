<?php
$CI = & get_instance();
$head = array(
    lang('name'),
    lang('value'),
);
$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       =>  $user ? $user->name : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'value'       =>  $user ? $user->name_en : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('english_name')),
    form_input($config),
);
if($CI->is_super_user())
{
$config = array(
    'name'        => 'QQ',
    'id'          => 'QQ',
    'value'       =>  $user ? $user->QQ : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('qq')),
    form_input($config),
);

$config = array(
    'name'        => 'QQ_pwd',
    'id'          => 'QQ_pwd',
    'value'       =>  $user ? $user->QQ_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('QQ_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'RTX',
    'id'          => 'RTX',
    'value'       =>  $user ? $user->RTX : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('RTX')),
    form_input($config),
);

$config = array(
    'name'        => 'RTX_pwd',
    'id'          => 'RTX_pwd',
    'value'       =>  $user ? $user->RTX_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('RTX_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'fileserv_username',
    'id'          => 'fileserv_username',
    'value'       =>  $user ? $user->fileserv_username : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('fileserv_username')),
    form_input($config),
);

$config = array(
    'name'        => 'fileserv_pwd',
    'id'          => 'fileserv_pwd',
    'value'       =>  $user ? $user->fileserv_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('fileserv_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'email',
    'id'          => 'email',
    'value'       =>  $user ? $user->email : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('email')),
    form_input($config),
);

$config = array(
    'name'        => 'email_pwd',
    'id'          => 'email_pwd',
    'value'       =>  $user ? $user->email_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('email_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'msn',
    'id'          => 'msn',
    'value'       =>  $user ? $user->msn : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('msn')),
    form_input($config),
);

$config = array(
    'name'        => 'msn_pwd',
    'id'          => 'msn_pwd',
    'value'       =>  $user ? $user->msn_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('msn_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'skype',
    'id'          => 'skype',
    'value'       =>  $user ? $user->skype : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('skype')),
    form_input($config),
);

$config = array(
    'name'        => 'skype_pwd',
    'id'          => 'skype_pwd',
    'value'       =>  $user ? $user->skype_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('skype_pwd')),
    form_input($config),
);

$config = array(
    'name'        => 'taobao_username',
    'id'          => 'taobao_username',
    'value'       =>  $user ? $user->taobao_username : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('taobao_username')),
    form_input($config),
);

$config = array(
    'name'        => 'taobao_pwd',
    'id'          => 'taobao_pwd',
    'value'       =>  $user ? $user->taobao_pwd : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('taobao_pwd')),
    form_input($config),
);
}

$data[] = array(
    $this->block->generate_required_mark(lang('birthday')),
    block_time_picker('birthday', $user ? $user->birthday : ''),
);


$data[] = array(
    $this->block->generate_required_mark(lang('contrct_time')),
    block_time_picker('contrct_time', $user ? $user->contrct_time : ''),
);

$config = array(
    'name'        => 'trial_end_time',
    'id'          => 'trial_end_time',
    'value'       =>  $user ? $user->trial_end_time : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('trial_end_time')),
    block_time_picker('trial_end_time', $user ? $user->trial_end_time : ''),
);

if($action == "edit")
{
    $title = lang('edit_user');
    $title .= $this->block->generate_back_icon(site_url('myinfo/myaccount/staff_manage'));
    $back_button = $this->block->generate_back_icon(site_url('myinfo/myaccount/staff_manage'));
}
echo block_header($title);
$attributes = array(
    'id' => 'edit_user_form',
);
echo form_open(site_url('myinfo/myaccount/edit_save'),$attributes);
echo $this->block->generate_table($head, $data);
if($action == 'edit')
{
    $url = site_url('myinfo/myaccount/edit_save',array('user_id' =>$user ? $user->id : ''));
    $config = array(
        'name'        => 'submit',
        'value'       => 'Save user!',
        'type'        => 'button',
        'style'       => 'margin:10px',
        'onclick'     => "this.blur();helper.ajax('$url',$('edit_user_form').serialize(true), 1);",
    );
    $attributes = array(
         'id'         => $user ? $user->id : '',
         'type'       => 'button',
         'style'      => 'margin:10px',

    );
    echo '<h2>'.block_button($config).$back_button.'</h2>';

}
echo form_close();
?>
