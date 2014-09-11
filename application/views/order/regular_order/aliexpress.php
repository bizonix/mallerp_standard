<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$html = '';
$url = site_url('order/regular_order/do_upload');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('ali_csv_file_notice'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('add_aliexpress'));
echo block_table($head, $data);

?>


