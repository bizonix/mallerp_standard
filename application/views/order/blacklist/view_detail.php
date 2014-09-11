<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
      'name'        => 'name',
      'id'          => 'name',
      'value'       => $blacklist ? $blacklist->name : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
      'name'        => 'cause',
      'id'          => 'cause',
      'value'       => $blacklist ? $blacklist->cause : '',
      'cols'        => '50',
      'rows'        => '3',
);
$data[] = array(
    $this->block->generate_required_mark(lang('cause')),
     form_textarea($config),
);

$config = array(
      'name'        => 'email',
      'id'          => 'email',
      'value'       => $blacklist ? $blacklist->email : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('email')),
    form_input($config).lang('email_example'),
);

$config = array(
      'name'        => 'phone',
      'id'          => 'phone',
      'value'       => $blacklist ? $blacklist->phone : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    lang('phone'),
    form_input($config).lang('phone_example'),
);

$config = array(
      'name'        => 'address',
      'id'          => 'address',
      'value'       => $blacklist ? $blacklist->address : '',
      'maxlength'   => '150',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('address')),
    form_input($config),
);

$config = array(
      'name'        => 'name',
      'id'          => 'name',
      'value'       => $blacklist ? $blacklist->u_name : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
      'name'        => 'remark',
      'id'          => 'remark',
      'value'       => $blacklist ? $blacklist->remark : '',
      'cols'        => '50',
      'rows'        => '3',
);
$data[] = array(
    lang('remark'),
     form_textarea($config),
);



if ($blacklist)
{
    $title = lang('customer_black_list_edit');
}
else
{
    $title = lang('customer_black_list_add');
}
$back_button = $this->block->generate_back_icon(site_url('order/blacklist/manage'));

$title .= $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'blacklist_form',
);


$url = site_url('order/blacklist/save_blacklist');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('save_blacklist'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('blacklist_form').serialize(true), 1);",
);

echo form_hidden('blacklist_id', $blacklist ? $blacklist->id : '-1');
echo '<h2>'.block_button($config).$back_button.'</h2>';
echo form_close();

?>