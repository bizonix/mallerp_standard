<?php
$head = array(
    array('text' => lang('chinese_name'), 'sort_key' => 'name', 'id' => 'user'),
    array('text' => lang('contract_notice_day'), 'sort_key' => 'contrct_time'),
    array('text' => lang('probation_notice_day'), 'sort_key' => 'trial_end_time '),
    array('text' => lang('birthday_notice_day'), 'sort_key' => 'birthday'),
    );
$data = array();
$contract_time = $day_infos->contract_time;
$probation_time = $day_infos->probation_time;
$birthday = $day_infos->birthday;
foreach($users as $user)
{
    if ($action == 'edit')
    {
        $edit_button = $this->block->generate_edit_link(site_url('myinfo/myaccount/expire_day_edit', array($user->id)));
        $url = $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('myinfo/myacount/expire_day_view', array($user->id)));
    }
    $birthday_cur = substr($user->birthday,5);
    $birthday_cur_time = date("Y")."-".$birthday_cur;
    $birthday_com_time = strtotime($birthday_cur_time) - mktime();
    $contract_com_time = strtotime($user->contrct_time) - mktime();
    $trial_com_time = strtotime($user->trial_end_time) - mktime();
    $contract_times = secs_to_readable($contract_com_time);
    $contract_day = $contract_times['days'];
    $probation_times = secs_to_readable($trial_com_time);
    $probation_day = $probation_times ['days'];
    $birthday_times = secs_to_readable($birthday_com_time);
    $birthday_day = $birthday_times['days'];
    if(($contract_day <$contract_time && $contract_day>0) || ($probation_day<$probation_time && $probation_day>0) || ($birthday_day<$birthday && $birthday_day>0))
    {
      $data[] = array(
        $user->name,
        $contract_day.lang('day'),
        $probation_day.lang('day'),
        $birthday_day.lang('day'),
      );
    }
}
    $title = lang('coming_day_info_manage');
    echo block_header($title);
    $filters = array(
    array(
        'type'      => 'input',
        'field'     => 'name',
    ),
    array(
        'type'      => 'input',
        'field'     => 'contrct_time',
    ),
    array(
        'type'      => 'input',
        'field'     => 'trial_end_time',
    ),
    array(
        'type'      => 'input',
        'field'     => 'QQ',
    )
  );
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
