<?php
$this->load->helper('product_permission');
$base_url = base_url();

$head = array(
    lang('name'),
    lang('value'),
);

$data = array();

$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';

$data[] = array(
    lang('item_number'),
    $order->item_no,
);

$data[] = array(
    lang('from_email'),
    $order->from_email,
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       => $order ? $order->name : '',
    'maxlength'   => '80',
    'size'        => '80',
);
$data[] = array(
    lang('name'),
    $order->name,
);

$data[] = array(
    lang('transaction_id'),
    $order->transaction_id,
);

$data[] = array(
    lang('item_id'),
    $order->item_id_str,
);


$item_ids = explode(',', $order->item_id_str);
$skus = explode(',', $order->sku_str);
$qties = explode(',', $order->qty_str);
$count = count($item_ids);

$item_sku_html = '';
$product_name = '';
$item_sku_html .= "<div id='item_div_$order->id'>";
for ($i = 0; $i < $count; $i++)
{
    $item_sku_html .= '<div style="margin: 5px;">';
    $item_sku_html .= '<a target="_blank" href="' . $ebay_url . $item_ids[$i] . '">Item No.</a>: ' . $item_ids[$i].'<br/>';
    $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] : '') . '<br/>';
    $item_sku_html .=  ' Qty: ' . (isset($qties[$i]) ? $qties[$i] : ''). '<br/>';
    
    if (isset($skus[$i]))
    {
        $product_name .=  get_product_name($skus[$i]) . '<br/>';
    }
    $item_sku_html .=$product_name. '</div>';
}
$item_sku_html .= '</div>';

$data[] = array(
    lang('product_information'),
    $item_sku_html,
);

$data[] = array(
    lang('gross').'('.$order->currency.')',
    $order->gross?$order->gross:$order->net,
);

$config = array(
    'name'        => 'return_cost',
    'id'          => 'return_cost',
    'value'       => $order ? $order->return_cost : '',
    'maxlength'   => '30',
    'size'        => '30',
    'type'        => 'text',
    'style'       => 'display:none',
);

$config_is_return = array(
    'name'        => 'is_return',
    'id'          => 'is_return',
    'value'       => $order ? $order->return_order : '',
    'maxlength'   => '30',
    'size'        => '30',
    'type'        => 'hidden',
);

$clue = lang('clue_is_return');

//$gross_number = $order ? ($order->gross ? $order->gross : $order->net) : 0 ;

$select_string = 'onchange="get_gross(this)";';
$label_cost = '<br/><br/><label id="label_cost" style="display:none">'.lang('return_cost').'('.$order->currency.')</label>';
$label_is_return = '<label id="label_is_return" style="display:none">'.lang('is_again').'</label>';
$label_is_return_clue = '<label id="label_clue" style="display:none">('.$clue.')</label>';


