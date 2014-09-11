<?php

$title = lang('order_bad_comment_statistic');
echo block_header($title);
if($rows) {
$CI = &get_instance();
$head = array(
    lang('order_number'),
    lang('content'),
    lang('order_bad_comment_type'),
);

$data = array();

foreach ($rows as $row) {
    $data[] = array(
        $row->item_no,
        $row->feedback_content,
        $CI->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->verify_type)),
    );
}

echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
} else {
    echo lang('no_bad_comment_statistic');
}
?>
