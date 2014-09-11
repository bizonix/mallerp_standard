<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    $provider->name,
);

$data[] = array(
    $this->block->generate_required_mark(lang('boss')),
    $provider->boss,
);

$data[] = array(
    $this->block->generate_required_mark(lang('contact_person')),
    $provider->contact_person,
);

$data[] = array(
    $this->block->generate_required_mark(lang('address')),
    $provider->address,
);

$data[] = array(
    lang('phone'),
    $provider->phone,
);

$data[] = array(
   lang('fax'),
    $provider->fax,
);

$data[] = array(
    lang('email'),
    $provider->email,
);

$data[] = array(
   lang('qq'),
    $provider->qq,
);
$data[] = array(
    lang('web'),
    $provider->web,
);

$data[] = array(
   lang('mobile'),
    $provider->mobile,
);

$data[] = array(
   lang('open_bank'),
    $provider->open_bank,
);

$data[] = array(
   lang('bank_account'),
    $provider->bank_account,
);

$data[] = array(
   lang('bank_title'),
    $provider->bank_title,
);

$data[] = array(
   lang('remark'),
    $provider->remark,
);

$data[] = array(
   lang('edit_user'),
    $provider->edit_user,
);

$data[] = array(
   lang('edit_date'),
    $provider->edit_date,
);

$back_button = $this->block->generate_back_icon('purchase/provider/view_list');
$title = lang('provider_detail'). $back_button;
echo block_header($title);
echo $this->block->generate_table($head, $data);
echo '<h2>'.$back_button.'</h2>';

?>
