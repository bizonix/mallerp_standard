<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
class Wish_order extends Mallerp_no_key
{

    private $start_time;
    private $end_time;
	private $username;
	private $password;
	private $merchant_name;
	private $wish_url;
	private $wish_url_ssl;
	private $wget_path;
    public function __construct()
    {
        parent::__construct();

        $this->lang->load('mallerp', DEFAULT_LANGUAGE);
        $this->load->model('order_model');
		$this->load->model('sale_model');
        $this->load->model('order_role_model');
        $this->load->model('product_model');
		$this->load->model('mixture_model');
		$this->load->model('ebay_order_model');
        $this->load->helper('order');
        $this->load->helper('shipping');
		$this->load->config('config_wish');

		$this->username = '';
		$this->password = '';
		$this->merchant_name = '';
		$this->wish_shop = $this->config->item('wish_shop');
		$this->wish_url = $this->config->item('wish_url');
		$this->wish_url_ssl = $this->config->item('wish_url_ssl');
		$this->wget_path = $this->config->item('wget_path');

        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }

        if (!session_id()) {
            session_start();
        }

        set_time_limit(0);
		date_default_timezone_set(DEFAULT_TIMEZONE);
    }
	private function _make_wget_path()
    {
        $sub_path = date('Y-m-d');
        $full_path = $this->wget_path . $sub_path;
        if (!file_exists($full_path))
        {
            mkdir($full_path);
        }
        return $full_path;
    }



	public function import_wish_orders()
	{
		$start_date='06/01/2013 00:00';
		$end_date='06/30/2013 00:00';
		$action='export';
		
		foreach($this->wish_shop as $shop)
		{
			$page=0;
			$all_pages=0;
			$this->username = $shop['username'];
			$this->password = $shop['password'];
			$this->merchant_name = $shop['merchant_name'];
			$formTime=$this->order_model->get_wish_order_begin_time();
			$toTime=date('m/d/Y H:i',mktime(substr($formTime,11,2)+24,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));
			$updatetoTime=date('Y-m-d\TH:i:00\Z',mktime(substr($formTime,11,2)+24,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));/*时间要和上面保持一致*/
			$formTime=date('m/d/Y H:i',mktime(substr($formTime,11,2)-24*20,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));
			$startdate=strtotime($toTime);
			$enddate=strtotime(get_utc_time('-5 minutes'));
			if($enddate-$startdate<=0){
				$toTime = get_utc_time('-5 minutes');
				$toTime=date('m/d/Y H:i',mktime(substr($toTime,11,2),substr($toTime,14,2),substr($toTime,17,2),substr($toTime,5,2),substr($toTime,8,2),substr($toTime,0,4)));
				$updatetoTime=get_utc_time('-5 minutes');
			}
			$start_date=$formTime;
			$end_date=$toTime;
			echo $this->username.":".$start_date."----".$end_date."\n";
			do{
				if($page<1){$page=1;}
				$order_gateway_url = $this->wish_url_ssl."://".$this->username.":".$this->password."@".$this->wish_url.$this->merchant_name."?action=".$action."&page=".$page."&start_date=".$start_date."&end_date=".$end_date."&manual=1";
				$realpath=$this->_make_wget_path()."/wish_order_xml_".$page.".xml";
				$wgetshell='wget -O '.$realpath.' "'.$order_gateway_url.'" ';
				echo $wgetshell;
				shell_exec($wgetshell);
				if($page==1)
				{
					$xml = simplexml_load_file($realpath);
					$all_pages= $xml['pages'][0];
				}
				//echo $all_pages."\n";
				echo $order_gateway_url."\n";
				$arr = parseNamespaceXml(file_get_contents($realpath));
				$page++;
				//var_dump($arr);
				if(!isset($arr['Order']))
				{
					$this->order_model->update_wish_order_begin_time($updatetoTime);
					continue;
				}
				if(count($arr['Order'])==0)
				{
					$this->order_model->update_wish_order_begin_time($updatetoTime);
					continue;
				}
			
				foreach($arr['Order']as $Order)
				{
					echo $Order['OrderID']."|".date("Y-m-d H:i:s",strtotime($Order['LastModified']))."\n";
					if($Order['OrderStatus']!='APPROVED')
					{
						continue;
					}
					if($Order['OrderID']!='')
					{
						$data=$this->_make_common_order_list_data($Order);
					}else{
						continue;
					}
					if($data['created_at']=='')
					{
						continue;
					}
				
					print_r($data);
					//echo "Order ID: ", $order['increment_id'], "\n";
					if ($this->order_model->check_exists('order_list', array('transaction_id' => $data['transaction_id'],'input_from_row'=>$data['input_from_row']))) {
						/*
						$data1=array(
								 'address_line_1'=>$data['address_line_1'],
								 'address_line_2'=>$data['address_line_2'],
								 );
						$this->order_model->update_order_by_item_no($data['item_no'],$data1);*/
					}else{
						$order_id = $this->order_model->add_order($data);
					}
					echo "\n*************\n";//die();
				}//end foreach
			}while($page<=$all_pages);
			$this->order_model->update_wish_order_begin_time($updatetoTime);
		}//结束帐号循环
		
	}
	private function _make_common_order_list_data($transaction_details)
    {
		//var_dump($transaction_details);
		$ship_to_country = $this->mixture_model->get_country_name_in_english_by_code(strtoupper($transaction_details['Customer']['ShipTo']['Country']));
		$import_date=date('Y-m-d H:i:s');
		$item_titles=array();
		$item_ids=array();
		$item_qties=array();
		$item_codes=array();
		$item_price=array();
		//var_dump($arr['Products']['Product']);
		$gross_tmp=0;

		foreach($transaction_details['Items'] as $product)
		{
				//var_dump($product);die();
				//echo $key.":".$value."\n";
				$item_titles[] = trim($product['Name']);
				$item_qties[] = (int)$product['Quantity'];
				//$item_ids[] = (int)$product['Id'];
				$item_codes[] = trim($product['SKU']);
				$sql1 = 'sale_price';
				$myproduct = $this->product_model->fetch_product_by_sku(trim($product['SKU']), $sql1);
				$item_price[] = price($myproduct->sale_price);
				$gross_tmp+=price($myproduct->sale_price)*(int)$product['Quantity'];
			
		}
		$sp_country=array('AUSTRALIA','ITALY','SWITZERLAND','JAPAN','MALAYSIA','SWEDEN','THAILAND','NETHERLANDS','CANADA','NEW ZEALAND','NORWAY');
		$sp_country1=array('');
		$is_register='CHR';
		if(strtoupper($ship_to_country)=='UNITED STATES')
		{
			$is_register='EUB';
		}
		if(in_array(strtoupper($ship_to_country),$sp_country))
		{
			$is_register='SGR';
		}
		if(in_array(strtoupper($ship_to_country),$sp_country1))
		{
			$is_register='SGR';
		}
		
		$auction_sites=explode(" ",preg_replace("/\s/"," ",$transaction_details['Source']));
		$item_no=$transaction_details['OrderNumber'];
		$sys_remark = $this->_create_sys_remark($import_date,$item_no);
		//echo date("Y-m-d H:i:s",strtotime($transaction_details['OrderDate']));
        $data = array(
                    'time_zone'                 => 'PDT',
					'created_at'				=> date("Y-m-d H:i:s",strtotime($transaction_details['OrderDate'])),
					'payment_status'			=> 'NONE',
                    'name'                      => $transaction_details['Customer']['ShipTo']['Name'],
                    'item_title_str'            => implode(ITEM_TITLE_SEP, $item_titles),
                    //'item_id_str'				=> implode(',', $item_ids),
                    'qty_str'                   => implode(',', $item_qties),
					'sku_str'                   => implode(',', $item_codes),
					'item_price_str'			=> implode(',', $item_price),
                    'currency'                  => 'USD',
                    //'gross'                     => $transaction_details['OrderTotal'],
					'gross'                     => price($gross_tmp*0.966),
                    'fee'                       => price($gross_tmp*0.034),
                    'net'                       => price($gross_tmp*0.966),
					'shippingamt'               => $transaction_details['ShippingAmount'],
					'shipping_cost'             => 1.99,
                    'note'                      => '',
                    'from_email'                => '',
                    'to_email'                  => 'screamprice',
                    'transaction_id'            => (isset($transaction_details['OrderNumber']) && !empty($transaction_details['OrderNumber']))?$transaction_details['OrderNumber']:'',
					'item_no'					=> $item_no,
					'is_register'				=> $is_register,
                    'payment_type'              => '',
                    'counterparty_status'       => '',
                    'address_status'            => '',
                    'shipping_handling_amount'  => '',
                    'insurance_amount'          => 0,
                    'sales_tax'                 => '',
                    'auction_site'              => 'WS',
					'auction_site_type'			=> 'wish',
                    'buyer_id'                  => $transaction_details['Customer']['CustomerCode'],
                    'item_url'                  => '',
                    'closing_date'              => '',
                    'reference_txn_id'          => '',
                    'invoice_number'            => '',
                    'subscription_number'       => '',
                    'custom_number'             => '',
                    'receipt_id'                => '',
                    'sys_remark'                => $sys_remark,
                    'address_line_1'            => preg_replace("/\s/"," ",$transaction_details['Customer']['ShipTo']['Address1']),
                    'address_line_2'            => preg_replace("/\s/"," ",$transaction_details['Customer']['ShipTo']['Address2']),
                    'town_city'                 => $transaction_details['Customer']['ShipTo']['City'],
                    'state_province'            => (isset($transaction_details['Customer']['ShipTo']['State'])&&!empty($transaction_details['Customer']['ShipTo']['State']))?$transaction_details['Customer']['ShipTo']['State']:'',
                    'zip_code'                  => $transaction_details['Customer']['ShipTo']['PostalCode'],
                    'country'                   => $ship_to_country,
                    'contact_phone_number'      => (isset($transaction_details['Customer']['ShipTo']['Phone']) && !empty($transaction_details['Customer']['ShipTo']['Phone']))?$transaction_details['Customer']['ShipTo']['Phone']:'',
                    'balance_impact'            => '',
                    'income_type'               => '',
                    'input_date'                => $import_date,
                    'input_from_row'            => $transaction_details['OrderID'],
                    'paid_time'                 => '',
					'order_status'				=> $this->order_statuses['wait_for_confirmation'],
					'domain'					=> 'wish',
					'wish_id'					=> $this->username,
                );
				foreach(explode(',', $data['sku_str']) as $sku)
				{
					if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
								$data['order_status']=$this->order_statuses['wait_for_confirmation'];
					}
				}
				if ($this->order_model->check_exists('order_list', array('item_no' => $data['item_no']))) {
					$data['is_duplicated']=1;
					$data['order_status']=$this->order_statuses['wait_for_confirmation'];
				}
		//var_dump($item_titles);
		//var_dump($item_qties);
        return $data;
    }



	private function _create_sys_remark($import_date,$item_no)
    {
       
        $sys_remark = <<<DATA
{$import_date}由程序导入本订单(Wish自动导单),编号为:{$item_no};
DATA;
        
        return $sys_remark;
    }

	
	public function auto_merged_wish_order()
	{
		echo "start auto_merged_wish_order \n";
		$orders=$this->get_wish_orders();
		foreach($orders as $order)
		{
			//echo $order->transaction_id."\n";
			$to_merged_id = $this->can_merge_order($order->transaction_id, $order->input_from_row);
			if ($to_merged_id && $this->_is_items_good($order))
			{
				echo $order->transaction_id." can merge\n";
				$this->_merge_order($to_merged_id,$order);
                $to_merged_id = false;
				sleep(3);
			}
		}
	}
	private function _merge_order($to_merged_id,$order)
	{
		$order_obj = $this->get_order_info_for_merge($to_merged_id);
        if ( ! $order_obj)
        {
            return false;
        }
		$new_order=$this->order_model->get_order($order->id);
		$gross = $order_obj->gross;
        $fee = $order_obj->fee;
		$shippingamt = $order_obj->shippingamt;
        $currency = $order_obj->currency;
        $item_title = $order_obj->item_title_str;
        $item_id = $order_obj->item_id_str;
        $sm_products_code = $order_obj->sku_str;
        $amount = $order_obj->qty_str;
		$item_no= $order_obj->item_no;
		$is_register= $order_obj->is_register;
		$item_info = array(
                         'item_titles' => array_merge(explode(ITEM_TITLE_SEP, $item_title), explode(ITEM_TITLE_SEP, $new_order->item_title_str)),
                         'item_ids' => array_merge(explode(',', $item_id), explode(',', $new_order->item_id_str)),
                         'item_codes' => array_merge(explode(',', $sm_products_code), explode(',', $new_order->sku_str)),
                         'item_qties' => array_merge(explode(',', $amount), explode(',', $new_order->qty_str))
                     );
		$new_gross = $gross + $new_order->gross;
        $new_fee = $fee + $new_order->fee;
		$new_shippingamt=$shippingamt+$new_order->shippingamt;
		$new_net = $new_gross - $new_fee;
		$new_title = implode(ITEM_TITLE_SEP, $item_info['item_titles']);
        $new_id = implode(',', $item_info['item_ids']);
        $new_sm_products_code = implode(',', $item_info['item_codes']);
        $new_amount = implode(',', $item_info['item_qties']);
		
		$order_status = fetch_status_id('order_status', 'wait_for_confirmation');
		$data = array(
                    'gross'              => $new_gross,
                    'fee'                => $new_fee,
                    'net'                => $new_net,
					'shippingamt'        => $new_shippingamt,
                    'item_title_str'     => $new_title,
                    'item_id_str'        => $new_id,
                    'sku_str'            => $new_sm_products_code,
                    'qty_str'            => $new_amount,
					'order_status'		 => $order_status,
                );
		$sys_remark = $this->merged_create_sys_remark($new_order);
		//var_dump($data);
		$this->merge_order($to_merged_id, $data, $sys_remark,$new_order);
	}
	public function merge_order($oid, $data, $sys_remark,$order) {
        $input_from_row = $order->input_from_row;
		$closed_status = fetch_status_id('order_status', 'closed');
        
        if ($this->order_model->check_exists('order_list', array('transaction_id' => $order->transaction_id,'input_from_row'=>$input_from_row,'order_status'=>$closed_status))) {
            return ;
        }
        $this->db->trans_start();
        $this->db->where('id', $oid);
        $this->db->update('order_list', $data);

        $sql="update order_list set sys_remark = CONCAT(sys_remark, {$this->db->escape($sys_remark)}) where id = $oid";
        $query = $this->db->query($sql);
		$close_sys_remark="Order are merged into".$oid;
        $this->db->insert('order_merged_list', array('transaction_id' => $input_from_row,'order_id'=>$oid));
		$sql="update order_list set order_status =$closed_status,sys_remark = CONCAT(sys_remark, {$this->db->escape($close_sys_remark)}) where id = ".$order->id;
        $query = $this->db->query($sql);
		echo $input_from_row. $close_sys_remark."  \n";
        $this->db->trans_complete();
    }
	private function merged_create_sys_remark($order)
    {
        $import_date = date("Y-m-d H:i:s");
        $code = implode(',',explode(',', $order->sku_str));
        $title = implode(ITEM_TITLE_SEP, explode(',', $order->item_title_str));
        $qty = implode(',',explode(',', $order->qty_str));

        $merged_order = <<<DATA
$import_date 由系统判断 {$order->transaction_id} 是重复订单，自动合并。增加商品 {$code};
数量分别为 {$qty}; input_from_row为{$order->input_from_row}; item_title加上了 {$title};\n
订单为合并订单(wish自动合并);\n
DATA;



        return $merged_order ;
    }
	public function get_order_info_for_merge($oid) {
        $this->db->select('gross, fee, currency, item_title_str, item_id_str, sku_str, qty_str,item_no,is_register,shippingamt');
        $this->db->where(array('id' => $oid));
        $query = $this->db->get('order_list');                
        $row = $query->row();
        
        return $row;
    }
	private function _is_items_good($order)
	{
		$skus = explode(',', $order->sku_str);
		$qties = explode(',', $order->qty_str);
		$item_titles = explode(ITEM_TITLE_SEP, $order->item_title_str);
		if(count($skus)==count($qties) && count($skus)==count($item_titles))
		{
			if (in_array('', $skus)){return false;}
			return true;
		}else{
			return false;
		}
	}
	public function can_merge_order($transaction_id, $input_from_row) {
		$wait_for_shipping_label_status = fetch_status_id('order_status', 'wait_for_shipping_label');
		$closed_status = fetch_status_id('order_status', 'closed');
		
		$this->db->select('order_list.id as o_id');
        $this->db->from('order_list');
        $this->db->where(array(
            'order_status <='    => $wait_for_shipping_label_status,
			'order_status !='    => $closed_status,
            'transaction_id' => $transaction_id,
			'input_from_row !='=> $input_from_row,
        ));
        
        $query = $this->db->get();
        $row = $query->row();
		return isset($row) && isset($row->o_id) ? $row->o_id : FALSE;
	}
	public function get_wish_orders() {
		$wait_for_confirmation_id = fetch_status_id('order_status', 'wait_for_confirmation');
        $this->db->select('*');
        $this->db->from('order_list');
		$this->db->where('auction_site_type', 'wish');
		$this->db->where(array('auction_site_type' => 'wish','order_status'=>$wait_for_confirmation_id));
        $query = $this->db->get();
        return $query->result();
    }

	public function complete_sale($order_id)
	{
		$order = $this->order_model->get_order($order_id);
		$OrderNumber=$order->transaction_id;
		$OrderID=$order->input_from_row;
		$buyer_id = $order->buyer_id;
		foreach($this->wish_shop as $shop)
		{
			if($shop['username']==$order->wish_id)
			{
				$this->username = $shop['username'];
				$this->password = $shop['password'];
				$this->merchant_name = $shop['merchant_name'];
				break;
			}
			
		}
		if($order->order_status=='-1')
		{
			$orders = $this->order_model->get_wish_order_by_transaction_id($order->transaction_id);
			if(!$orders){return;}
			foreach($orders as $order_wish)
			{
				$order =$order_wish;
			}
			$data_order=array('is_shiped_ebay'=>1);
			$this->order_model->update_order_merged_list_information($OrderID,$data_order);
		}
		
		var_dump($order);
		$shipping_method = shipping_method($order->is_register);
		$track_numbers = $order->track_number;
		$carrier_code=$shipping_method->wish_company_code;
		
		$ShipDate=$order->ship_confirm_date;
		$ShipDate=date('m/d/Y',mktime(substr($ShipDate,11,2),substr($ShipDate,14,2),substr($ShipDate,17,2),substr($ShipDate,5,2),substr($ShipDate,8,2),substr($ShipDate,0,4)));
		$LabelCreateDate=$order->print_label_date;
		$LabelCreateDate=date("m/d/Y H:i",mktime(substr($LabelCreateDate,11,2),substr($LabelCreateDate,14,2),substr($LabelCreateDate,17,2),substr($LabelCreateDate,5,2),substr($LabelCreateDate,8,2),substr($LabelCreateDate,0,4)));
		$Name=$order->name;
		$Address1=$order->address_line_1;
		$Address2=$order->address_line_2;
		$City=$order->town_city;
		$State=$order->state_province;
		$PostalCode=$order->zip_code;
		$Country=get_country_code($order->country);
		$ShippingCost=$order->shippingamt;
		
		$post_xml=<<<XML
<?xml version="1.0" encoding="utf-8"?>
<ShipNotice>
<OrderID>$OrderID</OrderID>
<OrderNumber>$OrderNumber</OrderNumber>
<CustomerCode>$buyer_id</CustomerCode>
<NotifyCustomer>True</NotifyCustomer>
<ShipDate>$ShipDate</ShipDate>
<ShippingCost>$ShippingCost</ShippingCost>
<Recipient>
<Name>$Name</Name>
<Address1>$Address1</Address1>
<Address2>$Address2</Address2>
<City>$City</City>
<State>$State</State>
<PostalCode>$PostalCode</PostalCode>
<Country>$Country</Country>
</Recipient>
<LabelCreateDate>$LabelCreateDate</LabelCreateDate>
<Carrier>$carrier_code</Carrier>
<TrackingNumber>$track_numbers</TrackingNumber>
</ShipNotice>
XML;
		$action='shipnotify';var_dump($post_xml);
		
		if($order->auction_site_type == 'wish')
		{
			$order_gateway_url = $this->wish_url_ssl."://".$this->username.":".$this->password."@".$this->wish_url.$this->merchant_name."?action=".$action;
			echo $order_gateway_url;
			$ch = curl_init();
			$header[] = "Content-type: text/xml";//定义content-type为xml
			curl_setopt($ch, CURLOPT_URL, $order_gateway_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_POST, 1);//启用POST提交 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_xml); //设置POST提交的字符串
			//curl_setopt($ch, CURLOPT_HEADER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
            var_dump($response);
			$this->ebay_order_model->delete_order_id($order_id);
		}
		$filename = '/var/www/html/log/wish/';
		if (!file_exists($filename))
        {
            mkdir($filename);
        }
		$filename .= date('Y-m-d').'.log';
		$requestInformation=$order_gateway_url.$post_xml;
		writefile($filename, $requestInformation, 'a');
		$requestInformation=var_export($response,true);
		writefile($filename, $requestInformation, 'a');
	}



}