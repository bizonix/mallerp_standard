<?php

$title = lang('set_product_purchaser');

echo block_header($title);
$url = site_url('pi/product/save_set_product_purchaser');
echo '<center>';
echo '<br/>';
$attributes = array(
    'id' => 'set_product_purchaser',
);
echo form_open($url,$attributes);

$config = array(
    'name'        => 'sku',
    'id'          => 'sku',
    'value'       => '',
    'maxlength'   => '1500',
    'size'        => '90',
);
echo lang('sku') . ' ' . form_input($config) ;
echo br() . br();
$options = array();
$options[-1] = lang('please_select');
foreach ($all_users as $purchase_user)
{
    $options[$purchase_user->id] = $purchase_user->name;
}
echo lang('purchaser') . form_dropdown('purchaser_id', $options, '-1');
echo br() . br();
echo lang('product_develper') . form_dropdown('product_develper_id', $options, '-1');echo br() . br();
$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'button',
	//'type' => 'submit',
	'onclick'=> "this.blur();helper.ajax('$url',$('set_product_purchaser').serialize(true), 1);",
);
echo block_button($config);
echo form_close();
echo '</center>';
$note = lang('note') . ': ' . '<br/>' .
    lang('give_product_note');
echo block_notice_div($note);

?>
