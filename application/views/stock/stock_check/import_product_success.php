<?php
$CI = & get_instance();
$back_button = block_back_icon(site_url('stock/stock_check/import_stock_count'));

echo block_header(lang('data_loading').$back_button);

foreach ($data as $sku=>$value)
{
 echo $sku.'-----'.$value.'<br>';
}
echo $back_button;

?>