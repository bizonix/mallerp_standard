<?php
$CI = & get_instance();
$ship_codes = $CI->shipping_code_model->fetch_all_shipping_codes();
$shipping_code = array();
$shipping_code[""] = lang('all');
foreach ($ship_codes as $ship_code)
{
    $shipping_code[$ship_code->code] = $ship_code->code;
}

$currencies = $CI->email_search_model->fetch_currency_code();
$currency = array();
$currency[""] = lang('all');
foreach ($currencies as $cur)
{
    $currency[$cur->code] = $cur->code;
}

$payment_types = $CI->order_model->fetch_all_income_type();
$payment_type = array();
$payment_type[""] = lang('all');
foreach ($payment_types as $types)
{
    $payment_type[$types->receipt_name] = $types->receipt_name;
}

$head = array(
    lang('name'),
    lang('value'),
    lang('name'),
    lang('value'),
);
$data = array();
$config1 = array(
      'name'        => 'country',
      'id'          => 'country',
      'maxlength'   => '20',
      'size'        => '20',
      'value'       => isset($country) ? $country : NULL,
);
$config2 = array(
      'name'        => 'gross_from',
      'id'          => 'gross_from',
      'maxlength'   => '10',
      'size'        => '10',
      'value'       => isset($gross_from) ? $gross_from : NULL,
);
$config3 = array(
      'name'        => 'gross_to',
      'id'          => 'gross_to',
      'maxlength'   => '10',
      'size'        => '10',
      'value'       => isset($gross_to) ? $gross_to : NULL,
);

$data[] = array(
    lang('country'),
    form_input($config1),
    lang('gross'),
    form_input($config2)." - ".form_input($config3),
);

$config1 = array(
    'name'    => 'skus',
    'id'      => 'skus',
    'value'   => isset($skus)? $skus : NULL,

);
$config2 = array(
    'name'    => 'shipping_cost_from',
    'id'      => 'shipping_cost_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($shipping_cost_from)? $shipping_cost_from : NULL,
);
$config3 = array(
    'name'    => 'shipping_cost_to',
    'id'      => 'shipping_cost_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($shipping_cost_to)? $shipping_cost_to : NULL,
);

$data[] = array(
    'sku',
    form_input($config1),
    lang('shipping_cost'),
    form_input($config2)." - ".form_input($config3),
);

$statuses = fetch_statuses('order_status');
$status_name[''] = lang('all');
foreach ($statuses as $key => $status)
{
    $status_name[$key] = lang($status);
}

$config2 = array(
    'name'        => 'profit_rate_from',
    'id'          => 'profit_rate_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'       => isset($profit_rate_from)? $profit_rate_from : NULL,
);
$config3 = array(
    'name'        => 'profit_rate_to',
    'id'          => 'profit_rate_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'       => isset($profit_rate_to)? $profit_rate_to : NULL,
);
$data[] = array(
    lang('order_status'),
    form_dropdown('order_status', $status_name, isset($cur_status)? $cur_status : NULL, 'id = order_status'),
    lang('profit_rate'),
    form_input($config2).' - '.form_input($config3),
);

$saler_users = $CI->user_model->fetch_all_sale_users();
$sales = array();
$sales[] = lang('all');
foreach ($saler_users as $saler_user)
{
    $sales[$saler_user->u_id] = $saler_user->u_name;
}
$config2 = array(
    'name'    => 'ship_weight_from',
    'id'      => 'ship_weight_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($ship_weight_from)? $ship_weight_from : NULL,
);
$config3 = array(
    'name'    => 'ship_weight_to',
    'id'      => 'ship_weight_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($ship_weight_to)? $ship_weight_to : NULL,
);
$data[] = array(
    lang('saler_name'),
    form_dropdown('saler_id', $sales, isset($cur_sales)? $cur_sales : NULL, 'id = saler_id'),
    lang('ship_weight'),
    form_input($config2).' - '.form_input($config3),
);

