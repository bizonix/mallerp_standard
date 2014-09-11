<?php
$config['production'] = TRUE;   // toggle to true if going against production
$config['debug'] = FALSE;   // toggle to provide debugging info
$config['version'] = 'international_eub_us_1.1';    // epacket API version
$config['message_id'] = 'mallerp';
$config['pdf_path'] = '/var/www/html/mallerp/static/ems/';
$config['ems_token'] = 'yourtoken';
$config['ems_url'] = 'http://www.ems.com.cn/partner/api/public/p/';
$config['ems_label_url'] = 'http://labels.ems.com.cn/partner/api/public/p/static/label/download';
?>