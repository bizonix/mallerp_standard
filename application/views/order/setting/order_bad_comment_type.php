<?php

$url = site_url('order/setting/add_bad_comment_type');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('order_bad_comment_type'),
    lang('order_status'),
    lang('person_responsible'),
    lang('department_responsible'),
    lang('is_show_sku'),
    lang('confirm_required'),
    lang('creator'),
    lang('created_date'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('order/setting/verify_bad_comment_type');

$system_names = to_js_array($system_names);  
foreach ($comment_types as $comment_type) {
    $drop_button = $this->block->generate_drop_icon(
                    'order/setting/drop_bad_comment_type',
                    "{id: $comment_type->id}",
                    TRUE
    );
    $creator_name = $this->user_model->fetch_user_name_by_id($comment_type->creator);
    $confirm_required = ($comment_type->confirm_required) ? lang('yes') : lang('no');
    $default_refund_show_sku = ($comment_type->default_refund_show_sku) ? lang('yes') : lang('no');

    $department = lang($this->order_model->get_one('system', 'name', array('code' => $comment_type->department)));
    $data[] = array(
        $this->block->generate_div("comment_type_{$comment_type->id}", empty($comment_type->type) ? '[edit]' : $comment_type->type),
        $this->block->generate_div("default_refund_type_{$comment_type->id}", empty($comment_type->default_refund_type) ? '[edit]' : lang(fetch_status_name('order_status',$comment_type->default_refund_type))),
        $this->block->generate_div("default_refund_duty_{$comment_type->id}", empty($comment_type->default_refund_duty) ? '[edit]' : $comment_type->default_refund_duty),
        $this->block->generate_div("default_refund_department_duty_{$comment_type->id}", empty($department) ? '[edit]' : $department),
        $this->block->generate_div("default_refund_show_sku_{$comment_type->id}", $default_refund_show_sku),
        $this->block->generate_div("confirm_required_{$comment_type->id}", empty($confirm_required) ? '[edit]' : $confirm_required),
        $creator_name,
        $comment_type->created_date,
        $drop_button,
    );

    $order_status_collection = array(''=>lang('please_select'));
    
    foreach ($order_status as $key => $value)
    {
        $order_status_collection[$key] = lang($value);
    }
    $collection = to_js_array($order_status_collection);
    
    echo $this->block->generate_editor(
            "default_refund_type_{$comment_type->id}",
            'comment_type_form',
            $code_url,
            "{id: $comment_type->id, type: 'default_refund_type'}",
             $collection
    );
            
    echo $this->block->generate_editor(
        "default_refund_duty_{$comment_type->id}",
        'comment_type_form',
        $code_url,
        "{id: $comment_type->id, type: 'default_refund_duty'}"
    );


       

    echo $this->block->generate_editor(
        "default_refund_department_duty_{$comment_type->id}",
        'comment_type_form',
        $code_url,
        "{id: $comment_type->id, type: 'department'}",
        $system_names
    );

    echo $this->block->generate_editor(
            "comment_type_{$comment_type->id}",
            'comment_type_form',
            $code_url,
            "{id: $comment_type->id, type: 'type'}"
    );
    $confirm_required = to_js_array(array('1' => lang('yes'), '0' => lang('no')));
    echo $this->block->generate_editor(
            "confirm_required_{$comment_type->id}",
            'comment_type_form',
            $code_url,
            "{id: $comment_type->id, type: 'confirm_required'}",
             $confirm_required
    );
            
    echo $this->block->generate_editor(
            "default_refund_show_sku_{$comment_type->id}",
            'comment_type_form',
            $code_url,
            "{id: $comment_type->id, type: 'default_refund_show_sku'}",
             $confirm_required
    );
}
$title = lang('order_bad_comment_type_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