if(isset ($tag) && $tag == 'auditing')
{
    $select_string = 'readonly="true"';
    
    $config['style'] = '';

    /**
     * 部分退款
     */
    $status_pr_n =  fetch_status_id('order_status', 'not_received_apply_for_partial_refund');
    $status_pr =  fetch_status_id('order_status', 'received_apply_for_partial_refund');
    
    /**
     * 全额退款
     */
    $status_fr_n =  fetch_status_id('order_status', 'not_received_apply_for_full_refund');
    $status_fr =  fetch_status_id('order_status', 'received_apply_for_full_refund');
    
    /**
     * 重发
     */
    $status_resend_n =  fetch_status_id('order_status', 'not_received_apply_for_resending');
    $status_resend =  fetch_status_id('order_status', 'received_apply_for_resending');
    
    /**
     * 未发货的
     */
    $status_not_ship =  fetch_status_id('order_status', 'not_shipped_apply_for_refund');

    $show_status = array($status_pr_n, $status_pr, $status_fr_n, $status_fr, $status_resend_n, $status_resend, $status_not_ship);

//    if($order->order_status == $status_nrar || $order->order_status == $status_rar)
    if(in_array($order->order_status, $show_status))
    {
        $label_cost = '<br/><br/><label id="label_cost">'.lang('return_cost').'('.$order->currency.')</label>';
        $config['type'] = 'text';
//        $config['readonly'] = 'true';
//        $config['style'] = 'display:';

        if($order->order_status == $status_pr_n)
        {
            $option = array(
                lang('not_received_partial_refunded')
            );
        }
        else if($order->order_status == $status_pr)
        {
            $option = array(
                lang('received_partial_refunded')
            );
        }
        else if($order->order_status == $status_fr_n)
        {
            $option = array(
                lang('not_received_full_refunded')
            );
        }
        else if($order->order_status == $status_fr)
        {
            $option = array(
                lang('received_full_refunded')
            );
        }
        else if($order->order_status == $status_resend_n)
        {
            $option = array(
                lang('not_received_approved_resending')
            );
        }
        else if($order->order_status == $status_resend)
        {
            $option = array(
                lang('received_approved_resending')
            );
        }
        else if($order->order_status == $status_not_ship)
        {
            $option = array(
                lang('not_shipped_agree_to_refund')
            );
        }
        $data[] = array(
            $this->block->generate_required_mark(lang('return_type')),
            form_dropdown('return_type', $option, $order->order_status, $select_string)
                .$label_cost
                .form_input($config)
                .$label_is_return
                .form_input($config_is_return).$label_is_return_clue,
        );
    }
}
else if(isset ($action) && $action === 'no_consignment')
{
    $config = array(
        'name'        => 'return_cost',
        'id'          => 'return_cost',
        'value'       => $order->gross ? $order->gross : $order->net,
        'maxlength'   => '30',
        'size'        => '30',
        'type'        => 'text',
    );
        
    $data[] = array(
        lang('return_cost').'('.$order->currency.')',
        form_input($config),
    );
}
else
{
    $data[] = array(
        $this->block->generate_required_mark(lang('return_type')),
        form_dropdown('return_type', $options, $order->order_status, $select_string)
            .$label_cost
            .form_input($config)
            .$label_is_return
            .form_input($config_is_return).$label_is_return_clue,
    );
}

$options = array(''=>lang('please_select'));
foreach($bad_comment_types as $value)
{
    $options["$value->id"] = $value->type;
}

$data[] = array(
    lang('refund_verify_type'),
    form_dropdown('refund_verify_type', $options, $order->refund_verify_type),
);
    

$config_content = array(
    'name' => 'refund_verify_content',
    'id' => 'refund_verify_content',
    'value' => $order ? $order->refund_verify_content : '',
    'cols' => '30',
    'rows' => '4',
);
$data[] = array(
    lang('refund_verify_content'),
    form_textarea($config_content),
);

$config = array(
    'name'        => 'person_responsible',
    'id'          => 'person_responsible',
    'value'       => $order ? $order->refund_duty : '',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    lang('person_responsible'),
    form_input($config),
);


$problem_sku_html = '';

foreach ($skus as $sku)
{
    $config = array(
        'name'        => 'refund_sku[]',
        'value'       => $sku,
        'checked'     => FALSE,
    );
    $problem_sku_html .= form_checkbox($config) . $sku ;
}

$data[] = array(
    lang('problem_sku'),
    $problem_sku_html,
);

$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'value'       => $order ? $order->return_remark : '',
    'rows'       =>'3',
    'clos'       =>'4',
);
$data[] = array(
    $this->block->generate_required_mark(lang('remark')),
    form_textarea($config),
);

$back_button = $this->block->generate_back_icon(site_url('order/special_order/view_list_return_order'));

$title = lang('return_option');
if(isset ($tag) && $tag == 'auditing')
{
    $status_name =  fetch_status_name('order_status', $order->order_status);

    $title = lang('return_and_again_auditing').'('.lang($status_name).')';
}

if(isset ($action) && $action === 'no_consignment')
{
    $title = lang('no_consignment_return_cost_html');
}

echo block_header($title);
$attributes = array(
    'id' => 'return_order_form',
);
echo form_open(site_url('order/special_order/save_return_order'), $attributes);
echo $this->block->generate_table($head, $data);

echo form_hidden('id', $order->id);
echo form_hidden('item_no', $order->item_no);
echo form_hidden('view_return_cost', $order->return_cost);

$commit_button = lang('apply');

$url = site_url('order/special_order/save_return_order');
$config = array(
    'name'        => 'submit',
    'value'       => $commit_button,
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('return_order_form').serialize(true), 1);",
);

