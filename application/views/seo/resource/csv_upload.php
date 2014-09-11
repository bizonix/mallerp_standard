<?php

$html = '';
$url = site_url('seo/resource/do_upload');

$export_url = site_url('seo/resource/download_model');
//echo block_export_button(lang('export_3days_item_no'), $export_url);

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file') . block_export_button(lang('download_model'), $export_url);
$html .= '&nbsp;' . block_notice_div(lang('upload_csv_file_notice'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('resource_csv_upload'));
echo block_table($head, $data);

?>