<?php

$title = lang('set_catalog_sale_permission');

echo block_header($title);
$url = site_url('pi/catalog/save_set_catalog_sale_permission');
echo '<center>';
echo '<br/>';
$attributes = array(
    'id' => 'catalog_sale_permission',
);
echo form_open($url,$attributes);

$config = array(
    'name'        => 'catalog_id',
    'id'          => 'catalog_id',
    'value'       => '',
    'maxlength'   => '1500',
    'size'        => '90',
);
echo lang('catalog_id') . ' ' . form_input($config) ;
echo br() . br();

$config = array(
    'name'        => 'saler_id',
    'id'          => 'saler_id',
    'value'       => '',
    'maxlength'   => '1500',
    'size'        => '90',
);
echo lang('saler_id') . ' ' . form_input($config) ;
echo br() . br();
$options = array(
    '1' => lang('revocation'),
    '0' => lang('authorize'),
);
echo lang('options').form_dropdown('action_id', $options, 0);
echo br() . br();
$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'button',
	//'type' => 'submit',
	'onclick'=> "this.blur();helper.ajax('$url',$('catalog_sale_permission').serialize(true), 1);",
);
echo block_button($config);
echo form_close();
echo '</center>';
$note = lang('note') . ': ' . '<br/>' .
    lang('give_product_note');
echo block_notice_div($note);

?>
