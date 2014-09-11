<?php
$CI = & get_instance();
$user_id = $CI->get_current_user_id();
$user_info = $CI->user_model->fetch_user_by_id($user_id);
$user_name_en=str_replace(' ','~@~',$user_info->name_en);
$user_name_en=str_replace(',','~@~',$user_name_en);
$user_name_en=str_replace('"','\"',$user_name_en);
$user_name_en=str_replace("'","\'",$user_name_en);
$title = lang('ebay_message_reply');
$back_button = block_back_icon(site_url('order/ebay_message/manage'));
echo block_header($title.$back_button);
$staus=array('status'=>1);
if($message->status==0){
$CI->ebay_model->update_ebay_message_by_id($message->id, $staus);
}

$url = site_url('order/ebay_message/ebay_message_reply_save');
echo '<center>';
echo '<br/>';
$attributes = array(
    'id' => 'reply_form',
);
echo form_open($url, $attributes);
$head = array(
    lang('key'),
    lang('value'),
);

$data = array();
$data[] = array(
        lang('buyer_id'),
		$message->sendid, 
    );
$data[] = array(
        lang('ebay_id'),
		$message->recipientid, 
    );
$data[] = array(
        lang('email_title'),
		$message->subject, 
    );
$data[] = array(
        lang('email_content'),
		'<font color="#FF0000"><b>'.nl2br($message->body).'</b></font>', 
    );
$data[] = array(
        lang('created_date'),
		$message->createtime, 
    );
$data[] = array(
        lang('item_id_str'),
		$message->itemid. '<br/><img src='.$img_url.'>', 
    );
$data[] = array(
        lang('product'),
		'<a href='.$message->itemurl.' target=_blank>'.$message->title.'</a>', 
    );
$data[] = array(
        lang('status_id'),
		lang($CI->order_model->fetch_status_name('message_status', $message->status)),
    );
$data[] = array(
        lang('replyied_message'),
		$message->replaycontent, 
    );

$history_messages=$this->ebay_model->fetch_all_message_history($message->sendid,$message->itemid);
$html='';
foreach ($history_messages as $history_message) {
	if($history_message->message_id!=$message->message_id){
		$html .='<font color="#FF0000"><b>'.nl2br($history_message->body).'</b><br>'.$history_message->createtime.'</font><br><font color="#0000FF"><b>'.nl2br($history_message->replaycontent)."</b><br>".$history_message->reply_time."</font><br><hr>";
	}
}

$data[] = array(
        lang('message_record'),
		$html, 
    );
$html='';
foreach ($templates as $template) {
    $html .= "<label onclick=get_coefficient('".str_replace(' ','~@~',$template->template_content)."','".$message->sendid."');>  ".$template->template_name."</label><br>";
}

$data[] = array(
        lang('message_template'),
		$html,
    );
