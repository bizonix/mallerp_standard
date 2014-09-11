<?php $add_order_url = site_url('shipping/deliver_management/add_order'); ?>
<?php $download_pdf_url = site_url('shipping/deliver_management/download_pdf/'.date('Y-m-d')); ?>
<?php $download_part_pdf_url = site_url('shipping/deliver_management/download_part_pdf'); ?>
<center>
    <br/><br/>
    <input type="button" value="点击发送获取所有E邮宝track number" onclick="get_track_number('<?=$add_order_url?>');">
    待获取的订单数:<font color=red><span id='unconfirmed_count'><?=$unconfirmed_count?></span></font>
    <div id="loading"></div>

    <br/><br/>
    <a href="<?=$download_pdf_url?>"><font color='green'>下载当天所有E邮宝的打印标签(<font color='blue'>共<?php echo $confirmed_count; ?>个</font>)</font></a>
    <br/><br/><a href="<?=$download_part_pdf_url?>"><font color='blue'>下载当天未下载过的E邮宝打印标签(<font color='blue'>共<?php echo $part_confirmed_count; ?>个</font>)</font></a>
    <br/><br/><br/>
    <font color='green'>点击任何一天下载打印标签</font>
    <?php
        $data = array(
            'uri' => 'shipping/deliver_management/download_pdf'
        );
    ?>
    <?php echo $calendar->generate($year, $month, $data); ?>
    <br/><br/><br/>

	<br/><br/>
<?php $add_order_url = site_url('shipping/epacket_ems/add_order'); ?>
<?php $download_pdf_url = site_url('shipping/epacket_ems/download_pdf/'.date('Y-m-d')); ?>
<?php $download_part_pdf_url = site_url('shipping/epacket_ems/download_part_pdf'); ?>
    <input type="button" value="点击发送获取所有线下E邮宝跟踪号" onclick="get_track_number('<?=$add_order_url?>');">
    待获取的订单数:<font color=red><span id='unconfirmed_ems_count'><?=$unconfirmed_ems_count?></span></font>
	<br/><br/>
    <a href="<?=$download_pdf_url?>"><font color='green'>下载当天所有线下E邮宝的打印标签(<font color='blue'>共<?php echo $confirmed_ems_count; ?>个</font>)</font></a>
    <br/><br/><a href="<?=$download_part_pdf_url?>"><font color='blue'>下载当天未下载过的线下E邮宝打印标签(<font color='blue'>共<?php echo $part_confirmed_ems_count; ?>个</font>)</font></a>
    <br/><br/><br/>
    <font color='green'>点击任何一天下载打印标签</font>
    <?php
        $data = array(
            'uri' => 'shipping/epacket_ems/download_pdf'
        );
    ?>
    <?php echo $calendar->generate($year, $month, $data); ?>
    <br/><br/><br/>
    
    <font color='green'>快捷下载PDF</font>
    <?php $track_number_download_part_pdf_url = site_url('shipping/epacket/track_numberdownload_part_pdf'); ?><br/><br/><br/>
<form id="form1" name="form1" method="post" action="<?=$track_number_download_part_pdf_url?>">
  <label>
    跟踪号<input type="text" name="track_number" id="track_number" />
  </label>
  <label>
    <input type="submit" name="button" id="button" value="提交" />
  </label>
</form>
<br/><br/><br/>




    <?php
    $CI = & get_instance();
    if (count($unconfirmed_orders)) {
        $data = array();
        $head = array(
            '订单号',
            'Paypal交易号',
            '提示信息',
            '输入人',
            '操作',
        );
        $error = '订单信息有误，请检查订单金额,paypal email, item id等';
        foreach ($unconfirmed_orders as $unconfirmed_order) {
            $input_name = $CI->user_model->fetch_user_login_name_by_id($unconfirmed_order->input_user);
            $data[] = array(
                $unconfirmed_order->item_no,
                $unconfirmed_order->transaction_id,
                (strpos($unconfirmed_order->message, 'SoapFault exception') !== false) ? $error : $unconfirmed_order->message,
                $input_name,
                anchor(site_url('shipping/deliver_management/remove_order_from_epacket', array($unconfirmed_order->id)), '从E邮宝中删除该信息'),
            );
        }
    echo block_table($head, $data);
    }
    if (count($print_no_confirmed)) {
        $data = array();
        $head = array(
            '订单号',
            'Paypal交易号',
            '追踪号',
        );
        $error = '订单信息有误，请检查订单金额,paypal email, item id等';
        foreach ($print_no_confirmed as $print_no_confirmed) {
            $data[] = array(
                $print_no_confirmed->id."<br>".$print_no_confirmed->item_no,
                $print_no_confirmed->transaction_id,
                $print_no_confirmed->track_number,
            );
        }
    $title = lang('printed_not_confirmed');
    echo block_header($title);
    echo block_table($head, $data);
    }
	if (count($undownload_ems_orders)) {
        $data = array();
        $head = array(
            '线下EUB未下载订单号',
            '交易号',
			'跟踪号',
            '确认人',
			'确认时间',
        );
        $error = '订单信息有误，请检查订单金额,paypal email, item id等';
        foreach ($undownload_ems_orders as $undownload_ems_order) {
            $input_name = $CI->user_model->fetch_user_login_name_by_id($undownload_ems_order->input_user);
            $data[] = array(
                $undownload_ems_order->id."<br>".$undownload_ems_order->item_no,
                $undownload_ems_order->transaction_id,
				$undownload_ems_order->track_number,
                $undownload_ems_order->check_user,
				$undownload_ems_order->check_date,
            );
        }
    echo block_table($head, $data);
    }
	
	if (count($undownload_orders)) {
        $data = array();
        $head = array(
            '线上EUB未下载订单号',
            'Paypal交易号',
			'跟踪号',
            '提示信息',
            '确认人',
			'确认时间',
            '操作',
        );
        $error = '订单信息有误，请检查订单金额,paypal email, item id等';
        foreach ($undownload_orders as $unconfirmed_order) {
            $input_name = $CI->user_model->fetch_user_login_name_by_id($unconfirmed_order->input_user);
            $data[] = array(
                $unconfirmed_order->id."<br>".$unconfirmed_order->item_no,
                $unconfirmed_order->transaction_id,
				$unconfirmed_order->track_number,
                (strpos($unconfirmed_order->message, 'SoapFault exception') !== false) ? $error : $unconfirmed_order->message,
                $unconfirmed_order->check_user,
				$unconfirmed_order->check_date,
                anchor(site_url('shipping/deliver_management/remove_order_from_epacket', array($unconfirmed_order->id)), '从E邮宝中删除该信息'),
            );
        }
    echo block_table($head, $data);
    }
    ?>

</center>