<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       =>  $provider ? $provider->name : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
    'name'        => 'contact_person',
    'id'          => 'name',
    'value'       =>  $provider ? $provider->contact_person : '',
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
    'value'       =>  $provider ? $provider->address : '',
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
    'value'       =>  $provider ? $provider->boss : '',
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
    'value'       =>  $provider ? $provider->phone : '',
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
    'value'       =>  $provider ? $provider->fax : '',
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
    'value'       =>  $provider ? $provider->email : '',
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
    'value'       =>  $provider ? $provider->qq : '',
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
    'value'       =>  $provider ? $provider->web : '',
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
    'value'       =>  $provider ? $provider->mobile : '',
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
    'value'       =>  $provider ? $provider->open_bank : '',
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
    'value'       =>  $provider ? $provider->bank_account : '',
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
    'value'       =>  $provider ? $provider->bank_title : '',
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
    'value'       =>  $provider ? $provider->remark : '',
    'rows'        => '5',
    'cols'        => '40',
);
$data[] = array(
    lang('remark'),
    form_textarea($config),
);

if($action == 'edit')
{
    $permission = '';
    $permission_array = array();
    if ($permissions)
    {
        foreach ($permissions as $p)
        {
            $permission_array[] = $p->user_id;
        }
    }
    foreach ($purchase_users as $purchase_user)
    {
        $config = array(
            'name'        => 'permissions[]',
            'value'       => $purchase_user->u_id,
            'checked'     => in_array($purchase_user->u_id, $permission_array) ? TRUE : FALSE,
            'style'       => 'margin:10px',
        );
        $permission .= form_checkbox($config) . form_label($purchase_user->u_name);
    }
    $data[] = array(
        $this->block->generate_required_mark(lang('purchase_permission')),
        $permission,
    );
    $title = lang('edit_a_provider');
    $title .= $this->block->generate_back_icon(site_url('purchase/provider/management'));
    $back_button = $this->block->generate_back_icon(site_url('purchase/provider/management'));
}
else
{
    $title = lang('provider_detail');
    $title .= $this->block->generate_back_icon(site_url('purchase/provider/view_list'));
    $back_button = $this->block->generate_back_icon(site_url('purchase/provider/view_list'));
}

$option_users = array();
foreach ($purchase_users as $purchase_user)
{
    $option_users[$purchase_user->u_id] = $purchase_user->u_name;
}

$data[] = array(
    $this->block->generate_required_mark(lang('edit_creator')),
    form_dropdown('edit_user', $option_users, $provider ? $provider->edit_user : ''),
);



echo block_header($title);
$attributes = array(
    'id' => 'edit_provider_form',
);
echo form_open(site_url('purchase/provider/edit_save'),$attributes);
echo $this->block->generate_table($head, $data);
if($action == 'edit')
{
    $url = site_url('purchase/provider/edit_save',array('provider_id' =>$provider ? $provider->id : ''));
    $config = array(
        'name'        => 'submit',
        'value'       => 'Save provider!',
        'type'        => 'button',
        'style'       => 'margin:10px',
        'onclick'     => "this.blur();helper.ajax('$url',$('edit_provider_form').serialize(true), 1);",
    );
    $attributes = array(
         'id'         => $provider ? $provider->id : '',
         'type'       => 'button',
         'style'      => 'margin:10px',

    );
    $url = site_url('purchase/provider/provider_sku_manage', array('provider_id' => $provider ? $provider->id : ''));
    $add_button = anchor($url, lang('manage_sku'),$attributes);
    echo '<h2>'.block_button($config).$add_button.$back_button.'</h2>';

}

echo form_close();
?>
