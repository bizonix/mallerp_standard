<?php

$title = lang('order_return_cost_statistic');
echo block_header($title);
if($query->docs) {
$CI = &get_instance();
$head = array(
    lang('order_number'),
    lang('content'),
    lang('refund_verify_type'),
);

$data = array();

foreach ($query->docs as $row) {
    $data[] = array(
        $row->item_no,
        $row->refund_verify_content,
        $CI->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type)),
    );
}

echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
} else {
    echo lang('no_bad_comment_statistic');

}
?>
