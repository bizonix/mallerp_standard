<?php
$sale_users = $this->user_model->fetch_users_by_system_code('sale');
$salers_options = array();
foreach($sale_users as $sale_user)
{
    $salers_options[$sale_user->u_id] = $sale_user->u_name;
}
$collection = to_js_array($salers_options);

$input_user_options = $paypal_emails;
$input_user_options['remove_input_user'] = lang('remove_paypal_email');
$input_user_collection = to_js_array($input_user_options);

$yes = lang('yes');
$no = lang('no');
$in_operation_collection = to_js_array(array('1' => $yes, '0' => $no));

$url = site_url('sale/sale_order/add_saler_input_user');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('saler'),
    lang('paypal_email'),
    lang('in_operation'),
    lang('options') . $add_button,
);

$data = array();
$saler_url = site_url('sale/sale_order/verify_saler_input_user');
foreach ($salers as $saler)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/sale_order/drop_saler_input_user',
        "{id: $saler->saler_id}",
        TRUE
    );
    $more_button = block_more_icon(
        'sale/sale_order/saler_add_input_user',
        "{saler_id: {$saler->saler_id}}"
    );
    $input_user_url = site_url('sale/sale_order/verify_input_user');
    $input_users = $this->sale_order_model->saler_fetch_input_users($saler->saler_id);   
    $input_users_str = '';
    foreach($input_users as $inputuser)
    {
        if ('void'== $inputuser->paypal_email)
        {
            $input_user = '[edit]';
        }
        else
        {
            $input_user = $inputuser->paypal_email;
        }
        $input_users_str .= $this->block->generate_div("input_user_{$inputuser->id}" , $input_user) . '<br>';
        echo $this->block->generate_editor(
            "input_user_{$inputuser->id}",
            'sale_order_form',
            $input_user_url,
            "{id: $inputuser->id, type: 'paypal_email'}",
            "$input_user_collection"
        );
    }
    $input_users_str .=  $more_button;
    $data[] = array(
        $this->block->generate_div("saler_{$saler->saler_id}", isset($saler) && $saler->saler_id!= '0' ?  fetch_user_name_by_id($saler->saler_id) : '[edit]'),
        $input_users_str,
        block_div("saler_in_operation_{$saler->saler_id}", $saler->in_operation == 1 ? $yes : $no),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "saler_{$saler->saler_id}",
        'sale_order_form',
        $saler_url,
        "{id: $saler->saler_id, type: 'saler_id'}",
        "$collection"        
    );
    echo $this->block->generate_editor(
        "saler_in_operation_{$saler->saler_id}",
        'sale_order_form',
        $saler_url,
        "{id: $saler->saler_id, type: 'in_operation'}",
        $in_operation_collection
    );
}
$title = lang('sale_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();

echo block_notice_div(lang('note') . ": " . lang('sale_input_user_notice'));

?>

