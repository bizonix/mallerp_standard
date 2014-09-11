<?php
require_once APPPATH . 'controllers/mallerp_no_key' . EXT;
class Epacket_specification extends Mallerp_no_key
{
    private $user;
    private $token;
	private $is_register;
    protected $order_statuses = array();
	protected $sender_info = array();
	protected $collect_info = array();
    public function __construct()
    {
        parent::__construct();

        $this->user = 'Mallerp';
        $this->config->load('config_epacket_specification');
        $this->load->model('order_model');
		$this->load->model('product_model');
		$this->load->model('product_makeup_sku_model');
		$this->load->model('epacket_model');
		$this->load->library('script');
		$this->load->library('fileutil');
		$this->is_register=(!empty($this->is_register))?$this->is_register:'EUB';
		$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($this->is_register);
		$this->sender_info['sender_name']=$epacket_config->shipfromaddress_contact;
		$this->sender_info['sender_postcode']=$epacket_config->shipfromaddress_postcode;
		$this->sender_info['sender_phone']=$epacket_config->shipfromaddress_mobile;
		$this->sender_info['sender_mobile']=$epacket_config->shipfromaddress_mobile;
		$this->sender_info['sender_province']=$epacket_config->shipfromaddress_province;
		$this->sender_info['sender_city']=$epacket_config->shipfromaddress_city;
		$this->sender_info['sender_county']=$epacket_config->shipfromaddress_district;
		$this->sender_info['sender_company']=$epacket_config->shipfromaddress_company;
		$this->sender_info['sender_street']=$epacket_config->shipfromaddress_street;
		$this->sender_info['sender_email']=$epacket_config->shipfromaddress_email;
		
		$this->collect_info['collect_name']=$epacket_config->pickupaddress_contact;
		$this->collect_info['collect_postcode']=$epacket_config->pickupaddress_postcode;
		$this->collect_info['collect_phone']=$epacket_config->pickupaddress_phone;
		$this->collect_info['collect_mobile']=$epacket_config->pickupaddress_mobile;
		$this->collect_info['collect_province']=$epacket_config->pickupaddress_province;
		$this->collect_info['collect_city']=$epacket_config->pickupaddress_city;
		$this->collect_info['collect_county']=$epacket_config->pickupaddress_district;
		$this->collect_info['collect_company']=$epacket_config->pickupaddress_company;
		$this->collect_info['collect_street']=$epacket_config->pickupaddress_street;
		$this->collect_info['collect_email']=$epacket_config->pickupaddress_email;
		
		
        $this->load->helper('url');
		$this->load->helper('epacket_specification');
        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }
	public function get_epacket_track_number($order_id)
    {
        $order = $this->order_model->get_order($order_id);
        if (!isset($order->id))
        {
            return false;
        }
        $this->_process_add_order($order);
    }
	private function _process_add_order($data)
	{
		$order_id = $data->id;
        $track_code = $this->epacket_model->get_ems_track_number($order_id);
        if ($track_code)
        {
            //return $this->_process_print_label($track_code, $transaction_id, $order_id);
			$user_name=lang('program');
			$wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
			$remark = $this->order_model->get_sys_remark($order_id);
			$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);
			$data1 = array(
                            'track_number' => $track_code,
                            'ship_confirm_date' => date('Y-m-d H:i:s'),
                            'order_status' => $wait_for_feedback_status,
                            'sys_remark' => $remark . ' epacket: order status id is ' . $wait_for_feedback_status,
				);
			$this->order_model->update_order_information($order_id, $data1);
		
			$this->epacket_model->save_specification_track_number($track_code,$data->id);
			$this->get_epacket_specification_print_lable_url($track_code);
			if ($this->_check_label_exists($track_code))
			{
				$this->print_sku_list($track_code,$order_id);/*标签文件下载成功后才生成清单*/
			}
			
			return ;
        }
        //$product_mode = FALSE;
        $this->CI = & get_instance();
		$this->CI->load->config('config_epacket_specification');
        $ems_token = $this->CI->config->item('ems_token');
        $ems_url = $this->CI->config->item('ems_url');
		$ems_label_url = $this->CI->config->item('ems_label_url');
		$version = $this->CI->config->item('version');
		
		$order_info=array();
		$receiver_info=array();
		$items=array();
		
		$skus = explode(',', trim($data->sku_str, ','));
        $item_ids = explode(',', trim($data->item_id_str, ','));
        $item_titles = explode(ITEM_TITLE_SEP, trim($data->item_title_str, ','));
        $qties = explode(',', trim($data->qty_str, ','));
		$i = 0;
		$volweight=0;
		$item_count = count($skus);
		/*$price = ( ! empty($data->gross)) ? ($data->gross / 2) : ($data->net / 2);
        $price = $price / $item_count;*/
		$price = 5;
        $weight = ($data->ship_weight / 1000) / $item_count;
		if(empty($weight)){$weight=0.05;}
		echo "weight:".$weight."\n";
		
