<?php

$add_button = $this->block->generate_add_icon('order/setting/add_stmp_sender');
$head = array(
    lang('paypal_email'),
    lang('sender_name'),
    lang('stmp_account'),
    lang('options') . $add_button,
);

$data = array();

$sender_update_url = site_url('order/setting/update_stmp_sender');
$data = array();
foreach ($paypal_senders as $sender)
{
    $sender_id = $sender->id;

    $drop_button = block_drop_icon(
        'order/setting/drop_stmp_sender',
        "{id: $sender_id}",
        TRUE
    );
    $sender_accounts = $this->stmp_model->fetch_paypal_sender_accounts($sender_id);
    $account_html = '';
    foreach ($sender_accounts as $account)
    {
        $account_html .= $account->stmp_host . ': ' . $account->stmp_account . block_status_image($account->account_status) . '<br/>';
    }
    $edit_button = block_edit_link(site_url('order/setting/update_stmp_sender_accounts', array($sender_id)));
    $account_html .= $edit_button;
    $data[] = array(
        $this->block->generate_div("paypal_email_$sender_id", $sender->paypal_email),
        $this->block->generate_div("sender_name_$sender_id", $sender->sender_name),
        $account_html,
        $drop_button,
    );
    echo block_editor(
        "paypal_email_$sender_id",
        'sender_form',
        $sender_update_url,
        "{id: $sender_id, type: 'paypal_email'}"
    );
    echo block_editor(
        "sender_name_$sender_id",
        'sender_form',
        $sender_update_url,
        "{id: $sender_id, type: 'sender_name'}"
    );
}

echo block_header(lang('notification_email_account_setting'));
echo block_table($head, $data);


?>
