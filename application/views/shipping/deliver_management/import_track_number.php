<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$html = '';
$url = site_url('shipping/deliver_management/do_upload');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('import_track_number_notice'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('import_track_number'));
echo block_table($head, $data);

?>