$refund_type_obj = $CI->order_model->fetch_all_bad_comment_type();
$refund_types = array();
$refund_types[] = lang('all');
foreach ($refund_type_obj as $item)
{
    $refund_types[$item->id] = $item->type;
}
$data[] = array(
    lang('refund_verify_type'),
    form_dropdown('refund_verify_type', $refund_types, isset($cur_refund_type)? $cur_refund_type : NULL, 'id = refund_verify_type'),
    lang('input_date'),
    lang('from').block_time_picker('input_datetime_from', isset($input_datetime_from) ? $input_datetime_from : NULL).lang('to').block_time_picker('input_datetime_to', isset($input_datetime_to) ? $input_datetime_to : NULL) ,
);

$config1 = array(
    'name'    => 'item_titles',
    'id'      => 'item_titles',
    'value'   => isset($item_titles)? $item_titles : NULL,
);
$data[] = array(
    'Item titles (*)',
    form_input($config1),
    lang('ship_confirm_date'),
    lang('from').block_time_picker('ship_confirm_date_from', isset($ship_confirm_date_from) ? $ship_confirm_date_from : NULL ).lang('to').block_time_picker('ship_confirm_date_to', isset($ship_confirm_date_to) ? $ship_confirm_date_to : NULL),
);

$config1 = array(
    'name'    => 'item_no',
    'id'      => 'item_no',
    'value'   => isset($item_no)? $item_no : NULL,
);
$data[] = array(
    'Item No (*)',
    form_input($config1),
    lang('enter_cost_date'),
    lang('from').block_time_picker('cost_date_from', isset($cost_date_from)? $cost_date_from : NULL).lang('to').block_time_picker('cost_date_to', isset($cost_date_to)? $cost_date_to : NULL),
);

$config1 = array(
    'name'    => 'state_province',
    'id'      => 'state_province',
    'value'   => isset($state_province)? $state_province : NULL,
);

$data[] = array(
    lang('state_province'),
    form_input($config1),
    lang('shipping_code'),
    form_dropdown('shipping_code', $shipping_code, isset($cur_ship_code)? $cur_ship_code : NULL, 'id = shipping_code')

);

$config1 = array(
    'name'    => 'town_city',
    'id'      => 'town_city',
    'value'   => isset($town_city)? $town_city : NULL,
);

$data[] = array(
    lang('town_city'),
    form_input($config1),
    lang('receipt_way'),
    form_dropdown('payment_type', $payment_type, isset($cur_payment_type)? $cur_payment_type : NULL, 'id = payment_type'),
);


$config1 = array(
    'name'    => 'qties',
    'id'      => 'qties',
    'value'   => isset($qties)? $qties : NULL,
);
$data[] = array(
    lang('currency_code'),
    form_dropdown('currency', $currency, isset($cur_currency)? $cur_currency : NULL, 'id = currency'),
    lang('qty_str'),
    form_input($config1),
);

/*--------------------排除查询开始---------------------------------------*/
$data_b = array();
$config1 = array(
      'name'        => 'country_b',
      'id'          => 'country_b',
      'maxlength'   => '20',
      'size'        => '20',
      'value'       => isset($country_b) ? $country_b : NULL,
);
$config2 = array(
      'name'        => 'gross_b_from',
      'id'          => 'gross_b_from',
      'maxlength'   => '10',
      'size'        => '10',
      'value'       => isset($gross_b_from) ? $gross_b_from : NULL,
);
$config3 = array(
      'name'        => 'gross_b_to',
      'id'          => 'gross_b_to',
      'maxlength'   => '10',
      'size'        => '10',
      'value'       => isset($gross_b_to) ? $gross_b_to : NULL,
);

$data_b[] = array(
    lang('country'),
    form_input($config1),
    lang('gross'),
    form_input($config2)." - ".form_input($config3),
);

$config1 = array(
    'name'    => 'skus_b',
    'id'      => 'skus_b',
    'value'   => isset($skus_b)? $skus_b : NULL,

);
$config2 = array(
    'name'    => 'shipping_cost_b_from',
    'id'      => 'shipping_cost_b_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($shipping_cost_b_from)? $shipping_cost_b_from : NULL,
);
$config3 = array(
    'name'    => 'shipping_cost_b_to',
    'id'      => 'shipping_cost_b_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($shipping_cost_b_to)? $shipping_cost_b_to : NULL,
);

