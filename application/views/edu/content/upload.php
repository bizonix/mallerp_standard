<?php

$image_html = '';
$image_url = site_url('edu/content/upload_file');
$config = array(
    'name'  => 'content_id',
    'value' => $content_id,
    'type'  => 'hidden',
);
$hidden = form_input($config);

$config = array(
    'name'        => 'description',
    'id'          => 'description',
    'maxlength'   => '200',
    'size'        => '50',
);

$hidden  .= '<B>'.lang('file_description').' : </B>'.'<br/><br/>'.form_input($config).'<br/><br/>';

$image_html .= '<br/>' . block_form_uploads('upload_file[]', $image_url, $hidden,'upload_file');
$image_html .= '&nbsp;' . block_notice_div(lang('upload_file_notice'));
$image_html .= '<br/>';


$head = array(
    lang('upload_file'),
);
$data = array();
$data[] = array(
    $image_html,
);
echo block_table($head, $data);

?>
