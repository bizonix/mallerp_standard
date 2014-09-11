<?php
$CI = & get_instance();
$head = array(
    lang('name'),
    lang('value'),
);
$data = array();

$config = array(
    'name'       => 'author',
    'id'         => 'author',
    'value'      => isset($work_rewards_error->author) ? $work_rewards_error->author : get_current_user_name(),
    'size'       => '100',
    'readonly'   => 'readonly',
);
$data[] = array(
    $this->block->generate_required_mark(lang('author')),
    form_input($config),
);

$type = array('please_select'=> lang('please_select'), 'reward' => lang('reward'), 'pulish' => lang('pulish'));
$str = form_dropdown('type', $type, isset($work_rewards_error->type) ? $work_rewards_error->type : 'please_select');

$data[] = array(
    $this->block->generate_required_mark(lang('type')),
    $str,
);


$str = form_dropdown('department', $department, isset($work_rewards_error->department) ? $work_rewards_error->department : '0');

$data[] = array(
    $this->block->generate_required_mark(lang('department')),
    $str,
);

$config = array(
    'name'       => 'worker_name',
    'id'         => 'worker_name',
    'value'      => $worker_id,
    'size'       => '100',
);

$data[] = array(
    $this->block->generate_required_mark(lang('work_rewards_error_person')),
    form_input($config),
);

$config = array(
    'name'       => 'order_no',
    'id'         => 'order_no',
    'value'      => isset($work_rewards_error->order_no) ? $work_rewards_error->order_no : '',
    'size'       => '100',
);

$data[] = array(
    lang('order_number'),
    form_input($config),
);

$config = array(
    'name'        => 'content_item',
    'id'          => 'content_item',
    'value'       => isset($work_rewards_error->content_item) ? $work_rewards_error->content_item : '',
    'cols'        => '100',
    'rows'        => '15',
);
$data[] = array(
    $this->block->generate_required_mark(lang('content')),
    form_textarea($config),
);

$priority = $this->user_model->fetch_user_priority_by_system_code('executive');
if($CI->is_super_user() || $priority > 1) {
    $config = array(
        'name'        => 'result',
        'id'          => 'result',
        'value'       => isset($work_rewards_error->result) ? $work_rewards_error->result : '',
        'cols'        => '100',
        'rows'        => '5',
    );
} else {
    $config = array(
        'name'        => 'result',
        'id'          => 'result',
        'value'       => isset($work_rewards_error->result) ? $work_rewards_error->result : '',
        'cols'        => '100',
        'rows'        => '5',
        'readonly'    =>'readonly',
    );
}
$data[] = array(
    lang('processing_result'),
    form_textarea($config),
);

$title =  lang('work_rewards_error');
$back_button = $this->block->generate_back_icon(site_url('myinfo/myaccount/work_rewards_error'));
echo block_header($title.$back_button);


$attributes = array(
    'id' => 'catalog_form',
);
echo form_open(site_url('myinfo/myaccount/work_rewards_error_save'), $attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('myinfo/myaccount/work_rewards_error_save');
$clue = lang('article_add_succeed');


$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('catalog_form').serialize(true), 1);",
);
echo form_hidden('contend_id', isset($work_rewards_error->id) ? $work_rewards_error->id : '');
$priority = $this->user_model->fetch_user_priority_by_system_code('executive');
$priority *= $this->user_model->fetch_user_priority_by_system_code('edu');
$priority *= $this->user_model->fetch_user_priority_by_system_code('finance');
$priority *= $this->user_model->fetch_user_priority_by_system_code('it');
$priority *= $this->user_model->fetch_user_priority_by_system_code('pi');
$priority *= $this->user_model->fetch_user_priority_by_system_code('purchase');
$priority *= $this->user_model->fetch_user_priority_by_system_code('qt');
$priority *= $this->user_model->fetch_user_priority_by_system_code('sale');
$priority *= $this->user_model->fetch_user_priority_by_system_code('seo');
$priority *= $this->user_model->fetch_user_priority_by_system_code('shipping');
$priority *= $this->user_model->fetch_user_priority_by_system_code('stock');
$priority_executive = $this->user_model->fetch_user_priority_by_system_code('executive');

if(isset($work_rewards_error->status))
{
    if($CI->is_super_user() OR $priority_executive > 1) {
        echo block_button($config) . $back_button;
    } elseif ($work_rewards_error->status == 'completed') {
        echo $back_button;
    } elseif (($work_rewards_error->status == 'wait_for_proccess') AND ($priority > 1)) {
        echo block_button($config) . $back_button;
    }
} else {
     if($CI->is_super_user() OR $priority > 1) {
        echo block_button($config) . $back_button;
     }
}
$js_array = '$A([';
if (isset($product_catalog))
{
    $items = explode('>', $product_catalog->path);
    $count = count($items);
    for ($i = 0; $i < $count; $i++)
    {
        $js_array .= $items[$i];
        if ($i < $count -1)
        {
            $js_array .= ',';
        }
    }
}
$js_array .= '])';

?>

<script>
    document.observe('dom:loaded', function(){
        //extand_catalog(<?=$js_array?>);
    });
</script>