<?php

$head = array(
    lang('group_name'),
    lang('user_name'),
);

$data = array();
$url = site_url('order/setting/proccess_update_view_all');

foreach ($users as $group_name => $value)
{
    $str = '';
    foreach ($value as $user)
    {
        $user_id = $user['user_id'];
        $checkbox = array(
            'name'        => 'user_' . $user['user_id'],
            'id'          => 'user_' . $user_id,
            'value'       => $user_id,
            'checked'     => in_array($user_id, $setted_user_ids) ? TRUE : FALSE,
            'onclick'     => "helper.ajax('$url', {user_id: $user_id, checked: this.checked}, 1)",
        );
        $str .= form_checkbox($checkbox) . form_label($user['user_name']) . '&nbsp;&nbsp;&nbsp;';
    }
    $data[] = array(
        $group_name,
        $str,
    );
}

$back_button = $this->block->generate_back_icon(site_url('order/setting/order_view_permission'));
$title = lang('update_view_all_permission'). $back_button;
echo block_header($title);
echo $this->block->generate_table($head, $data);
echo $back_button;

echo "<div style='clear:both;'><div>";
?>