<?php
$CI = &get_instance();
$head = array(
    array('text' => lang('content'), 'sort_key' => 'message', 'id' => 'messages'),
    array('text' => lang('author'), 'sort_key' => 'creator '),
    array('text' => lang('custom_date'), 'sort_key' => 'created_date'),
    array('text' => lang('already_read_or_not'), 'sort_key' => 'important_message_group.read'),
    lang('options'),
);

$author = $CI->myinfo_model->get_all_authors();


$read_statuse = array();
$read_statuse[''] = lang('all');
$read_statuse['0'] = lang('not_readed');
$read_statuse['1'] = lang('already_readed');

$data = array();

foreach ($rows as $row) {

    $drop_button = $this->block->generate_drop_icon(
        'myinfo/myaccount/drop_message',
        "{id: $row->group_id}",
        TRUE
    );
    $url = site_url('myinfo/myaccount/checkbox_read_edit');
    $readed = $row->read ? " checked" : "";
    $read_check =<<< html
    <input id=$row->id type='checkbox' onclick="checkbox('$url', '$row->id', '$row->read', '$row->group_name')" value='1'  $readed  >

html;
    $data[] = array(
        $row->message,
        $row->creator,
        $row->created_date,
        $read_check.lang('sing_as_readed'),
        $drop_button,
    );
}


$filters = array(
    array(
        'type' => 'input',
        'field' => 'message',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'creator',
        'options' => $author,
        'method' => '=',
    ),
    array(
        'type' => 'date',
        'field' => 'created_date',
        'method' => 'from_to'
    ),
    array(
        'type' => 'dropdown',
        'field' => 'important_message_group.read',
        'options' => $read_statuse,
        'method' => '=',
    ),
    null,
);

$title = lang('important_message_list');
echo block_header($title);

echo $this->block->generate_pagination('messages');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'messages');
echo form_close();
echo $this->block->generate_pagination('messages');
?>
