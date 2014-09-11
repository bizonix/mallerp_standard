<?php
$url = site_url('order/profit_rate/add_profit_rate_view');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('start_rate'),
    lang('end_rate'),
    lang('full_name'),
    lang('creator'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('order/profit_rate/verigy_exchange_profit_rate_view');



foreach ($profit_rate_lists as $profit_rate_list)
{
    $drop_button = $this->block->generate_drop_icon(
        'order/profit_rate/drop_profit_rate_view_by_id',
        "{id: $profit_rate_list->id}",
        TRUE
    );

    $edit_url = block_edit_link(site_url('order/profit_rate/update_view_all',array($profit_rate_list->id)));
    $str = '';
    foreach ($name_string["$profit_rate_list->id"] as $name)
    {
        $str .= $name->u_name . '&nbsp;&nbsp;';
    }
    $str .= $edit_url;

    $data[] = array(
        $this->block->generate_div("start_rate_{$profit_rate_list->id}", isset($profit_rate_list) && $profit_rate_list->start_rate  ?  $profit_rate_list->start_rate : '[edit]'),
        $this->block->generate_div("end_rate_{$profit_rate_list->id}", isset($profit_rate_list) && $profit_rate_list->end_rate?  $profit_rate_list->end_rate : '[edit]'),
        $str,
        $this->block->generate_div("creator_{$profit_rate_list->id}", isset($profit_rate_list)? $profit_rate_list->u_name:''),
        $drop_button,
    );

    echo $this->block->generate_editor(
        "start_rate_{$profit_rate_list->id}",
        'order_form',
        $code_url,
        "{id: $profit_rate_list->id, type: 'start_rate'}"
    );
    echo $this->block->generate_editor(
        "end_rate_{$profit_rate_list->id}",
        'order_form',
        $code_url,
        "{id: $profit_rate_list->id, type: 'end_rate'}"
    );
}
$title = lang('order_profit_rate_view_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