		foreach ($skus as $sku)
        {
			$item=array();
			$item_title = $item_titles[$i];
            $qty = $qties[$i];
			if ($this->order_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
			{
				$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
				$sku_arr=explode(',', $makeup_sku->sku);
				$qty_arr=explode(',', $makeup_sku->qty);
				foreach($sku_arr as $key=>$value)
				{
					$count_sku=(int)$qty*$qty_arr[$key];
					$product = $this->epacket_model->get_product_info_for_epacket($value);
					$item['cnname']=$product->name_cn;
					$item['enname']="Accessories ";
					$item['count']=$count_sku;
					$item['weight']=(integer)$weight;
					$item['delcarevalue']=price($price);
					$items[]=$item;
					$volweight+=$weight;
				}
			}else{
				$product = $this->epacket_model->get_product_info_for_epacket($sku);
				$item['cnname']=$product->name_cn;
				$item['enname']="Accessories ";
				$item['count']=$qty;
				$item['weight']=(integer)$weight;
				$item['delcarevalue']=price($price);
				$items[]=$item;
				$volweight+=$weight;
			}
            
			$i++;
		}
		
		$datetime=date('Y-m-d\TH:i:s\Z');
		$end_time=date('Y-m-d\TH:i:s\Z',mktime(substr($datetime,11,2),substr($datetime,14,2),substr($datetime,17,2),substr($datetime,5,2),substr($datetime,8,2)+4,substr($datetime,0,4)));
		
		$ems_token_array = explode('_', trim($ems_token));
		
		$order_info['orderid']=str_pad($data->id,4,'0',STR_PAD_LEFT);
		$order_info['customercode']=$ems_token_array[0];
		$order_info['volweight']=(integer)$volweight;
		$order_info['startdate']=$datetime;
		$order_info['enddate']=$end_time;
		
		$street = '';
        $street = empty($street) ? $data->address_line_1 : $street . ', ' . $data->address_line_1;
		if(!empty($data->address_line_2))
		{
			$street=$street.','.$data->address_line_2;
		}
		$receiver_info['receiver_name']=htmlspecialchars($data->name);
		$receiver_info['receiver_postcode']=$data->zip_code ? trim($data->zip_code) : ' ';
		$receiver_info['receiver_phone']=$data->contact_phone_number ? $data->contact_phone_number : ' ';
		$receiver_info['receiver_mobile']=$data->contact_phone_number ? $data->contact_phone_number : ' ';
		$receiver_info['receiver_country']=get_country_code($data->country);
		$receiver_info['receiver_province']=$data->state_province ? $data->state_province : ' ';
		$receiver_info['receiver_city']=$data->town_city;
		$receiver_info['receiver_county']='';
		$receiver_info['receiver_street']=$street;
		
		

        $xml = epacket_specification_order($order_info,$this->sender_info,$receiver_info,$this->collect_info,$items);
        $order_gateway_url = $ems_url."order/";
		var_dump($order_gateway_url);
		echo "\n";
		var_dump($xml);
		echo "\n";
		//echo "<pre>";var_dump($xml);echo "</pre>";die("***********************");
		//var_dump(utf8_encode($xml));
		//die($order_gateway_url);
        /* 开始提交xml了 */
		//$xml=utf8_encode($xml);
        $ch = curl_init();
        $header[] = "Content-type: text/xml"; //定义content-type为xml
		$header[] = "version: ".$version;
		$header[] = "authenticate: ".$ems_token;
        curl_setopt($ch, CURLOPT_URL, $order_gateway_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
		var_dump($response);
        $arr = parseNamespaceXml($response);
		//echo "<br>**********************<br>";
		//print_r($arr);
		$user_name=lang('program');
		$track_code=isset($arr['mailnum'])?$arr['mailnum']:'';
		$wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
		$remark = $this->order_model->get_sys_remark($order_id);
		$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);
		$data1 = array(
                            'track_number' => $track_code,
                            'ship_confirm_date' => date('Y-m-d H:i:s'),
                            'order_status' => $wait_for_feedback_status,
                            'sys_remark' => $remark . ' epacket: order status id is ' . $wait_for_feedback_status,
				);
		$this->order_model->update_order_information($order_id, $data1);
		
		$this->epacket_model->save_specification_track_number($track_code,$data->id);
		$this->get_epacket_specification_print_lable_url($track_code);
		if ($this->_check_label_exists($track_code))
		{
			$this->print_sku_list($track_code,$order_id);/*标签文件下载成功后才生成清单*/
		}
	}
	public function print_sku_list($track_code,$order_id)
	{
		echo $track_code."start!\n";
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );
		$order = $this->order_model->get_order($order_id);
		$skus = explode(',', trim($order->sku_str, ','));
        $qties = explode(',', trim($order->qty_str, ','));
			$temp_skus=array();
			$temp_qties=array();
			foreach($skus as $i=>$sku)
			{
				if ($this->order_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($skus[$i]);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $key=>$value)
					{
						$temp_skus[]=$value;
						$count_sku=(int)$qties[$i]*$qty_arr[$key];
						$temp_qties[]=$count_sku;
					}
				}else
				{
					$temp_skus[]=$sku;
					$temp_qties[]=$qties[$i];
				}
			}
			$skus = $temp_skus;
            $qties = $temp_qties;
		if(count($skus)>0)
		{
			$CI = & get_instance();
			$width = 100;
			$height = 100;
			$pagelayout = array($width,$height);
			$my_tcpdf['page_format'] = $pagelayout;
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetMargins(0, 0, 0);
			$page_index=0;
			$print_date = date('Y.m.d');
			foreach ($skus as $key => $sku) {
				if($key%13==0)
				{
					$this->pdf->AddPage();
					$page_index++;
					$htmllink_uppage = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:11;">{$track_code}清单：order id:{$order_id},本页是第{$page_index}页</span>
EOD;
					$htmlprint_date = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">日期:{$print_date}</span>
EOD;
					$this->pdf->writeHTMLCell($w = 90, $h = 8, $x = 5, $y = 3, $htmllink_uppage, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 40, $h = 3, $x = 2, $y =95, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				}
				
				$no = $key%13+1;
				$num_no = $key + 1;
				$sql1 = 'name_cn,shelf_code';
				$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
				$htmlproduct_list ='';
				$htmlproduct_list = <<<EOD
<span style="white-space:nowrap;font-size:9;">({$num_no}) {$myproduct->shelf_code}-{$sku}-{$myproduct->name_cn}*{$qties[$key]}</span>
EOD;
				$this->pdf->writeHTMLCell($w = 98, $h = 3, $x = 1, $y = 10 + 5 * $no, $htmlproduct_list, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				
			}
			if($order->note!='')
			{
				$order_note = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:11;">NOTE：{$order->note}</span>
EOD;
				$this->pdf->writeHTMLCell($w = 96, $h = 3, $x = 2, $y =85, $order_note, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
			}

			$this->CI = & get_instance();
		$this->CI->load->config('config_epacket_specification');
		$pdf_path = $this->CI->config->item('pdf_path');
		$this->db->select('*');
        $this->db->from('specification_epacket_confirm_list');
		$this->db->where('track_number', $track_code);
        $query = $this->db->get();
		$results=$query->result();
		if(count($results)>1)
		{
			//echo $pdf_path."********";
			$logname = '/var/www/html/log/ems/';
		if (!file_exists($logname))
        {
            mkdir($logname);
        }
		$logname .= date('Y-m-d').'.log';
		$requestInformation="track_code only one.but ".$track_code.' is '.count($results).',pls check database!';
		writefile($logname, $requestInformation, 'a');
			return false;
			
		}
		foreach($results as $result)
		{
			$sub_path = explode(' ',$result->input_date);
			$pdf_path = $pdf_path . $sub_path[0];
			
			
		}
			$filename = $pdf_path . "/sku_list_" . $track_code . ".pdf";
			$this->pdf->Output($filename, 'F');
		}else{
			return;
		}
	}
	public function get_epacket_specification_print_lable_url($track_code)
	{
        //$product_mode = FALSE;
        $this->CI = & get_instance();
		$this->CI->load->config('config_epacket_specification');
        $ems_token = $this->CI->config->item('ems_token');
        $ems_url = $this->CI->config->item('ems_url');
		$ems_label_url = $this->CI->config->item('ems_label_url');
		$version = $this->CI->config->item('version');
		
		$orders=array($track_code);

        $xml = epacket_specification_print_lable($orders);
        $order_gateway_url = $ems_url."print/batch/";
		var_dump($order_gateway_url);
		var_dump($xml);
		//var_dump(utf8_encode($xml));
		//die($order_gateway_url);
        /* 开始提交xml了 */
        $ch = curl_init();
        $header[] = "Content-type: text/xml"; //定义content-type为xml
		$header[] = "version: ".$version;
		$header[] = "authenticate: ".$ems_token;
        curl_setopt($ch, CURLOPT_URL, $order_gateway_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
		var_dump($response);
        $arr = parseNamespaceXml($response);
		$download_url=isset($arr['description'])?$arr['description']:'';
		$this->epacket_model->save_specification_download_url($download_url,$track_code);
		
		echo "starting confirm packet\n";
		
		$order = $this->epacket_model->get_specification_epacket_confirm_list_with_track_number($track_code);
		if (!isset($order->lable_download_url))
        {
            return false;
        }
        $this->_print_label($order);
	}
	
	public function print_label($order_id)
    {
        $order = $this->epacket_model->get_specification_epacket_confirm_list_with_order_id($order_id);
        if (!isset($order->lable_download_url))
        {
            return false;
        }
        $this->_print_label($order);
    }
	private function _print_label($order)
	{
		$pdf_path = $this->_pdf_path();
		$url = $order->lable_download_url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$res = curl_exec($ch);
		//$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch) ;
		/*$wgetshell='wget -O '.$pdf_path. '/' . $order->track_number . '.zip'.' -c "'.$url.'" ';
		echo $wgetshell;
		shell_exec($wgetshell);*/
		file_put_contents($pdf_path . '/' . $order->track_number . '.zip',$res);//write
		$pdf_tmp_path=$pdf_path . '/tmp/';
		
		$this->fileutil->zipFile($pdf_path . '/' . $order->track_number . '.zip',$pdf_tmp_path,false);
		//rename($pdf_tmp_path . '4_4/' . $order->track_number . '.pdf',$pdf_path. $order->track_number . '.pdf');
		$this->fileutil->moveFile($pdf_tmp_path . '4_4/' . $order->track_number . '.pdf',$pdf_path . '/' . $order->track_number . '.pdf',true);
		$filesize=abs(filesize($pdf_path . '/' . $order->track_number . '.zip'));
		if($filesize>1024)
		{
			$this->epacket_model->update_ems_print_label($order->track_number);
		}
		
	}
	private function _pdf_path()
    {
        $this->CI = & get_instance();
		$this->CI->load->config('config_epacket_specification');
        $pdf_path = $this->CI->config->item('pdf_path');
        $sub_path = date('Y-m-d');
        $full_path = $pdf_path . $sub_path;
        if (!file_exists($full_path))
        {
            mkdir($full_path);
        }
		if (!file_exists($full_path.'/tmp/'))
        {
			mkdir($full_path.'/tmp/');
        }
        return $full_path;
    }

	private function _check_label_exists($track_number)
    {
        $status = $this->epacket_model->get_specification_print_label_status($track_number);
        $pdf_file = $this->_pdf_path() . '/' . $track_number . '.pdf';
        if ($status && file_exists($pdf_file))
        {
            return true;
        }
        return false;
    }

	private function _check_old_label_exists($track_number)
    {
		$this->CI = & get_instance();
		$this->CI->load->config('config_epacket_specification');
		$pdf_path = $this->CI->config->item('pdf_path');
        $status = $this->epacket_model->get_specification_print_label_status($track_number);
		
		$this->db->select('specification_epacket_confirm_list.*');
        $this->db->from('specification_epacket_confirm_list');
		$this->db->where('specification_epacket_confirm_list.track_number', $track_number);
        $query = $this->db->get();
		$results=$query->result();
		foreach($results as $result)
		{
			$sub_path = explode(' ',$result->input_date);
			$pdf_file = $pdf_path . $sub_path[0].'/' . $track_number . '.pdf';
			
		}
		
        if ($status && file_exists($pdf_file))
        {
            return true;
        }
        return false;
    }
	public function check_sku_list($date)
    {
        $this->db->select('specification_epacket_confirm_list.*');
        $this->db->from('specification_epacket_confirm_list');
		$this->db->where('specification_epacket_confirm_list.print_label', 1);
		$this->db->where('specification_epacket_confirm_list.sku_list', 0);
		$this->db->like('input_date', $date);
		$this->db->limit(1);
        $query = $this->db->get();
		$results=$query->result();
		//echo $this->db->last_query();
		foreach($results as $result)
		{
			//if($result->track_number=='LN243191890CN'){
			if ($this->_check_old_label_exists($result->track_number))
			{
				//$this->print_sku_list($track_code,$order_id);/*标签文件下载成功后才生成清单*/
				
				$this->print_sku_list($result->track_number,$result->order_id);
				
				echo $result->track_number." ok\n";
				$this->db->where('track_number', $result->track_number);
		$this->db->update('specification_epacket_confirm_list', array('sku_list'=>1));
				//sleep(5);
			}
			
			//die("****");
			//}
			
			
		}
    }
}
?>