<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$html = '';
$url = site_url('pi/product/do_import_product_stock_upload');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('batch_import_product_shelf_code_note'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('batch_import_product_shelf_code'));
echo block_table($head, $data);

?>


