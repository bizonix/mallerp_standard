<?php
$back_button = block_back_icon(site_url('order/paypalapi/refundtransaction'));
$head = array(
    lang('key'),
    lang('value'),
);

$data = array();
foreach ($refundtransaction_details as $key=>$transaction_detail)
{
	//echo $key.":".$transaction_detail."<br>";
	$data[] = array(
        $key,
		$transaction_detail, 
    );
}
echo block_header(lang('paypal_refund_transaction').$back_button);
echo $this->block->generate_table($head, $data);
?>
