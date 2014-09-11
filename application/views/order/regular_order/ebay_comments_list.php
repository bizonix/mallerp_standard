<?php
$CI = & get_instance();

if (! isset($CI->order_model))
{
    $CI->load->model('order_model');
}
    
$head = array(
    array('text' => lang('buyer_id'), 'sort_key' => 'buyer_id', 'id' => 'comments'),
    lang('comment_reply'),
    array('text' => lang('comment_time'), 'sort_key' => 'feedback_time'),
    lang('product'),
    array('text' => lang('ebay_id'), 'sort_key' => 'ebay_id'),
    array('text' => lang('comment_results'), 'sort_key' => 'feedback_type'),
    lang('review_state'),
);
$data = array();
$confirm_feedback_url = site_url('order/ebay_comments/confirm_feedback');
$code_url = site_url('order/ebay_comments/verify_feedback_item_no');
$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
$role = $this->user_model->fetch_user_priority_by_system_code('sale');
$options = array();
$bad_comment_id = array();
foreach ($bad_comment_types as $bad_comment_type) {
    $options[''] = lang('all');
    $options[$bad_comment_type->id] = $bad_comment_type->type;
}
foreach ($feedbacks as $feedback) {
    $row = array();
    $row[] = $feedback->buyer_id;
    $content_resp = $feedback->feedback_content . br().br();
    $content_resp .= lang('reply') . $feedback->feedback_response;
    $row[] = $content_resp;
    $row[] = $feedback->feedback_time;

    $item_id_html = '<a target="_blank" href="' . $ebay_url . $feedback->item_id . '">'.$feedback->item_id.'</a>';
    $procuct = lang('item_id').'：'.$item_id_html . br();
    $procuct .= lang('good_name').'：'.$feedback->item_title . br();
    $procuct .= lang('transaction_id').'：'.$feedback->transaction_id . br();
    $procuct .= lang('item_no').'：'."<span id = 'feedback_{$feedback->id}'>" . ($feedback->item_no ?  $feedback->item_no : '[edit]') . "</span>";
    $row[] = $procuct;
    echo $this->block->generate_editor(
            "feedback_{$feedback->id}",
            'feedback_form',
            $code_url,
            "{id: $feedback->id, type: 'item_no'}"
    );
    $row[] = $feedback->ebay_id;
    $row[] = lang($feedback->feedback_type);

    $comment_type_ = 'comment_type_';
    $stock_type_   = 'stock_type_';
    $give_back = '';
    $give_back .= form_hidden_by_id('stock_type_' . $feedback->id, $stock_type);
    $give_back .= form_dropdown('comment_type_' . $feedback->id, $options, $feedback->verify_type, 'id="' . $comment_type_ . $feedback->id . '"');
    $give_back .= br();
    $config = array(
        'name' => 'remark_' . $feedback->id,
        'id' => 'remark_' . $feedback->id,
        'value' => $feedback->verify_content,
        'rows' => '2',
        'cols' => '17',
    );
    $give_back .= form_textarea($config);
    
    $config = array(
        'name' => 'confirm_' . $feedback->id,
        'id' => 'confirm_' . $feedback->id,
        'value' => lang('confirm_review'),
        'onclick' => "confirm_review('$confirm_feedback_url', $feedback->id,'',this);",
    );

    $config_sku = array(
        'name' => 'sku_' . $feedback->id,
        'id' => 'sku_' . $feedback->id,
        'value' => $feedback->feedback_sku_str,
        'maxlength' => '300',
        'size' => '15',
    );
    
        
    $config_user = array(
        'name' => 'user_' . $feedback->id,
        'id' => 'user_' . $feedback->id,
        'value' => $feedback->feedback_duty,
        'maxlength' => '100',
        'size' => '15',
    );
    
    if ( ! empty ($feedback->item_no) && $this->order_model->check_exists('order_list', array('item_no' => $feedback->item_no)))
    {
        $order = $CI->order_model->get_order_with_item_no_for_ebay_comment($feedback->item_no);

        $config_other_sku = array(
            'name' => 'other_refund_sku_' . $order->id,
            'id' => 'other_refund_sku_' . $order->id,
            'maxlength' => '50',
            'size' => '10',
        );

        $problem_sku_html = '';

        $i = 0;

        $skus_all = array();
        if($feedback->feedback_sku_str)
        {
            $sku_str_all = $order->sku_str . ',' . $feedback->feedback_sku_str;
            $skus_all = array_unique(explode(',', $sku_str_all));
        }
        else
        {
            $skus_all = explode(',', $order->sku_str);
        }

        foreach ($skus_all as $sku)
        {
            $config_rs = array(
                'name'        => 'refund_sku_' . $order->id,
                'id'          => 'refund_sku_' . $order->id . '_' . $i,
                'value'       => $sku,
                'checked'     => empty ($feedback->feedback_sku_str) ? FALSE : (strpos($feedback->feedback_sku_str, $sku) !== FALSE ? TRUE : FALSE),
            );
            $problem_sku_html .= form_checkbox($config_rs) . $sku . '<br/>';
            $i++;
        }
        $problem_sku_html .= lang('other').'SKU : '.form_input($config_other_sku);

        echo $this->block->generate_ac('other_refund_sku_' . $order->id, array('product_basic', 'sku'));
        
        $give_back .= br().lang('person_responsible').' : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.form_input($config_user).br().$problem_sku_html;
        
        echo $this->block->generate_ac('user_' . $feedback->id, array('user', 'name'));
        
        $config['onclick'] = "confirm_review('$confirm_feedback_url', $feedback->id, $order->id, this);";
    
    }
    else
    {
        $give_back .= br().lang('person_responsible').' : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.form_input($config_user).br().lang('problem_sku').' : '.form_input($config_sku);   
    
        echo $this->block->generate_ac('sku_' . $feedback->id, array('product_basic', 'sku'));
        
        echo $this->block->generate_ac('user_' . $feedback->id, array('user', 'name'));
    }
   

    if ($feedback->feedback_type != $stock_type)
    {
        if ($feedback->feedback_type != 'bad_comments_commited')
        { 
            if ((($role > 1)|| ($CI->is_super_user())))
            {
                $give_back .= '<br/>' . block_button($config);
             }
            else if (($feedback->feedback_type == 'Negative') OR ($feedback->feedback_type == 'bad_first_verify'))
            {
                $give_back .= '<br/>' . block_button($config);
            }
        }
    } else {
        $give_back .= '<br/>' . block_button($config);
    }
    $row[] = $give_back;
//    $row[] = $problem_sku_html;
    $data[] = $row;
}

