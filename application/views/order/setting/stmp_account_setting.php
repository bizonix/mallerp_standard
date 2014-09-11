<?php

$add_button = $this->block->generate_add_icon('order/setting/add_stmp_account');
$head = array(
    lang('account_name'),
    lang('account_password'),
    lang('stmp_host'),
    lang('status'),
    '<div>' . lang('options') . $add_button . '</div>',
);

$account_update_url = site_url('order/setting/update_stmp_account');
$account_collection = object_to_js_array($hosts, 'id', 'host');
$data = array();
foreach ($accounts as $account)
{
    $account_id = $account->id;

    $drop_button = block_drop_icon(
        'order/setting/drop_stmp_accout',
        "{id: $account_id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("stmp_account_name_$account_id", $account->account_name),
        $this->block->generate_div("stmp_account_password_$account_id", $account->account_password),
        $this->block->generate_div("stmp_account_host_$account_id", $account->stmp_host),
        '<center>' . block_status_image($account->status) . '</center>',
        $drop_button,
    );
    echo $this->block->generate_editor(
        "stmp_account_name_$account_id",
        'account_form',
        $account_update_url,
        "{id: $account_id, type: 'account_name'}"
    );
    echo $this->block->generate_editor(
        "stmp_account_password_$account_id",
        'account_form',
        $account_update_url,
        "{id: $account_id, type: 'account_password'}"
    );
    echo $this->block->generate_editor(
        "stmp_account_host_$account_id",
        'account_form',
        $account_update_url,
        "{id: $account_id, type: 'stmp_host'}",
        $account_collection
    );
}

echo block_header(lang('stmp_accout_setting'));
echo block_table($head, $data);

?>
