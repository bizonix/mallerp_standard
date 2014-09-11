<?php
$contact = '/var/www/html/mallerp/static/contract/';
$path = $contact . $item_no . '.html';
echo file_get_contents($path);

?>