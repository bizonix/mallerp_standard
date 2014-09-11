<?php
$CI = & get_instance();
$head = array(
    lang('operator'),
    lang('stock_code'),
	lang('is_register'),
	lang('edited_date'),
    lang('options'),
);

$data = array();
$code_url = site_url('order/power_manage/verigy_exchange_power_management');
foreach ($epacket_configs as $epacket_config)
{
    $drop_button = $this->block->generate_drop_icon(
        'shipping/epacket_config/drop_config_by_id',
        "{id: $epacket_config->id}",
        TRUE
    );
	$edit_button = $this->block->generate_edit_link(site_url('shipping/epacket_config/add_edit', array($epacket_config->id)));
	$user_info = $CI->user_model->fetch_user_by_id($epacket_config->user_id);
    $data[] = array(
        $user_info->name,
        $epacket_config->stock_code,
		$epacket_config->is_register,
		$epacket_config->update_date,
        $drop_button.$edit_button,
    );
}
$title = lang('management_epacket_config');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