$data_b[] = array(
    'sku',
    form_input($config1),
    lang('shipping_cost'),
    form_input($config2)." - ".form_input($config3),
);


$config2 = array(
    'name'        => 'profit_rate_b_from',
    'id'          => 'profit_rate_b_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'       => isset($profit_rate_b_from)? $profit_rate_b_from : NULL,
);
$config3 = array(
    'name'        => 'profit_rate_b_to',
    'id'          => 'profit_rate_b_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'       => isset($profit_rate_b_to)? $profit_rate_b_to : NULL,
);
$data_b[] = array(
    lang('order_status'),
    form_dropdown('order_status_b', $status_name, isset($cur_status_b)? $cur_status_b : NULL, 'id = order_status_b'),
    lang('profit_rate'),
    form_input($config2).' - '.form_input($config3),
);

$config2 = array(
    'name'    => 'ship_weight_b_from',
    'id'      => 'ship_weight_b_from',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($ship_weight_b_from)? $ship_weight_b_from : NULL,
);
$config3 = array(
    'name'    => 'ship_weight_b_to',
    'id'      => 'ship_weight_b_to',
    'maxlength'   => '10',
    'size'        => '10',
    'value'   => isset($ship_weight_b_to)? $ship_weight_b_to : NULL,
);
$data_b[] = array(
    lang('saler_name'),
    form_dropdown('saler_id_b', $sales, isset($cur_sales_b)? $cur_sales_b : NULL, 'id = saler_id_b'),
    lang('ship_weight'),
    form_input($config2).' - '.form_input($config3),
);

$data_b[] = array(
    lang('refund_verify_type'),
    form_dropdown('refund_verify_type_b', $refund_types, isset($cur_refund_type_b)? $cur_refund_type_b : NULL, 'id = refund_verify_type_b'),
    lang('input_date'),
    lang('from').block_time_picker('input_datetime_b_from', isset($input_datetime_b_from) ? $input_datetime_b_from : NULL).lang('to').block_time_picker('input_datetime_b_to', isset($input_datetime_b_to) ? $input_datetime_b_to : NULL) ,
);

$config1 = array(
    'name'    => 'item_titles_b',
    'id'      => 'item_titles_b',
    'value'   => isset($item_titles_b)? $item_titles_b : NULL,
);
$data_b[] = array(
    'Item titles (*)',
    form_input($config1),
    lang('ship_confirm_date'),
    lang('from').block_time_picker('ship_confirm_date_b_from', isset($ship_confirm_date_b_from) ? $ship_confirm_date_b_from : NULL ).lang('to').block_time_picker('ship_confirm_date_b_to', isset($ship_confirm_date_b_to) ? $ship_confirm_date_b_to : NULL),
);

$config1 = array(
    'name'    => 'item_no_b',
    'id'      => 'item_no_b',
    'value'   => isset($item_no_b)? $item_no_b : NULL,
);
$data_b[] = array(
    'Item No (*)',
    form_input($config1),
    lang('enter_cost_date'),
    lang('from').block_time_picker('cost_date_b_from', isset($cost_date_b_from)? $cost_date_b_from : NULL).lang('to').block_time_picker('cost_date_b_to', isset($cost_date_b_to)? $cost_date_b_to : NULL),
);

$config1 = array(
    'name'    => 'state_province_b',
    'id'      => 'state_province_b',
    'value'   => isset($state_province_b)? $state_province_b : NULL,
);

$data_b[] = array(
    lang('state_province'),
    form_input($config1),
    lang('shipping_code'),
    form_dropdown('shipping_code_b', $shipping_code, isset($cur_ship_code_b)? $cur_ship_code_b : NULL, 'id = shipping_code_b')

);

