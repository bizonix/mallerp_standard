<?php
$back_button = block_back_icon(site_url('order/paypalapi/search_paypal_api'));
$head = array(
    lang('key'),
    lang('value'),
);

$data = array();
foreach ($transaction_details as $key=>$transaction_detail)
{
	//echo $key.":".$transaction_detail."<br>";
	$data[] = array(
        $key,
		$transaction_detail, 
    );
}
echo block_header(lang('paypal_default_data').$back_button);
echo $this->block->generate_table($head, $data);
?>
