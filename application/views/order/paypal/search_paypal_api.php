<?php

$title = lang('paypal_default_data');

echo block_header($title);
$url = site_url('order/paypalapi/transaction_details');
echo '<center>';
echo '<br/>';
echo form_open($url);

$config = array(
    'name'        => 'transaction_id',
    'id'          => 'transaction_id',
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

echo nbs() . nbs();

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