$config1 = array(
    'name'    => 'town_city_b',
    'id'      => 'town_city_b',
    'value'   => isset($town_city_b)? $town_city_b : NULL,
);

$data_b[] = array(
    lang('town_city'),
    form_input($config1),
    lang('receipt_way'),
    form_dropdown('payment_type_b', $payment_type, isset($cur_payment_type_b)? $cur_payment_type_b : NULL, 'id = payment_type_b'),
);


$config1 = array(
    'name'    => 'qties_b',
    'id'      => 'qties_b',
    'value'   => isset($qties_b)? $qties_b : NULL,
);
$data_b[] = array(
    lang('currency_code'),
    form_dropdown('currency_b', $currency, isset($cur_currency_b)? $cur_currency_b : NULL, 'id = currency_b'),
    lang('qty_str'),
    form_input($config1),
);

if(!isset($subscriptions))
{
    $subscriptions = array();
}

array_unshift($subscriptions, lang('select'));
$config1 = array(
      'name'        => 'reflesh',
      'id'          => 'reflesh',
      'type'        => 'submit',
      'value'       => lang('reflesh'),
      'onclick'     => 'check_reflesh()',
);

$url_reflesh = site_url('seo/email_search/save_subscription');
$data_b[] = array(
    $this->block->generate_required_mark('subscription'),
    form_dropdown('sub_id', $subscriptions, '0','id = sub_id').form_submit($config1),
    NULL,
    NULL,
);
/*--------------------排除查询结束---------------------------------------*/

$title = lang('email_advanced_search');
echo block_header($title);
$attributes = array(
    'id' => 'email_search'
);
$url = site_url('seo/email_search/email_search_result');
$error = lang('one_value_required');

echo form_open($url, $attributes);
echo $this->block->generate_table($head, $data);
echo '<br/>';
echo block_header(lang('email_exclude_search'));
echo $this->block->generate_table($head, $data_b);
echo '<br/>';

$config = array(
    'id'          => 'search',
    'name'        => 'search',
    'value'       => lang('search'),
    'type'        => 'submit',
    'onclick'     => "return check_submit('$error', this.form)",
);
echo block_button($config);
$config_sent = array(
    'name'      => 'sent_email',
    'id'        => 'sent_email',
    'value'     => lang('sent_to_shiqi'),
    'type'      => 'submit',
    'onclick'   => 'return check_sent()',
);
if(isset($check_value) && $check_value == 1 && isset($nums) && $nums > 0)
{
    echo block_button($config_sent);
}
echo form_close();

echo $this->block->generate_ac('skus', array('product_basic', 'sku'));
echo $this->block->generate_ac('country', array('country_code', 'name_en'));
echo "<br><br><br>";

echo "<div id ='result'>";
if(isset($nums) && $nums > 0)
{
    $title = lang('email_matches') . $nums.lang('num');
    $remark = block_header($title);
    if (isset($sent_value) && $sent_value == 1)
    {
        $remark = block_header(lang('sent_success'));
    }
    echo $remark;
}
else if(isset($nums) && $nums == 0)
{
    $title = lang('no_email_matches');
    echo block_header($title);
}
echo "</div>";

