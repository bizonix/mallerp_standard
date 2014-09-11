<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
    'name'        => 'contact_person',
    'id'          => 'contact_person',
    'maxlength'   => '255',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('contact_person')),
    form_input($config),
);

$config = array(
    'name'        => 'address',
    'id'          => 'address',
    'maxlength'   => '255',
    'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('address')),
    form_input($config),
);

$config = array(
    'name'        => 'boss',
    'id'          => 'boss',
    'maxlength'   => '100',
    'size'        => '20',
);
$data[] = array(
    lang('boss'),
    form_input($config),
);

$config = array(
    'name'        => 'phone',
    'id'          => 'phone',
    'maxlength'   => '255',
    'size'        => '20',
);
$data[] = array(
    lang('phone'),
    form_input($config).lang('phone_example'),
);

$config = array(
    'name'        => 'fax',
    'id'          => 'fax',
    'maxlength'   => '100',
    'size'        => '30',
);
$data[] = array(
    lang('fax'),
    form_input($config),
);

$config = array(
    'name'        => 'email',
    'id'          => 'email',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('email'),
    form_input($config),
);

$config = array(
    'name'        => 'qq',
    'id'          => 'qq',
    'maxlength'   => '100',
    'size'        => '30',
);
$data[] = array(
    lang('qq'),
    form_input($config),
);

$config = array(
    'name'        => 'web',
    'id'          => 'web',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
     lang('web'),
     form_input($config),
);

$config = array(
    'name'        => 'mobile',
    'id'          => 'mobile',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('mobile'),
    form_input($config),
);

$config = array(
    'name'        => 'open_bank',
    'id'          => 'open_bank',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('open_bank'),
    form_input($config),
);

$config = array(
    'name'        => 'bank_account',
    'id'          => 'bank_account',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('bank_account'),
    form_input($config),
);

$config = array(
    'name'        => 'bank_title',
    'id'          => 'bank_title',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('bank_title'),
    form_input($config),
);

$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'rows'        => '5',
    'cols'        => '40',
);
$data[] = array(
    lang('remark'),
    form_textarea($config),
);

$permission = '';
foreach ($purchase_users as $purchase_user)
{
    $config = array(
        'name'        => 'permissions[]',
        'value'       => $purchase_user->u_id,
        'style'       => 'margin:10px',
    );
    $permission .= form_checkbox($config) . form_label($purchase_user->u_name);
}
$data[] = array(
    $this->block->generate_required_mark(lang('purchase_permission')),
    block_check_group('permissions[]', $permission),
);
$title = lang('add_a_new_provider');

$back_button = $this->block->generate_back_icon(site_url('purchase/provider/management'));

if($popup)
{
    $back_button = NULL;
}

$title .= $back_button ;

echo block_header($title);
$attributes = array(
    'id' => 'add_provider_form',
);
echo form_open(site_url('purchase/provider/add_save'),$attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('purchase/provider/add_save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('add_provider_form').serialize(true), 1);",
);

if($popup)
{
     $config['onclick'] = "save('$input_id','$url');";
}

echo '<h2>'.block_button($config).$back_button.'</h2>';
echo form_close();
?>