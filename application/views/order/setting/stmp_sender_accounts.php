
<?php

$head = array(
    lang('paypal_email'),
    lang('stmp_account'),
);

$data = array();

$str = '';
$url = site_url('order/setting/proccess_update_stmp_sender_accounts');
foreach ($accounts as $account)
{
    $account_id = $account->id;
    $checkbox = array(
        'name'        => 'account_' . $account_id,
        'id'          => 'account_' . $account_id,
        'value'       => $account_id,
        'checked'     => in_array($account_id, $account_ids) ? TRUE : FALSE,
        'onclick'     => "helper.ajax('$url', {sender_id: $sender->id, account_id: $account_id, checked: this.checked}, 1)",
    );
    $str .= form_checkbox($checkbox) . form_label($account->stmp_host) . ': ' . $account->account_name . block_status_image($account->status) . '<br/><br/>';
}

$data[] = array(
    $sender->paypal_email . ' ( ' . $sender->sender_name . ' )',
    $str,
);

$back_button = $this->block->generate_back_icon(site_url('order/setting/notification_email_account'));
$title = lang('stmp_sender_accounts_setting'). $back_button;
echo block_header($title);
echo $this->block->generate_table($head, $data);
echo $back_button;

?>