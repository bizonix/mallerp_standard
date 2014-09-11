<?php

$back_button = block_back_icon(site_url('seo/resource/csv_upload'));

echo block_header(lang('data_loading').$back_button);

foreach ($data as $value)
{
    echo $value;
}
echo $back_button;

?>