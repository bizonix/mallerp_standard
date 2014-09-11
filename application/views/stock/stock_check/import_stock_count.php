<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$html = '';
$url = site_url('stock/stock_check/do_import_stock_count_upload');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('do_import_stock_count_upload_note'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('import_stock_count'));
echo block_table($head, $data);

?>