if(isset ($tag) && $tag == 'auditing')
{
    $return_type_string = $this->order_model->fetch_status_name('order_status', $order->order_status);

    switch ($return_type_string)
    {
        case 'not_received_apply_for_partial_refund' :
            $button_value = lang('yes_part');
            $button_rejected = lang('no_part');
            break;
        case 'not_received_apply_for_full_refund':
            $button_value = lang('yes_full');
            $button_rejected = lang('no_full');
            break;
        case 'not_received_apply_for_resending' :
            $button_value = lang('yes_again');
            $button_rejected = lang('no_again');
            break;

        case 'received_apply_for_partial_refund':
            $button_value = lang('yes_part');
            $button_rejected = lang('no_part');
            break;
        case 'received_apply_for_full_refund':
            $button_value = lang('yes_full');
            $button_rejected = lang('no_full');
            break;
        case 'received_apply_for_resending':
            $button_value = lang('yes_again');
            $button_rejected = lang('no_again');
            break;

        case 'not_shipped_apply_for_refund':
            $button_value = lang('yes_full');
            $button_rejected = lang('no_full');
            break;
        
        default :
            
    }

    $url = site_url('order/special_order/save_return_order',array('approved'));
    $config = array(
        'name'        => 'submit',
        'value'       => isset ($button_value) ? $button_value : '',
        'type'        => 'button',
        'style'       => 'margin:10px',
        'onclick'     => "this.blur();helper.ajax('$url',$('return_order_form').serialize(true), 1);",
    );
    
    if( ! isset ($button_value))
    {
        $config['style'] = 'display:none';
    }

    $html = block_button($config);

    $url = site_url('order/special_order/save_return_order',array('rejected'));
    $config = array(
        'name'        => 'submit',
        'value'       => isset ($button_rejected) ? $button_rejected : '',
        'type'        => 'button',
        'style'       => 'margin:10px',
        'onclick'     => "this.blur();helper.ajax('$url',$('return_order_form').serialize(true), 1);",
    );
        
    if( ! isset ($button_rejected))
    {
        $config['style'] = 'display:none';
    }

    $html .= block_button($config);

    echo '<h2>'.$html.'</h2>';

}
else
{
    echo '<h2>'.block_button($config).'</h2>';
}

echo form_close();

?>

<script>
function get_gross(obj,gross)
{
    var gross = '<?=  $order ? ($order->gross ? $order->gross : $order->net) : 0 ; ?>';
    
//    alert(gross);
    
    var status_full_not = '<?=  fetch_status_id('order_status', 'not_received_apply_for_full_refund') ?>';
    var status_full_yes = '<?=  fetch_status_id('order_status', 'received_apply_for_full_refund') ?>';
    
    var status_partial_not = '<?=  fetch_status_id('order_status', 'not_received_apply_for_partial_refund') ?>';
    var status_partial_yes = '<?=  fetch_status_id('order_status', 'received_apply_for_partial_refund') ?>';

    var status_resending_not = '<?=  fetch_status_id('order_status', 'not_received_apply_for_resending') ?>';
    var status_resending_yes = '<?=  fetch_status_id('order_status', 'received_apply_for_resending') ?>';


    if(obj.value == status_full_not || obj.value == status_full_yes)
    {
//        $('label_cost').hide();
//        $('return_cost').hide();
               
        $('label_is_return').hide();
        $('is_return').type = 'hidden';
        $('label_clue').hide();

        $('label_cost').style.display = '';
        $('return_cost').style.display = '';
        $('return_cost').value = gross;

    }

    if(obj.value == status_partial_not || obj.value == status_partial_yes)
    {
        $('label_is_return').hide();
        $('is_return').type = 'hidden';
        $('label_clue').hide();

        $('label_cost').style.display = '';
        $('return_cost').style.display = '';
        $('return_cost').value = '0.00';
    }

    if(obj.value == status_resending_not || obj.value == status_resending_yes)
    {
//        $('label_cost').hide();
//        $('return_cost').hide();

        $('label_is_return').hide();
        $('is_return').type = 'hidden';
        $('label_clue').hide();

        $('label_cost').style.display = '';
        $('return_cost').style.display = '';
        
        $('return_cost').value = '0.00';
    }

}
</script>