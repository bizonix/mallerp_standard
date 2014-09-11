<?php

$title = lang('paypal_refund_transaction');

echo block_header($title);
$url = site_url('order/paypalapi/RefundReceipt');
echo '<center>';
echo '<br/>';
echo form_open($url);

$config = array(
    'name'        => 'transactionID',
    'id'          => 'transactionID',
    'value'       => '',
    'maxlength'   => '20',
    'size'        => '20',
);
echo lang('transaction_id') . ' ' . form_input($config) ;
echo nbs() . nbs();

$intut_users=array();
$input_user_login=$this->paypal_model->fetch_all_input_user_login();
foreach($input_user_login as $input_user)
{
	$intut_users[$input_user->user]=$input_user->paypal;
}
echo form_dropdown('intut_user', $intut_users, '');

echo br();
$refundtypes = array(
    'Full' => 'Full',
    'Partial' => 'Partial',
);
$refundtype='Partial';
echo lang('refund_verify_type') . ' ' . form_dropdown('refundType', $refundtypes, $refundtype);

echo br();
$config = array(
    'name'        => 'amount',
    'id'          => 'amount',
    'value'       => '0.00',
    'maxlength'   => '6',
    'size'        => '10',
);
echo lang('return_cost') . ' ' . form_input($config) ;
$currencys = array(
    'USD' => 'USD',
    'GBP' => 'GBP',
	'EUR' => 'EUR',
	'JPY' => 'JPY',
	'CAD' => 'CAD',
	'AUD' => 'AUD',
);
$currency='USD';
$cur_list = array('USD', 'GBP', 'EUR', 'JPY', 'CAD', 'AUD');
echo lang('currency_code') . ' ' . form_dropdown('currency', $currencys, $currency);
echo br();
echo br();
$config = array(
    'name'        => 'memo',
    'id'          => 'memo',
    'value'       => ' ',
	'cols'        => '35',
    'rows'        => '6',

);
echo lang('note') . ' ' . form_textarea($config) ;

echo br();
$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'submit',
);
echo block_button($config);
echo form_close();

echo br();
echo '</center>';
echo br();

?>
