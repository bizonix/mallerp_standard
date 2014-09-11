<?php
$head = array(
    lang('name'),
    lang('value'),
);


$config = array(
    'name'        => 'sku',
    'id'          => 'sku',
    'maxlength'   => '20',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('sku')),
    form_input($config),
);

$config = array(
    'name'        => 'name_cn',
    'id'          => 'name_cn',
    'maxlength'   => '100',
    'size'        => '100',
);

if($apply_tag == '1')
{
    $config['value']        = $language == 'chinese' ? $product_name : '' ;
}

$data[] = array(
    $this->block->generate_required_mark(lang('chinese_name')),
    form_input($config),
);

$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'maxlength'   => '100',
    'size'        => '100',
);

if($apply_tag == '1')
{
    $config['value']        = $language == 'english' ? $product_name : '';
}

$data[] = array(
    $this->block->generate_required_mark(lang('english_name')),
    form_input($config),
);

$str = form_dropdown('parent', $catalogs);

$data[] = array(
    $this->block->generate_required_mark(lang('product_catalog')),
    $str,
);


if($apply_tag == '1')
{
    $config = array(
        'name'        => 'product_image_url',
        'id'          => 'product_image_url',
        'value'       => $product_image_url,
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
        'value'       => $product_description ,
        'maxlength'   => '80',
        'size'        => '20',
    );
    $data[] = array(
        lang('product_description'),
        form_textarea($config),
    );

    echo $this->block->generate_tinymce(array('product_description'));
}

$title =lang('add_a_new_product') . $this->block->generate_back_icon(site_url('pi/product/manage'));
$back_button = $this->block->generate_back_icon(site_url('pi/product/manage'));
echo block_header($title);
$attributes = array(
    'id' => 'product_form',
);
echo form_open(site_url('pi/product/save_new'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('pi/product/save_new');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_product'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('product_form').serialize(true), 1);",
);

if($apply_tag == '1')
{
    echo form_hidden('apply_tag', $apply_tag ? $apply_tag : '0');
    echo form_hidden('apply_id', $apply_id);
}

echo form_hidden('product_id', '-1');
echo '<h2>'.block_button($config).$back_button.'</h2>';
echo form_close();

?>
