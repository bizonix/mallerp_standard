<?php
$CI = & get_instance();
$back_button = block_back_icon(site_url('shipping/deliver_management/before_late_print_label'));
$head = array(
    lang('item_no'),
    lang('order_status'),
);

$data = array();
foreach ($orders as $key=>$order)
{
	//echo $key.":".$transaction_detail."<br>";
	$data[] = array(
        $order->item_no,
		lang($CI->order_model->fetch_status_name('order_status', $order->order_status)), 
    );
}
echo block_header(lang('change_order_shiped').$back_button);
echo $this->block->generate_table($head, $data);
?>
