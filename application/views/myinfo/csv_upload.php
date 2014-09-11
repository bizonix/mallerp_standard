<?php

$html = '';
$url = site_url('myinfo/myaccount/do_upload');

$html .= '<br/>' . block_form_uploads('userfile', $url, NULL,'upload_csv_file');
$html .= '&nbsp;' . block_notice_div(lang('upload_reward_csv_file_notice'));
$html .= '<br/>';

$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $html,
);

echo $error;
echo block_header(lang('work_reward_csv_upload'));
echo block_table($head, $data);

?>