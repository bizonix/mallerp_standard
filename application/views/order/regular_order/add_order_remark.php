<?php
$title = lang('add_order_remark').'-----'.$order->item_no;
echo block_header($title);
$url = site_url('order/regular_order/save_order_remark');
echo '<center>';
echo '<br/>';
$attributes = array(
    'id' => 'remark_form',
);
echo form_open($url, $attributes);
$config = array(
    'name'        => 'remark_content',
    'id'          => 'remark_content',
    'value'       => '',
	'cols'        => '35',
    'rows'        => '6',

);
echo lang('note') . ' ' . form_textarea($config) ;
echo form_hidden('order_id', $order->id);
echo br();
$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('remark_form').serialize(true), 1);",
);
echo block_button($config);
echo form_close();

echo br();
echo '</center>';
echo br();

?>
