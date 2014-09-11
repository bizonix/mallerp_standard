<?php

$head = array(
    lang('login_name'),
    lang('username'),
    lang('paypal_email_setting'),
    lang('ebay_id_setting'),
);

$data = array();
$url = site_url('order/setting/update_ebay_info');

foreach ($users as $user)
{
    $user_id = $user->u_id;
    $ebay_info = $this->user_model->fetch_user_ebay_info($user_id);
    $data[] = array(
        $user->login_name,
        $user->u_name,
        $this->block->generate_div("paypal_email_{$user_id}", empty($ebay_info->paypal_email_str) ? '[edit]' : $ebay_info->paypal_email_str),
        $this->block->generate_div("ebay_id_{$user_id}", empty($ebay_info->ebay_id_str) ? '[edit]' : $ebay_info->ebay_id_str),
    );
    echo $this->block->generate_editor(
        "paypal_email_{$user_id}",
        'user_form',
        $url,
        "{user_id: $user_id, type: 'paypal_email'}"
    );
    echo $this->block->generate_editor(
        "ebay_id_{$user_id}",
        'user_form',
        $url,
        "{user_id: $user_id, type: 'ebay_id'}"
    );
}

echo block_header(lang('paypal_email_ebay_id_setting'));
echo $this->block->generate_table($head, $data);

?>
