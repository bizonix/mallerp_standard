<?php
$CI = & get_instance();
$head = array(
    array('text' => lang('chinese_name'), 'sort_key' => 'name', 'id' => 'user'),
    array('text' => lang('english_name'), 'sort_key' => 'name_en'),
    );
if($CI->is_super_user())
{
    $head[] = array('text' => lang('qq'), 'sort_key' => 'QQ');
    $head[] = array('text' => lang('QQ_pwd'), 'sort_key' => 'QQ_pwd');
    $head[] = array('text' => lang('RTX'), 'sort_key' => 'RTX');
    $head[] = array('text' => lang('RTX_pwd'), 'sort_key' => 'RTX_pwd');
    $head[] = array('text' => lang('fileserv_username'), 'sort_key' => 'fileserv_username');
    $head[] = array('text' => lang('fileserv_pwd'), 'sort_key' => 'fileserv_pwd');
    $head[] = array('text' => lang('email'), 'sort_key' => 'email');
    $head[] = array('text' => lang('email_pwd'), 'sort_key' => 'email_pwd');
    $head[] = array('text' => lang('msn'), 'sort_key' => 'msn');
    $head[] = array('text' => lang('msn_pwd'), 'sort_key' => 'msn_pwd');
    $head[] = array('text' => lang('skype'), 'sort_key' => 'skype');
    $head[] = array('text' => lang('skype_pwd'), 'sort_key' => 'skype_pwd');
    $head[] = array('text' => lang('taobao_username'), 'sort_key' => 'taobao_username');
    $head[] = array('text' => lang('taobao_pwd'), 'sort_key' => 'taobao_pwd');
 }
    $head[] = array('text' => lang('birthday'), 'sort_key' => 'birthday');
    $head[] = array('text' => lang('contrct_time'), 'sort_key' => 'contrct_time');
    $head[] = array('text' => lang('trial_end_time'), 'sort_key' => 'trial_end_time');
    $head[] = lang('options');

$data = array();
foreach($users as $user)
{
    if ($action == 'edit')
    {
        $edit_button = $this->block->generate_edit_link(site_url('myinfo/myaccount/edit', array($user->id)));
        $url = $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('myinfo/myacount/view', array($user->id)));
    }
   if($CI->is_super_user())
   {
    $data[] = array(
        $user->name,
        $user->name_en,
        $user->QQ,
        $user->QQ_pwd,
        $user->RTX,
        $user->RTX_pwd,
        $user->fileserv_username,
        $user->fileserv_pwd,
        $user->email,
        $user->email_pwd,
        $user->msn,
        $user->msn_pwd,
        $user->skype,
        $user->skype_pwd,
        $user->taobao_username,
        $user->taobao_pwd,
        $user->birthday,
        $user->contrct_time,
        $user->trial_end_time,
        $url,
        );
   }
   else
   {
    $data[] = array(
        $user->name,
        $user->name_en,
        $user->birthday,
        $user->contrct_time,
        $user->trial_end_time,
        $url,
      );
   }
}
if($action == 'edit')
{
    $title = lang('staff_management');
}
else
{
    $title = lang('staff_view');
}
echo block_header($title);
if($CI->is_super_user())
{
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'user.name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'name_en',
    ),
    array(
        'type'      => 'input',
        'field'     => 'QQ',
    ),
    array(
        'type'      => 'input',
        'field'     => 'QQ_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'RTX',
    ),
    array(
        'type'      => 'input',
        'field'     => 'RTX_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'fileserv_username',
    ),
    array(
        'type'      => 'input',
        'field'     => 'fileserv_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'email',
    ),
    array(
        'type'      => 'input',
        'field'     => 'email_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'msn',
    ),
    array(
        'type'      => 'input',
        'field'     => 'msn_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'skype',
    ),
    array(
        'type'      => 'input',
        'field'     => 'skype_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'taobao_name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'taobao_pwd',
    ),
    array(
        'type'      => 'input',
        'field'     => 'birthday',
    ),
    array(
        'type'      => 'input',
        'field'     => 'contrct_time',
    ),
    array(
        'type'      => 'input',
        'field'     => 'trial_end_time',
    ),
    NULL,
);
}
else
{
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'name_en',
    ),
     array(
        'type'      => 'input',
        'field'     => 'birthday',
    ),
    array(
        'type'      => 'input',
        'field'     => 'contrct_time',
    ),
    array(
        'type'      => 'input',
        'field'     => 'trial_end_time',
    ),
    NULL,
   );
}
echo $this->block->generate_pagination('user');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'user');
echo $this->block->generate_pagination('user');
echo form_close();
?>
