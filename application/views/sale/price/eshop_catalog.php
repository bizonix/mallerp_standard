<?php

$eshop_cats = array();
$eshop_cats[0] = lang('other_category');
foreach ($categories as $cat)
{
    $eshop_cats[$cat->id] = $cat->category;
}
echo form_dropdown('eshop_category', $eshop_cats, NULL, ' id="eshop_category" ');

?>