?>
<script type="text/javascript">
    function check_submit(error, form)
    {
        var country = $('country').value;
        var gross_from = $('gross_from').value;
        var gross_to = $('gross_to').value;
        var input_datetime_from = $('input_datetime_from').value;
        var input_datetime_to = $('input_datetime_to').value;
        var ship_confirm_date_from = $('ship_confirm_date_from').value;
        var ship_confirm_date_to = $('ship_confirm_date_to').value;
        var skus = $('skus').value;
        var order_status = $('order_status').value;
        var cost_date_from = $('cost_date_from').value;
        var cost_date_to = $('cost_date_to').value;
        var shipping_cost_from = $('shipping_cost_from').value;
        var shipping_cost_to = $('shipping_cost_to').value;
        var saler_id = $('saler_id').value;
        var profit_rate_from = $('profit_rate_from').value;
        var profit_rate_to = $('profit_rate_to').value;
        var refund_verify_type = $('refund_verify_type').value;
        var ship_weight_from = $('ship_weight_from').value;
        var ship_weight_to = $('ship_weight_to').value;
        var item_titles = $('item_titles').value;
        var item_no = $('item_no').value;
        var shipping_code = $('shipping_code').value;
        var state_province = $('state_province').value;
        var town_city = $('town_city').value;
        var currency = $('currency').value;
        var payment_type = $('payment_type').value;
        var qties = $('qties').value;

        var country_b = $('country_b').value;
        var gross_b_from = $('gross_b_from').value;
        var gross_b_to = $('gross_b_to').value;
        var input_datetime_b_from = $('input_datetime_b_from').value;
        var input_datetime_b_to = $('input_datetime_b_to').value;
        var ship_confirm_date_b_from = $('ship_confirm_date_b_from').value;
        var ship_confirm_date_b_to = $('ship_confirm_date_b_to').value;
        var skus_b = $('skus_b').value;
        var order_status_b = $('order_status_b').value;
        var cost_date_b_from = $('cost_date_b_from').value;
        var cost_date_b_to = $('cost_date_b_to').value;
        var shipping_cost_b_from = $('shipping_cost_b_from').value;
        var shipping_cost_b_to = $('shipping_cost_b_to').value;
        var saler_id_b = $('saler_id_b').value;
        var profit_rate_b_from = $('profit_rate_b_from').value;
        var profit_rate_b_to = $('profit_rate_b_to').value;
        var refund_verify_type_b = $('refund_verify_type_b').value;
        var ship_weight_b_from = $('ship_weight_b_from').value;
        var ship_weight_b_to = $('ship_weight_b_to').value;
        var item_titles_b = $('item_titles_b').value;
        var item_no_b = $('item_no_b').value;
        var shipping_code_b = $('shipping_code_b').value;
        var state_province_b = $('state_province_b').value;
        var town_city_b = $('town_city_b').value;
        var currency_b = $('currency_b').value;
        var payment_type_b = $('payment_type_b').value;
        var qties_b = $('qties_b').value;

        if (country == '' && gross_from == '' && gross_to == '' && input_datetime_from == '' && input_datetime_to == '' && ship_confirm_date_from == '' && ship_confirm_date_to == ''
            && skus == '' && order_status == '' && cost_date_from == '' && cost_date_to == '' && shipping_cost_from == '' && shipping_cost_to == '' && saler_id == '0'
            && profit_rate_from == '' && profit_rate_to == '' && refund_verify_type == '0' && ship_weight_from == '' && ship_weight_to == '' && item_titles == ''
            && item_no == '' && shipping_code == '' && state_province == '' && town_city == '' && currency == '' && payment_type == '' && qties == ''
            && country_b == '' && gross_b_from == '' && gross_b_to == '' && input_datetime_b_from == '' && input_datetime_b_to == '' && ship_confirm_date_b_from == '' && ship_confirm_date_b_to == ''
            && skus_b == '' && order_status_b == '' && cost_date_b_from == '' && cost_date_b_to == '' && shipping_cost_b_from == '' && shipping_cost_b_to == '' && saler_id_b == '0'
            && profit_rate_b_from == '' && profit_rate_b_to == '' && refund_verify_type_b == '0' && ship_weight_b_from == '' && ship_weight_b_to == '' && item_titles_b == ''
            && item_no_b == '' && shipping_code_b == '' && state_province_b == '' && town_city_b == '' && currency_b == '' && payment_type_b == '' && qties_b  == ''
            )
        {
            alert(error);
            return false;
        }
        this.blur();
        helper.show_loading();
   
    }

    function check_sent()
    {

       var subscription = document.getElementById('sub_id').value;
       if (subscription == 0)
       {
           alert('please select subscription');
           return false;
       }
       else
       {
           if( confirm('are you sure'))
           {
               this.blur();
               helper.show_loading();
           }
           else
           {
               return false;
           }
       }
     }


    function check_reflesh()
    {
        this.blur();
        helper.show_loading();
    }
</script>