<?php
$head = array(
    lang('apply_name'),
    lang('apply_value'),
);

$config = array(
    'name'        => 'product_name',
    'id'          => 'product_name',
    'value'       => $apply ? $apply->product_name : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('product_name')),
    form_input($config),
);

$config = array(
    'name'        => 'product_image_url',
    'id'          => 'product_image_url',
    'value'       => $apply ? $apply->product_image_url : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('product_image_url'),
    $this->block->generate_image_input($config),
);

$config = array(
    'name'        => 'product_description',
    'id'          => 'product_description',
    'value'       => $apply ? $apply->product_description : '',
    'maxlength'   => '80',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('product_description')),
    form_textarea($config),
);

echo $this->block->generate_tinymce(array('product_description'));

$title = lang('edit_purchase_apply');
$back_button = $this->block->generate_back_icon(site_url('pi/purchase_apply/manage'));

$title .= $back_button ;

echo block_header($title);
$attributes = array(
    'id' => 'purchase_apply_form',
);
echo form_open(site_url('pi/product/add_edit'),$attributes);
echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('edit_purchase_apply'),
    'type'        => 'submit',
    'style'       => 'margin:10px',
);

if($action === 'edit')
{
    echo '<h2>'.form_input($config).$back_button.'</h2>';
}
echo form_hidden('apply_tag', '1' );
echo form_hidden('apply_id', $apply->id );
echo form_close();
?>