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
    'name'        => 'reference_links',
    'id'          => 'reference_links',
	'value'       => $apply ? $apply->reference_links : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('reference_links'),
    form_input($config),
);
$config = array(
    'name'        => 'sales_strategy',
    'id'          => 'sales_strategy',
	'value'       => $apply ? $apply->sales_strategy : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('sales_strategy'),
    form_input($config),
);
$config = array(
    'name'        => 'sales_statistics',
    'id'          => 'sales_statistics',
	'value'       => $apply ? $apply->sales_statistics : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('sales_statistics'),
    form_input($config),
);
$config = array(
    'name'        => 'related_specifications',
    'id'          => 'related_specifications',
	'value'       => $apply ? $apply->related_specifications : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('related_specifications'),
    form_input($config),
);
$config = array(
    'name'        => 'provider',
    'id'          => 'provider',
	'value'       => $apply ? $apply->provider : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('provider'),
    form_input($config),
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

$purchasers = $this->purchase_model->purchase_apply_purchasers();
foreach ($purchasers  as $purchaser)
{
   $options[$purchaser->u_id] = $purchaser->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('purchaser')),
    form_dropdown('purchaser_id', $options,$apply ? $apply->purchaser_id : ''),
);
if($action === 'edit' )
{
    $title = lang('edit_purchase_apply');
    $back_button = $this->block->generate_back_icon(site_url('order/purchase_apply/manage'));
}
else
{
    $title = lang('view_purchase_apply');
    $back_button = $this->block->generate_back_icon(site_url('order/purchase_apply/view_list'));
}

$title .= $back_button ;

echo block_header($title);
$attributes = array(
    'id' => 'purchase_apply_form',
);
echo form_open(site_url('order/purchase_apply/save'),$attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('order/purchase_apply/save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('purchase_apply_saved'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_content_by_purchase_apply(this, '$url');",
);

if($action === 'edit')
{
    echo '<h2>'.block_button($config).$back_button.'</h2>';
}
else
{
    echo '<h2>'.$back_button.'</h2>';
}

echo form_hidden('apply_id', $apply->id );
echo form_close();

?>