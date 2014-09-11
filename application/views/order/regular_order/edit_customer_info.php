<?php
$tmpl = array(
    'table_open' => '<table  class="tableborder" cellspacing="1" cellpadding="0" border="0" style="width: 49%; float: left;">',
    'heading_row_start' => '<tr class="heading">',
    'heading_row_end' => '</tr>',
    'heading_cell_start' => '<th>',
    'heading_cell_end' => '</th>',
    'row_start' => '<tr class="td-odd">',
    'row_end' => '</tr>',
    'cell_start' => '<td>',
    'cell_end' => '</td>',
    'row_alt_start' => '<tr class="td">',
    'row_alt_end' => '</tr>',
    'cell_alt_start' => '<td>',
    'cell_alt_end' => '</td>',
    'table_close' => '</table>'
);
$this->table->set_template($tmpl);
$this->table->set_caption(lang('edit_ebay_info'));


$this->table->set_heading(lang('name'), lang('value'));


$this->table->add_row(
        lang('name'),
        form_input(array(
            'name' => 'name',
            'size' => '50',
            'value' => $order->name,
        ))
);
$this->table->add_row(
        lang('address_line_1'),
        form_input(array(
            'name' => 'address_line_1',
            'size' => '30',
            'value' => $order->address_line_1,
        ))
);
$this->table->add_row(
        lang('address_line_2'),
        form_input(array(
            'name' => 'address_line_2',
            'size' => '30',
            'value' => $order->address_line_2,
        ))
);
$this->table->add_row(
        lang('town_city'),
        form_input($config = array(
            'name' => 'town_city',
            'size' => '20',
            'value' => $order->town_city,
        ))
);
$this->table->add_row(
        lang('state_province'),
        form_input(array(
            'name' => 'state_province',
            'size' => '20',
            'value' => $order->state_province,
        ))
);
$this->table->add_row(
        lang('country'),
        form_input(array(
            'name' => 'country',
            'size' => '10',
            'value' => $order->country,
        ))
);

$this->table->add_row(
        lang('zip_code'),
        form_input(array(
            'name' => 'zip_code',
            'size' => '10',
            'value' => $order->zip_code,
        ))
);


$params = "$('customer_form').serialize()";
$url = site_url('order/regular_order/proccess_edit_customer_info');
$config = array(
    'name' => 'submit',
    'value' => lang('save'),
    'type' => 'button',
    'style' => 'margin:10px;padding:5px;',
    'onclick' => "this.blur();helper.ajax('$url', $params, 1);",
);
$save_button = block_button($config);
$back_button = ''; //block_back_icon(site_url('order/regular_order/confirm_order'));

echo block_header(lang('edit_customer_info') . "( $order->item_no )" . $back_button);

$attributes = array('id' => 'customer_form');
echo form_open('', $attributes);
//-- Display Table
$table = $this->table->generate();
$this->table->clear();




$this->table->set_caption(lang('view_paypal_info'));


$this->table->set_heading(lang('name'), lang('value'));

if ($paypal) {
    $this->table->add_row(
            lang('name'),
            form_input(array(
                'size' => '50',
                'value' => $paypal->buyer_name,
                'readonly' => 'readonly',
            ))
    );
    $this->table->add_row(
            lang('address_line_1'),
            form_input(array(
                'size' => '30',
                'value' => $paypal->street1,
                'readonly' => 'readonly',
            ))
    );
    $this->table->add_row(
            lang('address_line_2'),
            form_input(array(
                'size' => '30',
                'value' => $paypal->street2,
                'readonly' => 'readonly',
            ))
    );
    $this->table->add_row(
            lang('town_city'),
            form_input($config = array(
                'size' => '20',
                'value' => $paypal->city,
                'readonly' => 'readonly',
            ))
    );
    $this->table->add_row(
            lang('state_province'),
            form_input(array(
                'size' => '20',
                'value' => $paypal->province,
                'readonly' => 'readonly',
                
            ))
    );
    $this->table->add_row(
            lang('country'),
            form_input(array(
                'size' => '10',
                'value' => $paypal->country,
                'readonly' => 'readonly',
            ))
    );

    $this->table->add_row(
            lang('zip_code'),
            form_input(array(
                'size' => '10',
                'value' => $paypal->postal_code,
                'readonly' => 'readonly',
            ))
    );
    $table_another = $this->table->generate();
} else {
    $table_another = br() . br() . br() . br() . br() . br() . br() . br() . br() . br() . br();
}

echo $table . '<p style="float:left"></p>' . $table_another;

$config = array(
    'name' => 'order_id',
    'value' => $order->id,
    'type' => 'hidden',
);
echo form_input($config);
echo '<div style="float:left;">' . $save_button . '</div>';
echo form_close();
echo $back_button;
echo '<div style="height:200px;"></div>';
?>
