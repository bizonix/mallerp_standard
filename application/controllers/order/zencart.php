<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
class Zencart extends Mallerp_no_key
{

    private $start_time;
    private $end_time;
	private $web_service_url;
	private $zencart_paypal;
	private $zencart_gateway_urls;
	private $zencart_web_paypal;
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
		$this->load->config('config_zencart');

		$this->zencart_gateway_urls = $this->config->item('zencart_gateway_url');
		$this->zencart_paypal = $this->config->item('zencart_paypal');

        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }

        /*if (!session_id()) {
            session_start();
        }*/

        set_time_limit(0);
		date_default_timezone_set(DEFAULT_TIMEZONE);
    }



	public function import_zencart_orders()
	{
		$formTime='2013-06-23T11:11:11Z';
		$toTime='2013-07-01T00:00:00Z';
		$action='orderlist';
		foreach($this->zencart_gateway_urls as $key=>$zencart_gateway_url)
		{
			$formTime=$this->order_model->get_magento_order_begin_time(array($key));
			
			//$toTime='2013-04-04 04:24:11';
			$toTime=date('Y-m-d\TH:i:s\Z',mktime(substr($formTime,11,2)+24,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));
			$formTime=date('Y-m-d\TH:i:s\Z',mktime(substr($formTime,11,2)-24*30,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));

			$startdate=strtotime($toTime);
			$enddate=strtotime(get_utc_time_b2c('-5 minutes'));
			if($enddate-$startdate<=0){
				$toTime = get_utc_time_b2c('-5 minutes');
			}
			echo $formTime."----".$toTime."\n";
			$api_type="xml";
			$order_gateway_url = $zencart_gateway_url."/".$action."/?sdate=".$formTime."&edate=".$toTime."&api_type=".$api_type;
			$this->zencart_web_paypal = $this->zencart_paypal[$key];
			echo $order_gateway_url."\n";
			$ch = curl_init();
			$header[] = "Content-type: text/xml";//定义content-type为xml
			curl_setopt($ch, CURLOPT_URL, $order_gateway_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			$response = curl_exec($ch);
			curl_close($ch);
			$arr = parseNamespaceXml(trim($response));
			echo $arr['Domain']."\n";
			//var_dump($arr);
			if($arr['OrderNum']==1)
			{
				$arr['OrderLists']['OrderList']=$arr['OrderLists'];
			}
			if(!isset($arr['OrderLists']['OrderList']))
			{
				$this->order_model->update_magento_order_begin_time($toTime,array($key));
				continue;
			}
			if(count($arr['OrderLists']['OrderList'])==0)
			{
				$this->order_model->update_magento_order_begin_time($toTime,array($key));
				continue;
			}
			foreach($arr['OrderLists']['OrderList'] as $OrderList)
			{
				//var_dump($OrderList);
				echo $OrderList['OrderNo']."|".$OrderList["OrderId"]."\n";
				$order_info=array(
					'domain'=>$arr['Domain'],
					//'order_no'=>$OrderList['OrderNo'],
					'order_id'=>$OrderList['OrderId'],
					//'order_id'=>218,
					'zencart_gateway_url'=>$zencart_gateway_url,
					);//var_dump($order_info);die();
				if($order_info['order_id']!='')
				{
					$data=$this->_make_common_order_list_data($order_info);
				}else{
					continue;
				}
				if($data['created_at']=='')
				{
					continue;
				}
				
				print_r($data);//die();
				//echo "Order ID: ", $order['increment_id'], "\n";
				if ($this->order_model->check_exists('order_list', array('domain' => $data['domain'],'input_from_row' => $data['input_from_row']))) {
					//$this->order_model->update_order_by_item_no($data['item_no'],$data);
				}else{
					$order_id = $this->order_model->add_order($data);
				}
				//$this->order_model->update_magento_order_begin_time($data['created_at'],array($key));
				$this->order_model->update_magento_order_begin_time($toTime,array($key));
				echo "\n*************\n";//die();
			}//end foreach
		}//end foreach
		
	}
	private function _make_common_order_list_data($transaction_details)
    {
		$action='orderdetail';
		$api_type="xml";
		//$transaction_details['order_id']=16275;
		$order_gateway_url = $transaction_details['zencart_gateway_url']."/".$action."/?id=".$transaction_details['order_id']."&domain=".$transaction_details['domain']."&api_type=".$api_type;
		echo $order_gateway_url."\n";
		$ch = curl_init();
		$header[] = "Content-type: text/xml";//定义content-type为xml
		curl_setopt($ch, CURLOPT_URL, $order_gateway_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		curl_close($ch);
		$arr = parseNamespaceXml(trim($response));
		//echo $arr['Domain']."\n";
		
		//echo $order_gateway_url;
		//$ship_to_country = $this->mixture_model->get_country_name_in_english_by_code(strtoupper($transaction_details['shipping_address']['country_id']));
		$ship_to_country=$arr['Country'];
		$import_date=date('Y-m-d H:i:s');
		$item_titles=array();
		$item_ids=array();
		$item_qties=array();
		$item_codes=array();
		$item_price=array();
		//var_dump($arr['Products']['Product']);
		//var_dump($arr);
		if($arr['ItemNum']==1)
		{
			$arr['Products']['Product']=$arr['Products'];
		}

		foreach($arr['Products']['Product'] as $key=>$product)
		{
				//var_dump($product);
				//echo $key.":".$value."\n";
				/*
				if ($this->sale_model->check_exists('product_makeup_sku', array('makeup_sku' => trim($product['Model']) )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku(trim($product['Model']));
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $key=>$value)
					{
						$count_sku=(int)$product['Qty']*$qty_arr[$key];
						$item_titles[] = trim(base64_decode($product['Title']));
						$item_qties[] = $count_sku;
						$item_ids[] = (int)$product['Id'];
						$item_codes[] = trim($value);
						$item_price[] = trim($product['Price']);
						
					}
				}else{*/
					$item_titles[] = trim(base64_decode($product['Title']));
					$item_qties[] = (int)$product['Qty'];
					$item_ids[] = (int)$product['Id'];
					$item_codes[] = trim($product['Model']);
					$item_price[] = trim($product['Price']);
				//}
				
			
		}
		//$is_register='SGS';// strpos($arr['ShippingMethod'], 'Registered Air Mail') !== false
		$is_register='CHS';
		$sp_country=array('ITALY','SPAIN','PORTUGAL','FRANCE','BELGIUM','LUXEMBOURG','MONACO','NETHERLANDS','UNITED KIONGDOM','IRELAND','GERMANY');
		$sp_country1=array('SWEDEN','DENMARK','FINLAND','NORWAY');
		if(($arr['Gross']<15 && $arr['Shippingamt']=='1.99')||($arr['Gross']>=15))
		{
			if(strtoupper($ship_to_country)=='UNITED STATES'){$is_register='EUB';}
			if(strtoupper($ship_to_country)=='BRAZIL'){$is_register='CNR';}
			if(in_array(strtoupper($ship_to_country),$sp_country)){$is_register='CHR';}
			if(in_array(strtoupper($ship_to_country),$sp_country1)){$is_register='SGR';}
			if(!in_array(strtoupper($ship_to_country),$sp_country1)&&!in_array(strtoupper($ship_to_country),$sp_country)){$is_register='SGR';}
		}
		if(strpos($arr['ShippingMethod'], 'DHL Rates') !== false)
		{
			$is_register='DHL';
		}
		if(strpos($arr['ShippingMethod'], 'EMS Rates') !== false)
		{
			$is_register='EMS';
		}
		
		$order_status=$this->order_statuses['wait_for_purchase'];
		$sp_country=array('RUSSIA','UKRAINE','SPAIN','ROMANIA','VIETNAM','MALAYSIA','PHILIPPINES','ZAMBIA','SOUTH AFRICA','UGANDA','EGYPT','SUDAN','LIBYA','TUNISIA','ALGERIA','MOROCCO','ETHIOPIA','KENYA','TANZANIA','RWANDA','SEYCHELLES','MALI','SIERRA LEONE','LIBERIA',"Côte d'Ivoire",'REPUBLIC OF IVORY COAST','GHANA','NIGER','NIGERIA','BENIN','CHAD','CENTRAL AFRICAN REPUBLIC','CAMEROON','ANGOLA');
		if($arr['Gross']>200||$arr['PaymentMethod']!='PayPal'||count($item_codes)>=2||in_array(strtoupper($ship_to_country),$sp_country))
		{
			$order_status=$this->order_statuses['wait_for_confirmation'];
		}
		
		$auction_sites=explode(" ",preg_replace("/\s/"," ",$arr['Domain']));
		if($arr['StoreId']=='www.7daysget.com'){$auction_sites='7DG';}
		if($arr['StoreId']=='www.screamprice.com'){$auction_sites='SP';}
		$item_no=$arr['ItemNo'];
		$sys_remark = $this->_create_sys_remark($import_date,$item_no);
		$note=(!empty($arr['Note']))?trim(base64_decode($arr['Note'])):'';
		$note_array=array();
		$note_array=unserialize($note);
		foreach($note_array as $key=>$value)
		{
			$note_array[$key]=iconv("GB2312","UTF-8",$value);
		}
		//echo implode('', $note_array);die();
        $data = array(
                    'list_date'                 => $arr['ListDate'],
                    'list_time'                 => $arr['ListTime'],
                    'time_zone'                 => 'PDT',
					'created_at'				=> $arr['DatePurchased'],
					'payment_status'			=> (isset($arr['PaymentStatus']) && !empty($arr['PaymentStatus']))?$arr['PaymentStatus']:'NONE',
                    'name'                      => $arr['Name'],
                    'item_title_str'            => implode(ITEM_TITLE_SEP, $item_titles),
                    'item_id_str'				=> implode(',', $item_ids),
					'item_price_str'			=> implode(',', $item_price),
                    'qty_str'                   => implode(',', $item_qties),
					'sku_str'                   => implode(',', $item_codes),
                    'currency'                  => $arr['Currency'],
                    'gross'                     => $arr['Gross'],
                    'fee'                       => 0,
                    'net'                       => $arr['Net'],
					'shippingamt'               => $arr['Shippingamt'],
                    'note'                      => implode('', $note_array),
                    'from_email'                => $arr['FromeMail'],
                    'to_email'                  => $this->zencart_web_paypal,
                    'transaction_id'            => (isset($arr['PaypalTransactionId']) && !empty($arr['PaypalTransactionId']))?$arr['PaypalTransactionId']:'',
					'item_no'					=> $item_no,
					'is_register'				=> $is_register,
                    'payment_type'              => $arr['PaymentMethod'],
                    'counterparty_status'       => '',
                    'address_status'            => '',
                    'shipping_handling_amount'  => '',
                    'insurance_amount'          => 0,
                    'sales_tax'                 => $arr['SalesTax'],
                    'auction_site'              => $auction_sites,
					'auction_site_type'			=> 'zencart',
                    'buyer_id'                  => $arr['FromeMail'],
                    'item_url'                  => '',
                    'closing_date'              => '',
                    'reference_txn_id'          => '',
                    'invoice_number'            => '',
                    'subscription_number'       => '',
                    'custom_number'             => '',
                    'receipt_id'                => '',
                    'sys_remark'                => $sys_remark,
                    'address_line_1'            => preg_replace("/\s/"," ",$arr['AddressLine']),
                    'address_line_2'            => '',
                    'town_city'                 => $arr['TownCity'],
                    'state_province'            => isset($arr['StateProvince'])?$arr['StateProvince']:'',
                    'zip_code'                  => $arr['ZipCode'],
                    'country'                   => $ship_to_country,
                    'contact_phone_number'      => (isset($arr['ContactPhoneNumber']) && !empty($arr['ContactPhoneNumber']))?$arr['ContactPhoneNumber']:'',
                    'balance_impact'            => '',
                    'income_type'               => '',
                    'input_date'                => $import_date,
                    'input_from_row'            => $arr['InputFromRow'],
                    'paid_time'                 => '',
					'remote_ip'					=> $arr['RemoteIp'],
					'domain'					=> $arr['StoreId'],
					'order_status'				=> (isset($arr['PaymentStatus']) && $arr['PaymentStatus']=='Completed') ? $this->order_statuses['wait_for_confirmation']:$this->order_statuses['wait_for_finance_confirmation'],
                );
				foreach(explode(',', $data['sku_str']) as $sku)
				{
					if (!$this->product_model->fetch_product_id(strtoupper($sku)) || 1==1) {
								$data['order_status']=$this->order_statuses['wait_for_confirmation'];
					}
				}
		//var_dump($item_titles);
		//var_dump($item_qties);
        return $data;
    }



	private function _create_sys_remark($import_date,$item_no)
    {
       
        $sys_remark = <<<DATA
{$import_date}由程序导入本订单(zencart自动导单),编号为:{$item_no};
DATA;
        
        return $sys_remark;
    }

	public function auto_update_order_comment()
	{
		$order_comments=$this->order_model->fetch_need_update_order_remark();
		foreach($order_comments as $order_comment)
		{
			$this->_add_magento_order_comment($order_comment->id);
		}
	}
	private function _add_magento_order_comment($order_remark_id)
	{
		$order_comment=$this->order_model->fetch_order_remark_by_id($order_remark_id);
		
		$order = $this->order_model->get_order($order_comment->order_id);
		if($order->auction_site_type == 'zencart')
		{
			$action='ordermodify';
			$zencart_gateway_url = "http://".$order->store_id."/GetOrdersApi.php";
			$zencart_gateway_url = $zencart_gateway_url."/".$action."/";
			
			$status=2;
			$comments=$order_comment->remark_content;
			$notify=1;
			$notify_comments=1;
			$postfield="id=".$order->input_from_row."&no=".$order->transaction_id."&status=".$status."&comments=".$comments."&notify=".$notify."&notify_comments=".$notify_comments."&domain=".$order->store_id;
			//$zencart_gateway_url=$zencart_gateway_url."?".$postfield;
			//die($zencart_gateway_url);
			echo $postfield."\n";


			$ch = curl_init();
			$header[] = "Content-type: text/xml";//定义content-type为xml
			curl_setopt($ch, CURLOPT_URL, $zencart_gateway_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);//启用POST提交 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield); //设置POST提交的字符串
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			var_dump($response);
			//$arr = parseNamespaceXml($response);
			//echo $arr['Domain']."\n";
			//var_dump($arr);
			//die();
			//echo $order_gateway_url;
			
		}
	}

	public function complete_sale($order_id)
	{
		$order = $this->order_model->get_order($order_id);
		if($order->domain=='www.7daysget.com')
		{
			$status_array=array(
							'SGS'=>13,
							'CHS'=>13,
							'HKS'=>13,
							'CHR'=>8,
							'CNR'=>8,
							'EMS'=>9,
							'DHL'=>10,
							'SGR'=>8,
							'EUB'=>8,
							'HKR'=>8,
							'MYR'=>8,
			);
		}
		if($order->domain=='www.screamprice.com')
		{
			$status_array=array(
							'SGS'=>13,
							'CHS'=>13,
							'HKS'=>13,
							'CHR'=>8,
							'CNR'=>8,
							'EMS'=>9,
							'DHL'=>10,
							'SGR'=>8,
							'EUB'=>8,
							'HKR'=>8,
							'MYR'=>8,
			);
		}
		
		$shipping_method = shipping_method($order->is_register);
		$track_numbers = explode(',',$order->track_number);
		$qties = explode(',',$order->qty_str);
		$item_ids = explode(',',$order->item_id_str);
		$itemsQty=array();
		foreach($item_ids as $key=>$item_id)
		{
			$itemsQty[$item_id]=$qties[$key];
		}

		if($order->auction_site_type == 'zencart')
		{
			
			$action='ordermodify';
			$zencart_gateway_url = "http://".$order->domain."/GetOrdersApi.php";
			$zencart_gateway_url = $zencart_gateway_url."/".$action."/";
			
			$status=$status_array[$order->is_register];
			$track_url='';
			if($order->track_number!='' || !empty($order->track_number)|| $order->track_number!=NULL)
			{
				$comments='items has been shipped on '.date('Y-m-d H:i:s').';tracking url:http://www.17track.net; tracking numbers:'.$order->track_number;
				$track_url='http://www.17track.net';
			}else{
				$comments='items has been shipped on '.date('Y-m-d H:i:s');
			}
			
			$notify=1;
			$notify_comments=1;
			$postfield="id=".$order->input_from_row."&no=".$order->transaction_id."&status=".$status."&comments=".$comments."&notify=".$notify."&notify_comments=".$notify_comments."&domain=".$order->domain."&track_number=".$order->track_number."&track_url=".$track_url;
			//$zencart_gateway_url=$zencart_gateway_url."?".$postfield;
			//die($zencart_gateway_url);
			echo $postfield."\n";


			$ch = curl_init();
			$header[] = "Content-type: text/xml";//定义content-type为xml
			curl_setopt($ch, CURLOPT_URL, $zencart_gateway_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);//启用POST提交 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield); //设置POST提交的字符串
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$this->ebay_order_model->delete_order_id($order_id);
			
		}//end magento if

		
	}






}