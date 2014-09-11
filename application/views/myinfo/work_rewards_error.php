<?php
$CI = & get_instance();
$head = array(
    array('text' => lang('content'), 'sort_key' => 'content_item', 'id' => 'work_rewards'),
    array('text' => lang('department'), 'sort_key' => 'department'),
    array('text' => lang('processing_result'), 'sort_key' => 'result'),
    array('text' => lang('work_rewards_error_person'), 'sort_key' => 'worker_id'),
    array('text' => lang('order_number'), 'sort_key' => 'order_no'),
    array('text' => lang('author'), 'sort_key' => 'author'),
    array('text' => lang('document_catalog'), 'sort_key' => 'type'),
    array('text' => lang('processing_status'), 'sort_key' => 'status'),
    array('text' => lang('custom_date'), 'sort_key' => 'created_time'),
    lang('options'),
);

$data = array();
$priority = $this->user_model->fetch_user_priority_by_system_code('executive');

$dept = array();
$dept[''] = lang('all');
foreach($department_arr as $department) {
    $dept[$department->department] = str_replace('制度', '', $this->work_rewards_model->get_dept_name_by_id($department->department));
}

foreach ($work_rewards_errors as $work_rewards_error) {
    $drop_button = $this->block->generate_drop_icon(
                    'myinfo/myaccount/drop_work_rewards_error',
                    "{id: $work_rewards_error->id}",
                    TRUE
    );

    $id = $work_rewards_error->id;
    $work_id_arr = $this->work_rewards_model->fetch_worker_by_id($id);
    $worker = array();

    foreach ($work_id_arr as $work_id) {
        $worker[] = fetch_user_name_by_id($work_id['worker_id']);
    }

    $worker_id = implode(',', $worker);

    $edit_button = $this->block->generate_edit_link(site_url('myinfo/myaccount/work_rewards_error_edit', array($work_rewards_error->id)), FALSE);

    $department = $this->document_catalog_model->fetch_name_by_dept_id($work_rewards_error->department);
    if ($CI->is_super_user() OR $priority > 1) {
        $drop_button .= $edit_button;
    } else {
        $drop_button = $edit_button;
    }
    $data[] = array(
        trim_right(substr($work_rewards_error->content_item, 0, 100)),
        $department,
        $work_rewards_error->result,
        $worker_id,
//        $work_rewards_error->name,
        $work_rewards_error->order_no,
        $work_rewards_error->author,
        lang($work_rewards_error->type),
        lang($work_rewards_error->status),
        $work_rewards_error->created_time,
        $drop_button,
    );
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'content_item',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'department',
        'options' => $dept,
    ),
    array(
        'type' => 'input',
        'field' => 'result',
    ),
    array(
        'type' => 'input',
        'field' => 'name',
    ),
    array(
        'type' => 'input',
        'field' => 'order_no',
    ),
    array(
        'type' => 'input',
        'field' => 'author',
    ),
);
$type = array('' => lang('please_select'), 'reward' => lang('reward'), 'pulish' => lang('pulish'));
$filters[] = array(
    'type' => 'dropdown',
    'field' => 'type',
    'options' => $type,
    'method' => '=',
);
$status = array('' => lang('please_select'), 'wait_for_proccess' => lang('wait_for_proccess'), 'completed' => lang('completed'));
$filters[] = array(
    'type' => 'dropdown',
    'field' => 'status',
    'options' => $status,
    'method' => '=',
);

$filters[] = array(
    'type' => 'date',
    'field' => 'created_time',
    'method' => 'from_to'
);
$filters[] = '';



$title = lang('work_rewards_error');
echo block_header($title);

echo $this->block->generate_pagination('work_rewards');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'work_rewards');
echo form_close();
echo $this->block->generate_pagination('work_rewards');
?>
