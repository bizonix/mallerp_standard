<?php

$data = array();

$data[] = array(
    'label' => lang('login_name'),
    'value' => $user->login_name,
);

$data[] = array(
    'label'  => lang('role'),
    'value' => $this->role_level_model->fetch_role_name_by_id($user->role),
);

$config = array(
    'name'        => 'myname',
    'id'          => 'myname',
    'value'       => $user->name,
    'maxlength'   => '50',
    'size'        => '20',
);

$data[] = array(
    'label' => block_required_mark(lang('myname')),
    'value' => form_input($config),
);
$integral = fetch_user_all_integrals($user->id);
if($integral > 0)
{
    $data[] = array(
        'label' => lang('my_integral'),
        'value' => $integral,
    );
}
$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'value'       => $user->name_en,
    'maxlength'   => '50',
    'size'        => '20',
);

$data[] = array(
    'label' => lang('english_name'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'phone',
    'id'          => 'phone',
    'value'       => $user->phone,
    'maxlength'   => '50',
    'size'        => '30',
);

$data[] = array(
    'label' => lang('phone'),
    'value' => form_input($config).lang('phone_example'),
);

$config = array(
    'name'        => 'platform1',
    'id'          => 'platform1',
    'value'       => $user->platform1,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('platform1'),
    'value' => form_input($config).lang('case_example'),
);

$config = array(
    'name'        => 'platform2',
    'id'          => 'platform2',
    'value'       => $user->platform2,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('platform2'),
    'value' => form_input($config),
);


$config = array(
    'name'        => 'email',
    'id'          => 'email',
    'value'       => $user->email,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('email'),
    'value' => form_input($config).lang('email_example'),
);

$config = array(
    'name'        => 'email_pwd',
    'id'          => 'email_pwd',
    'value'       => $user->email_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('email_pwd'),
    'value' => form_input($config),
);



$config = array(
    'name'        => 'msn',
    'id'          => 'msn',
    'value'       => $user->msn,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('msn'),
    'value' => form_input($config).lang('msn_example'),
);

$config = array(
    'name'        => 'msn_pwd',
    'id'          => 'msn_pwd',
    'value'       => $user->msn_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('msn_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'skype',
    'id'          => 'skype',
    'value'       => $user->skype,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('skype'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'skype_pwd',
    'id'          => 'skype_pwd',
    'value'       => $user->skype_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('skype_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'QQ',
    'id'          => 'QQ',
    'value'       => $user->QQ,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('QQ'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'QQ_pwd',
    'id'          => 'QQ_pwd',
    'value'       => $user->QQ_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('QQ_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'RTX',
    'id'          => 'RTX',
    'value'       => $user->RTX,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('RTX'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'RTX_pwd',
    'id'          => 'RTX_pwd',
    'value'       => $user->RTX_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('RTX_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'fileserv_username',
    'id'          => 'fileserv_username',
    'value'       => $user->fileserv_username,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('fileserv_username'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'fileserv_pwd',
    'id'          => 'fileserv_pwd',
    'value'       => $user->fileserv_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('fileserv_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'taobao_username',
    'id'          => 'taobao_username',
    'value'       => $user->taobao_username,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('taobao_username'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'taobao_pwd',
    'id'          => 'taobao_pwd',
    'value'       => $user->taobao_pwd,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('taobao_pwd'),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'birthday',
    'id'          => 'birthday',
    'value'       => $user->birthday,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('birthday'),
    'value' => form_input($config).lang('birthday_example'),
);

$config = array(
    'name'        => 'contrct_time',
    'id'          => 'contrct_time',
    'value'       => $user->contrct_time,
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    'label' => lang('contrct_time'),
    'value' => form_input($config),
);
$config = array(
    'name'        => 'trial_end_time',
    'id'          => 'trial_end_time',
    'value'       => $user->trial_end_time,
    'maxlength'   => '50',
    'size'        => '50',
    'readonly'    => 'true',
);

$data[] = array(
    'label' => lang('trial_end_time'),
    'value' => form_input($config),
);
$url = site_url('myinfo/myaccount/proccess_update_account');
$params = "{myname: $('myname').value, name_en: $('name_en').value, phone: $('phone').value, msn: $('msn').value, skype: $('skype').value, platform1: $('platform1').value, platform2: $('platform2').value, email_pwd: $('email_pwd').value, QQ: $('QQ').value, QQ_pwd: $('QQ_pwd').value, RTX: $('RTX').value, RTX_pwd: $('RTX_pwd').value, msn_pwd: $('msn_pwd').value, birthday: $('birthday').value, contrct_time: $('contrct_time').value, skype_pwd: $('skype_pwd').value, taobao_username: $('taobao_username').value, taobao_pwd: $('taobao_pwd').value, fileserv_username: $('fileserv_username').value, fileserv_pwd: $('fileserv_pwd').value, email: $('email').value, trial_end_time: $('trial_end_time').value}";
$save_button = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'onclick'     => "this.blur();helper.ajax('$url', $params, 1);",
);

echo block_entry(lang('account_information'), $data, $save_button);

$data = array();

$config = array(
    'name'        => 'old_password',
    'id'          => 'old_password',
    'value'       => '',
    'type'        => 'password',
    'maxlength'   => '50',
    'size'        => '20',
);
$data[] = array(
    'label' => block_required_mark(lang('old_password')),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'password',
    'id'          => 'password',
    'value'       => '',
    'type'        => 'password',
    'maxlength'   => '50',
    'size'        => '20',
);
$data[] = array(
    'label' => block_required_mark(lang('new_password')),
    'value' => form_input($config),
);

$config = array(
    'name'        => 'confirm_password',
    'id'          => 'confirm_password',
    'value'       => '',
    'type'        => 'password',
    'maxlength'   => '50',
    'size'        => '20',
);
$data[] = array(
    'label' => block_required_mark(lang('confirm_password')),
    'value' => form_input($config),
);

$url = site_url('myinfo/myaccount/proccess_update_password');
$params = "{old_password: $('old_password').value,password: $('password').value, confirm_password: $('confirm_password').value}";

$save_button = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'onclick'     => "this.blur();helper.ajax('$url', $params, 1);",
);

echo block_entry(lang('change_password'), $data, $save_button, FALSE);

?>