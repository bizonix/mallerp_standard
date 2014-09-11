
<?php

$head = array(
    lang('name'),
    lang('receivers'),
);

$data = array();

foreach ($all_messages as $key => $value)
{
    $edit_button = $this->block->generate_edit_link(site_url('admin/message/edit_receiver', array($key)));
    $receivers_text = '';
    $receivers = $value['receivers'];
    foreach ($receivers as $receiver)
    {
        $receivers_text .= $receiver->u_name . '&nbsp;&nbsp;';
    }
    $str = $receivers_text . $edit_button;
    $data[] = array(
        lang($value['message']),
        $str,
    );
}
echo block_header(lang('massage_management'));
echo $this->block->generate_table($head, $data);
?>