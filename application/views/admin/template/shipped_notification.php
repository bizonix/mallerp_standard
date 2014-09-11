<?php

echo block_header(lang('shipped_notification_template'));

$data = array(
    'buyer_name'        => 'lion',
    'item_no'           => 'No-5',
    'shipped_date'      => '2012',
    'item_list_entries' => array(
        array(
            'item_name' => 'toy',
            'sku'       => 'A23',
            'qty'       => 34,
        ),
        array(
            'item_name' => 'toy2',
            'sku'       => 'A232',
            'qty'       => 342,
        ),
    ),
    'weight'            => 23.43,
    'shipping_address'  => 'USA<BR/>NY',
    'track_number'      => 'LXDDFDE',
    'shipping_method'   => 'DHL',
    'track_url'         => 'www.mallerp.com',
    'email'             => 'john@mallerp.com',
    'sender_name'       => 'john',
    'usd'               => 99,
);
$view = 'local/english/template/email/order_shipped_notification';
$content = $this->parser->parse($view, $data, TRUE);
echo block_clickable_fieldset(lang('view_template'), $content, 'view');
echo br(), br();

$config = array(
    'name'        => 'edit_template',
    'id'          => 'edit_template',
    'value'       => file_get_contents($shipped_notification_filename),
    'cols'        => '100',
    'rows'        => '25',
);
$textarea = form_textarea($config);

$url = site_url('admin/template/edit_shipped_notification');
$config = array(
    'name'      => 'save_template',
    'id'        => 'save_template',
    'value'     => lang('save_template'),
    'onclick'   => "return save_shipped_notification_template('$url');",
);
$button = block_button($config);

$content = $textarea . br() . $button;

echo block_clickable_fieldset(lang('edit_template'), $content, 'edit');

?>
