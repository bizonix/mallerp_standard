<?php
$config_copy = array(
    'name'        => 'item_no',
    'id'          => 'item_no',
    'maxlength'   => '50',
    'size'        => '50',
);

$back_button = $this->block->generate_back_icon(site_url('order/regular_order/manage'));
$title = lang('add_order');

echo block_header($title);

$url = site_url('order/regular_order/add');
echo '1. <a href='.$url.'>'.lang('add_new_order').'</a><br/><br/>';

$attributes = array(
    'id' => 'order_form',
);
echo form_open(site_url('order/regular_order/copy'), $attributes);
$config = array(
    'name'        => 'submit',
    'value'       => lang('copy_order'),
    'type'        => 'submit',
    'style'       => 'margin:10px',
);

if($info)
{
    $clue = '<strong style="color:red">'.lang($info).'</strong>';
}
else
{
    $clue = lang('clue_to_item_no');
}

echo '2. '.form_input($config_copy).form_input($config).$clue;
echo form_close();

?>


