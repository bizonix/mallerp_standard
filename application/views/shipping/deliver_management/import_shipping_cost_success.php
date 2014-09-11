<?php

$back_button = block_back_icon(site_url('shipping/deliver_management/import_shipping_cost'));

echo block_header(lang('data_loading').$back_button);

foreach ($data as $key=>$value)
{
    echo $key."--------".$value."<br>";
}
echo $back_button;

?>