if ($stock_type)
{
    $result = array('' => lang('all'), 'bad_comments_wait_for_commit' => lang('bad_comments_wait_for_commit'), 'bad_comments_commited' => lang('bad_comments_commited'));
} else {
    $result = array('' => lang('all'), 'Negative' => lang('bad'), 'bad_first_verify' => lang('bad_first_verify'), 'Negative_vertify' => lang('Negative_vertify'), 'bad_comments_wait_for_commit' => lang('bad_comments_wait_for_commit'), 'bad_comments_commited' => lang('bad_comments_commited'));
}
$filters = array(
    array(
        'type' => 'input',
        'field' => 'buyer_id',
    ),
    array(
        'type' => 'input',
        'field' => 'feedback_content|feedback_response',
    ),
 
    array(
		'type'      => 'date',
		'field'     => 'feedback_time',
        'method'    => 'from_to'
	),
    array(
        'type' => 'input',
        'field' => 'item_id|item_title|transaction_id',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'ebay_id',
        'options' => $ebay_ids,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'feedback_type',
        'options' => $result,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'verify_type',
        'options' => $options,
        'method' => '=',
    ),
);

$config = array(
    'filters' => $filters,
);

$title = lang('ebay_comment_list');
echo block_header($title);

echo $this->block->generate_pagination('comments');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'comments');

echo form_close();

echo $this->block->generate_pagination('comments');
?>