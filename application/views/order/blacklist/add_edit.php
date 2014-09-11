<?php
$head = array(
    lang('name'),
    lang('value'),
);

$options = array(
    'ebay'  =>'ebay',
    'mallerp.com'=>'mallerp.com',
    'DHgate'=>'DHgate',
    'alibaba' =>'alibaba',
    'domestic'=>lang('domestic'),
    'Other'=>'Other',
);

$data[] = array(
    $this->block->generate_required_mark(lang('platform')),
     form_dropdown('platform', $options, 'ebay'),
);

$config = array(
      'name'        => 'buyer_id',
      'id'          => 'buyer_id',
      'value'       => $blacklist ? $blacklist->buyer_id : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    lang('buyer_id'),
    form_input($config).lang('buyer_id_or_email'),
);

$config = array(
      'name'        => 'email',
      'id'          => 'email',
      'value'       => $blacklist ? $blacklist->email : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    lang('email'),
    form_input($config).lang('email_example'),
);

$config = array(
      'name'        => 'name',
      'id'          => 'name',
      'value'       => $blacklist ? $blacklist->b_name : '',
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    lang('name'),
    form_input($config),
);

//echo $this->block->generate_ac('country', array('country_code', 'name_cn'));

$config = array(
      'name'        => 'remark',
      'id'          => 'remark',
      'value'       => $blacklist ? $blacklist->remark : '',
      'cols'        => '50',
      'rows'        => '3',
);
$data[] = array(
    lang('remark'),
     form_textarea($config).lang('blacklist_information'),
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