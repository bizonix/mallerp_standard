<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$html = '';
$url = site_url('shipping/deliver_management/import_shipping_cost_by_order_id');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
//$html .= '&nbsp;' . block_notice_div(lang('import_shipping_cost_note'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    lang('import_shipping_cost_by_order_id').":".$html,
);

$html = '';
$url = site_url('shipping/deliver_management/import_shipping_cost_by_track_number');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('import_shipping_cost_note'));
$html .= '<br/>';
$data[] = array(
    lang('import_shipping_cost_by_track_number').":".$html,
);

echo $error;
echo block_header(lang('import_shipping_cost'));
echo block_table($head, $data);

?>


