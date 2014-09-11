<?php
$url = site_url('order/power_manage/add_power_management');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('superintendent'),
    lang('login_name'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('order/power_manage/verigy_exchange_power_management');
foreach ($power_managements as $power_management)
{
    $drop_button = $this->block->generate_drop_icon(
        'order/power_manage/drop_power_management_by_id',
        "{id: $power_management->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("superintendent_id_{$power_management->id}", isset($power_management) &&$power_management->u_name  ?  $power_management->u_name : '[edit]'),
        $this->block->generate_div("login_name_str_{$power_management->id}", isset($power_management) ?  $power_management->login_name_str : '[edit]'),
        $drop_button,
    );

    $order_options = array();
    foreach($all_order_users as $all_order_user)
    {
        $order_options[$all_order_user->u_id] = $all_order_user->u_name;
    }
    $collection = to_js_array($order_options);

    echo $this->block->generate_editor(
        "superintendent_id_{$power_management->id}",
        'order_form',
        $code_url,
        "{id: $power_management->id, type: 'superintendent_id'}",
        $collection
    );
    echo $this->block->generate_editor(
        "login_name_str_{$power_management->id}",
        'order_form',
        $code_url,
        "{id: $power_management->id, type: 'login_name_str'}"
    );
}
$title = lang('manage_power_set');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
