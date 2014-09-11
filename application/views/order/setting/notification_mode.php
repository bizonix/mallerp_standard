<?php

echo block_header(lang('notification_mode_setting'));

$options = array(
    '1'     => lang('normal_mode'),
    '0'     => lang('dev_mode'),
);

$select_mode = '';
$select_mode_html =  "onchange     = 'toggle_email(this.value);' id='select_mode'";
$select_mode = form_label(lang('select_mode') . ': ') .
                form_dropdown('select_mode', $options, $notification_mode, $select_mode_html);

$config = array(
    'name'        => 'dev_email',
    'id'          => 'dev_email',
    'value'       => $notification_dev_mode_email,
    'maxlength'   => '50',
    'size'        => '50',
);

$dev_mode = form_label(lang('receive_email') . ': ') . form_input($config);
$disply_none = "";
if ($notification_mode == 1)
{
    $disply_none = "display:none;";
}
$select_mode .= block_div('dev_mode', $dev_mode, 'style="padding-top: 10px;' . $disply_none . '"');

$url = site_url('order/setting/proccess_update_notification_mode');
$params = "{select_mode: $('select_mode').value, dev_email: $('dev_email').value}";
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px;padding:5px;float:right;',
    'onclick'     => "this.blur();helper.ajax('$url', $params, 1);",
);
$select_mode .= block_button($config);

$mode = block_fieldset(lang('mode'), $select_mode);

echo form_open();
echo block_div('mode', $mode);
echo form_close();

?>

<script>
    function toggle_email(val)
    {
        if (val == 1)
        {
            $('dev_mode').hide();
        }
        else
        {
            $('dev_mode').show();
        }
    }
</script>