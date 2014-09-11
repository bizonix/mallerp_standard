<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
class Aliexpress extends Mallerp_no_key
{
	private $CI = NULL;
    private $start_time;
    private $end_time;
	private $appKey;
	private $appSecret;
	private $access_token;
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
		$this->load->config('config_aliexpress');
        $this->appKey = $this->config->item('appKey');
        $this->appSecret = $this->config->item('appSecret');
		$this->CI = & get_instance();

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
	public function update_aliexpress_access_token()
	{
		$this->CI->load->config('config_aliexpress');
        $appKey = $this->CI->config->item('appKey');
        $appSecret = $this->CI->config->item('appSecret');
		$get_access_token_url ="https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/{$appKey}";
		$aliexpress_tokens=$this->system_model->fetch_all_aliexpress_token();
		foreach($aliexpress_tokens as $aliexpress_token)
		{
			$refresh_token=$aliexpress_token->refresh_token;
			$curlPost =
		'grant_type=refresh_token&client_id='.urlencode($appKey).'&client_secret='.urlencode($appSecret).'&refresh_token='.urlencode($refresh_token).'';
			echo $curlPost."\n";
			$ch = curl_init();//初始化curl
			curl_setopt($ch,CURLOPT_URL,$get_access_token_url);//抓取指定网页
			curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
			curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$data = curl_exec($ch);//运行curl
			curl_close($ch);
			$data=json_decode($data);
			echo "<pre>";
			var_dump($data);//输出结果
			echo "</pre>";
			$this->system_model->update_aliexpress_token_by_aliid($data->aliId,array('access_token'=>$data->access_token));
			
		}
		
		
	}
	private function api_code_sign($apiInfo, $code_arr)
	{
		$url = 'http://gw.api.alibaba.com/openapi';
		//$apiInfo = 'json/1/aliexpress.open/api.getChildrenPostCategoryById/' . $this->appKey;
		//urlencode('http://hanshisky.taobao.com');就是说签名的时候不要乱七八糟的encode,会跳转不过去
		//生成签名
		ksort($code_arr);
		$sign_str='';
		foreach ($code_arr as $key=>$val)
		{
        	$sign_str .= $key . $val;
		}
		$sign_str = $apiInfo . $sign_str;//签名因子
		//echo $sign_str."<br>";
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $this->appSecret, true)));
		return $code_sign;
	}



	public function import_aliexpress_orders()
	{
		$formTime='08/15/2013';
		$toTime='08/18/2013';
		$aliexpress_tokens=$this->system_model->fetch_all_aliexpress_token();
		foreach($aliexpress_tokens as $aliexpress_token)
		{
			/*$formTime=$this->order_model->get_magento_order_begin_time(array($key));
			$formTime=date('Y-m-d\TH:i:s\Z',mktime(substr($formTime,11,2),substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));
			//$toTime='2013-04-04 04:24:11';
			$toTime=date('Y-m-d\TH:i:s\Z',mktime(substr($formTime,11,2)+2,substr($formTime,14,2),substr($formTime,17,2),substr($formTime,5,2),substr($formTime,8,2),substr($formTime,0,4)));

			$startdate=strtotime($toTime);
			$enddate=strtotime(get_utc_time('-5 minutes'));
			if($enddate-$startdate<=0){
				$toTime = get_utc_time('-5 minutes');
			}*/
			$nowtime=date('Y-m-d\TH:i:s\Z');
			$formTime=date('m/d/Y',mktime(substr($nowtime,11,2)-24*30,substr($nowtime,14,2),substr($nowtime,17,2),substr($nowtime,5,2),substr($nowtime,8,2),substr($nowtime,0,4)));
			//$formTime=date('m/d/Y');
			$toTime=date('m/d/Y');
			echo $formTime."----".$toTime."\n";
			$url = 'http://gw.api.alibaba.com/openapi/';
			$apiInfo = 'param2/1/aliexpress.open/api.findOrderListQuery/' . $this->appKey;
			//$access_token=$aliexpress_token->access_token;
			$page=0;
			$all_pages=0;
			//if($aliexpress_token->aliid!='1095280561839'){continue;}
			$this->access_token=$aliexpress_token->access_token;
			//$access_token='d318f663-87fd-4f24-9ace-58cbc30d11cb';
			do{
				if($page<1){$page=1;}
				$code_arr = array(
        			'access_token' => urlencode($this->access_token),
        			'page' => $page,
        			'pageSize' => 50,
					'orderStatus'=>'WAIT_SELLER_SEND_GOODS',
					'createDateStart' => $formTime,
					'createDateEnd' => $toTime,
				);
				$get_refresh_token_url =$url.$apiInfo;
				$code_sign=$this->api_code_sign($apiInfo, $code_arr);
				$curlPost ='';
				$i=0;
				foreach($code_arr as $key=>$val)
				{
					$curlPost .=$key.'='.urlencode($val).'&';
				}
				$get_refresh_token_url.= "?".substr(trim($curlPost), 0, -1);
				//echo $get_refresh_token_url."&_aop_signature=".$code_sign;
				echo "<br>";
				$ch = curl_init();//初始化curl
				curl_setopt($ch,CURLOPT_URL,$get_refresh_token_url);//抓取指定网页
				curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
				//curl_setopt($ch, CURLOPT_POST, 0);//post提交方式
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
				$data = curl_exec($ch);//运行curl
				curl_close($ch);
				$data=json_decode($data);
				if($page==1)
				{
					$all_pages= ceil($data->totalItem/$code_arr['pageSize']);
				}
				//echo "****".$page."****";
				$page++;
				$order_list=array();
				if(isset($data->orderList))
				{
					$order_list= $data->orderList;
				}
				
				//echo "****".$all_pages."****";
				
				//echo "<pre>";
				//var_dump($data);//输出结果
				//echo "</pre>";
				if(count($order_list)>0)
				{
					foreach($order_list as $order)
					{
						if($order->orderId!='')
						{
							$order_data=$this->_make_common_order_list_data($order);
						}else{
							continue;
						}
						if($order_data['created_at']=='')
						{
							continue;
						}
						echo "order data:<pre>";print_r($order_data);echo "</pre>";
						if ($this->order_model->check_exists('order_list', array('transaction_id' => $order_data['transaction_id']))) {
						}else{
							$order_id = $this->order_model->add_order($order_data);
						}
					}
				}
			}while($page<=$all_pages);
		}//end foreach
		
	}
	private function _make_common_order_list_data($transaction_details)
    {
		//echo "transaction_details:<pre>";var_dump($transaction_details);echo "</pre>";
		$url = 'http://gw.api.alibaba.com/openapi/';
		$apiInfo = 'param2/1/aliexpress.open/api.findOrderById/' . $this->appKey;
		$code_arr = array(
        		'access_token' => urlencode($this->access_token),
        		'orderId' => $transaction_details->orderId,
			);
		$get_order_url =$url.$apiInfo;
		$code_sign=$this->api_code_sign($apiInfo, $code_arr);
		$curlPost ='';
		foreach($code_arr as $key=>$val)
		{
			$curlPost .=$key.'='.urlencode($val).'&';
		}
		$get_order_url.= "?".substr(trim($curlPost), 0, -1);
		//echo $get_order_url."&_aop_signature=".$code_sign;
		$ch = curl_init();//初始化curl
		curl_setopt($ch,CURLOPT_URL,$get_order_url);//抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		$order_detail = curl_exec($ch);//运行curl
		curl_close($ch);
		$order_detail=json_decode($order_detail);
		echo "order_detail:<pre>";var_dump($order_detail);echo "</pre>";
		$ship_to_country=$order_detail->receiptAddress->country;
		$ship_to_country = $this->mixture_model->get_country_name_in_english_by_code(strtoupper($ship_to_country));
		echo "************".$order_detail->receiptAddress->country;
		
		$import_date=date('Y-m-d H:i:s');
		$item_titles=array();
		$item_ids=array();
		$item_qties=array();
		$item_codes=array();
		$item_price=array();
		//var_dump($arr['Products']['Product']);
		foreach($order_detail->childOrderList as $product)
		{
				//echo "**********";var_dump($product);die();
				//echo $key.":".$value."\n";
				$item_titles[] = trim($product->productName);
				$item_qties[] = (int)$product->productCount;
				$item_ids[] = (int)$product->productId;
				$item_codes[] = trim($product->skuCode);
				$item_price[] = $product->productPrice->amount;
				
			
		}
		$note='';
		$note_logisticsServiceName='';
		$is_register='';
		$item_url=array();
		$i=0;
		foreach($transaction_details->productList as $product)
		{
			$note.=trim($product->memo);
			if($i==0)
			{
				echo "*9*9*9*9*9--".$product->logisticsType;
				$is_register=get_shipping_code_by_company_code($product->logisticsType);
				$note_logisticsServiceName="logisticsServiceName:".$product->logisticsServiceName;
				$item_url[]=$product->productSnapUrl;
			}
			$i++;
		}
		/*print_r($item_titles);
		print_r($item_qties);
		print_r($item_ids);
		print_r($item_codes);*/
		$gmtcreate=$order_detail->gmtCreate;
		/*20130713235618000*/
		$list_date=date('Y-m-d',mktime(substr($gmtcreate,8,2),substr($gmtcreate,10,2),substr($gmtcreate,12,2),substr($gmtcreate,4,2),substr($gmtcreate,6,2),substr($gmtcreate,0,4)));
		$list_time=date('H:i:s',mktime(substr($gmtcreate,8,2),substr($gmtcreate,10,2),substr($gmtcreate,12,2),substr($gmtcreate,4,2),substr($gmtcreate,6,2),substr($gmtcreate,0,4)));
		$gmtcreate=date('Y-m-d H:i:s',mktime(substr($gmtcreate,8,2),substr($gmtcreate,10,2),substr($gmtcreate,12,2),substr($gmtcreate,4,2),substr($gmtcreate,6,2),substr($gmtcreate,0,4)));
		
		//die('*************');
		
		$item_no=$transaction_details->orderId;
		$sys_remark = $this->_create_sys_remark($import_date,$item_no);
		$phone_str='';
		if(isset($order_detail->receiptAddress->phoneCountry))
		{
			$phone_str.=$order_detail->receiptAddress->phoneCountry;
		}
		if(isset($order_detail->receiptAddress->phoneArea))
		{
			$phone_str.=$order_detail->receiptAddress->phoneArea;
		}
		if(isset($order_detail->receiptAddress->phoneNumber))
		{
			$phone_str.=$order_detail->receiptAddress->phoneNumber;
		}
		if($phone_str=='')
		{
			$phone_str.=$order_detail->receiptAddress->mobileNo;
		}
        $data = array(
                    'list_date'                 => $list_date,
                    'list_time'                 => $list_time,
                    'time_zone'                 => 'PDT',
					'created_at'				=> $gmtcreate,
					'payment_status'			=> $order_detail->fundStatus,
                    'name'                      => $order_detail->receiptAddress->contactPerson,
                    'item_title_str'            => implode(ITEM_TITLE_SEP, $item_titles),
                    'item_id_str'				=> implode(',', $item_ids),
                    'qty_str'                   => implode(',', $item_qties),
					'sku_str'                   => implode(',', $item_codes),
					'item_price_str'			=> implode(',', $item_price),
                    'currency'                  => $order_detail->orderAmount->currencyCode,
                    'gross'                     => $order_detail->orderAmount->amount,
                    'fee'                       => price($order_detail->orderAmount->amount*0.05),
                    'net'                       => price($order_detail->orderAmount->amount*0.95),
					'shippingamt'               => $order_detail->logisticsAmount->amount,
                    'note'                      => $note." ".$note_logisticsServiceName,
                    'from_email'                => $order_detail->buyerInfo->email,
                    'to_email'                  => $order_detail->sellerOperatorLoginId,
                    'transaction_id'            => $transaction_details->orderId,
					'item_no'					=> $item_no,
                    'payment_type'              => '',
                    'counterparty_status'       => '',
                    'address_status'            => '',
                    'shipping_handling_amount'  => '',
                    'insurance_amount'          => 0,
                    'sales_tax'                 => '',
					'is_register'				=> $is_register,
                    'auction_site'              => 'SM',
					'auction_site_type'			=> 'aliexpress',
                    'buyer_id'                  => $order_detail->buyerInfo->loginId,
                    'item_url'                  => implode(',', $item_url),
                    'closing_date'              => '',
                    'reference_txn_id'          => '',
                    'invoice_number'            => '',
                    'subscription_number'       => '',
                    'custom_number'             => '',
                    'receipt_id'                => '',
                    'sys_remark'                => $sys_remark,
                    'address_line_1'            => preg_replace("/\s/"," ",$order_detail->receiptAddress->detailAddress),
                    'address_line_2'            => $order_detail->receiptAddress->address2,
                    'town_city'                 => $order_detail->receiptAddress->city,
                    'state_province'            => $order_detail->receiptAddress->province,
                    'zip_code'                  => $order_detail->receiptAddress->zip,
                    'country'                   => $ship_to_country,
                    'contact_phone_number'      => $phone_str,
                    'balance_impact'            => '',
                    'income_type'               => '',
                    'input_date'                => $import_date,
                    'input_from_row'            => '',
                    'paid_time'                 => '',
					'remote_ip'					=> '',
					'domain'					=> $order_detail->sellerOperatorLoginId,
					'order_status'				=> $this->order_statuses['wait_for_confirmation'],
                );
				/*sku 不 存在的需要客服确认*/
				foreach(explode(',', $data['sku_str']) as $sku)
				{
					if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
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
{$import_date}由程序导入本订单(Aliexpress自动导单),编号为:{$item_no};
DATA;
        
        return $sys_remark;
    }
	
	
	public function complete_sale($order_id)
	{
		$order = $this->order_model->get_order($order_id);
		$shipping_method = shipping_method($order->is_register);
		$track_numbers = $order->track_number;
		
		$aliexpress_tokens=$this->system_model->fetch_all_aliexpress_token();
		$url = 'http://gw.api.alibaba.com/openapi/';
		$apiInfo = 'param2/1/aliexpress.open/api.sellerShipment/' . $this->appKey;
		foreach($aliexpress_tokens as $aliexpress_token)
		{
			
			//$access_token=$aliexpress_token->access_token;
			if($aliexpress_token->resource_owner==$order->domain||$aliexpress_token->aliid==$order->domain)
			{
				$this->access_token=$aliexpress_token->access_token;
			}
		}
		$code_arr = array(
        	'access_token' => urlencode($this->access_token),
        	'serviceName' => $shipping_method->taobao_company_code,
        	'logisticsNo' => $track_numbers,
			'sendType' => 'all',
			'outRef' => $order->transaction_id,
		);
		$get_refresh_token_url =$url.$apiInfo;
		$code_sign=$this->api_code_sign($apiInfo, $code_arr);
		$curlPost ='';
		$i=0;
		foreach($code_arr as $key=>$val)
		{
			$curlPost .=$key.'='.urlencode($val).'&';
		}
		$get_refresh_token_url.= "?".substr(trim($curlPost), 0, -1);
				echo $get_refresh_token_url."&_aop_signature=".$code_sign;
		echo "<br>";
		$ch = curl_init();//初始化curl
		curl_setopt($ch,CURLOPT_URL,$get_refresh_token_url);//抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		//curl_setopt($ch, CURLOPT_POST, 0);//post提交方式
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = curl_exec($ch);//运行curl
		curl_close($ch);
		$data=json_decode($data);
		var_dump($data);
		$this->ebay_order_model->delete_order_id($order_id);

		$filename = '/var/www/html/log/aliexpress/';
		if (!file_exists($filename))
        {
            mkdir($filename);
        }
		$filename .= date('Y-m-d').'.log';
		$requestInformation=$get_refresh_token_url;
		writefile($filename, $requestInformation, 'a');
		$requestInformation=var_export($data,true);
		writefile($filename, $requestInformation, 'a');
		
	}


}