$html='';
//$body=str_replace("'","\'",$detail->Question->Body);
//$body=str_replace('"','\"',$body);
foreach ($orders as $order) {
	$name=str_replace(' ','~@~',$order->name);
	$name=str_replace(',','~@~',$name);
	$name=str_replace('"','\"',$name);
	$name=str_replace("'","\'",$name);
	
	$ship_confirm_date=str_replace(' ','~@~',$order->ship_confirm_date);
	$ship_confirm_date=str_replace(',','~@~',$ship_confirm_date);
	$ship_confirm_date=str_replace('"','\"',$ship_confirm_date);
	$ship_confirm_date=str_replace("'","\'",$ship_confirm_date);
	
	$address_line_1=str_replace(' ','~@~',$order->address_line_1);
	$address_line_1=str_replace(',','~@~',$address_line_1);
	$address_line_1=str_replace('"','\"',$address_line_1);
	$address_line_1=str_replace("'","\'",$address_line_1);
	$address_line_2=str_replace(' ','~@~',$order->address_line_2);
	$address_line_2=str_replace(',','~@~',$address_line_2);
	$address_line_2=str_replace('"','\"',$address_line_2);
	$address_line_2=str_replace("'","\'",$address_line_2);
	
	$town_city=str_replace(' ','~@~',$order->town_city);
	$town_city=str_replace(',','~@~',$town_city);
	$town_city=str_replace('"','\"',$town_city);
	$town_city=str_replace("'","\'",$town_city);
	$state_province=str_replace(' ','~@~',$order->state_province);
	$state_province=str_replace(',','~@~',$state_province);
	$state_province=str_replace('"','\"',$state_province);
	$state_province=str_replace("'","\'",$state_province);
	$country=str_replace(' ','~@~',$order->country);
	$country=str_replace(',','~@~',$country);
	$country=str_replace('"','\"',$country);
	$country=str_replace("'","\'",$country);
	$zip_code=str_replace(' ','~@~',$order->zip_code);
	$zip_code=str_replace(',','~@~',$zip_code);
	$zip_code=str_replace('"','\"',$zip_code);
	$zip_code=str_replace("'","\'",$zip_code);
	
	$track_url=$CI->ebay_model->get_track_url_by_is_register($order->is_register);
	
    $html .= "<label onclick=get_orderinfo('".$order->track_number."','".$ship_confirm_date."','".$order->item_no."','".$order->ship_remark."','".$name."','".$address_line_1."','".$address_line_2."','".$town_city."','".$state_province."','".$zip_code."','".$country."','".trim($track_url)."','".$user_name_en."');>  ".$order->item_no."---".$order->currency." ".(($order->gross!='')?$order->gross:$order->net)."[".$order->item_id_str."]</label><br>";
}
$data[] = array(
        lang('order'),
		$html,
    );
$config = array(
    'name'        => 'replaycontent',
    'id'          => 'replaycontent',
    'value'       => $message->replaycontent,
	'cols'        => '90',
    'rows'        => '10',

);
$config_id = array(
        'name' => 'id',
        'id' => 'id' ,
        'value' => $message->id,
        'type' => 'hidden',
    );
$data[] = array(
        lang('ebay_message_reply'),
		form_textarea($config).form_input($config_id), 
    );
echo $this->block->generate_table($head, $data);


if($message->update_ebay!=1 && $message->status!=5){
$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'button',
	'onclick'     => "this.blur();helper.ajax('$url',$('reply_form').serialize(true), 1);",
);

echo block_button($config);
}
echo form_close();
echo '</center>';
$note = lang('note') . ': ' . '<br/>' .
    lang('message_template_note');
echo block_notice_div($note);
?>
<script>
    function get_coefficient(value,buyerid)
    {
		var value=value.replace("{buyerid}",buyerid);
		var value=value.replace(/~@~/g, ' ');
		//alert(value);
		
        if(value)
        {
            $('replaycontent').value = value;
			//document.getElementById('replaycontent').value = value;
        }
        else
        {
            alert('模版内容为空值');
        }
    }
	function get_orderinfo(track_number,ship_confirm_date,item_no,ship_remark,name,address_line_1,address_line_2,town_city,state_province,zip_code,country,track_url,my_name_en){
		var value=$('replaycontent').value;
		var value=value.replace("{track_number}",track_number);
		var value=value.replace("{ship_confirm_date}",ship_confirm_date);
		var value=value.replace("{item_no}",item_no);
		var value=value.replace("{ship_remark}",ship_remark);
		var value=value.replace("{name}",name);
		var value=value.replace("{address_line_1}",address_line_1);
		var value=value.replace("{address_line_2}",address_line_2);
		var value=value.replace("{town_city}",town_city);
		var value=value.replace("{state_province}",state_province);
		var value=value.replace("{zip_code}",zip_code);
		var value=value.replace("{country}",country);
		var value=value.replace("{track_url}",track_url);
		var value=value.replace("{my_name_en}",my_name_en);
		var value=value.replace(/~@~/g, ' ');
		
		
		if(value)
        {
            $('replaycontent').value = value;
			//document.getElementById('replaycontent').value = value;
        }
	}


</script>