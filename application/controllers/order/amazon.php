<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
function __autoload($className){
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
		
        foreach($includePaths as $includePath){
            if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
                require_once $filePath;
                return;
            }
        }
 }
class Amazon extends Mallerp_no_key
{
    private $uid;
    private $user;
    private $paypal;
    private $item_info;
    private $to_merged_id;
    private $report;
    private $start_time;
    private $end_time;
    private $order_statuses = array();
    private $accounts = array();
    private $is_register = NULL;
    private $paypal_email_login_name = array();
    private $user_note = '';
    private $profit_rate = '';
    private $country_names = array();
	protected $CI;
	
	
    public function __construct()
    {
        parent::__construct();
		

        $this->lang->load('mallerp', DEFAULT_LANGUAGE);
        $this->load->model('order_model');
        $this->load->model('order_role_model');
        $this->load->model('product_model');
		$this->load->model('paypal_model');
        $this->load->model('mixture_model');
        $this->load->helper('order');
        $this->load->helper('shipping');
        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }

        if (!session_id()) {
            session_start();
        }
        set_time_limit(0);
    }
	public function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request) 
	{
		$AmazonOrderIds=array();
		$return_array=array();
		$NextToken='';
		try {
			$response = $service->listOrders($request);
			if ($response->isSetListOrdersResult()) {
				$listOrdersResult = $response->getListOrdersResult();
				if ($listOrdersResult->isSetNextToken())
				{
					$NextToken=$listOrdersResult->getNextToken();
				}
				if ($listOrdersResult->isSetOrders()) {
					$orders = $listOrdersResult->getOrders();
					$orderList = $orders->getOrder();
					foreach ($orderList as $order) {
						$AmazonOrderIds[]=$order->getAmazonOrderId();
					}
				}
			}
			$return_array=array($NextToken,$AmazonOrderIds);
			return $return_array;
		}catch (MarketplaceWebServiceOrders_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
	}


	function invokeListOrdersByNextToken(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		$AmazonOrderIds=array();
		$return_array=array();
		$NextToken='';
		try {
			$response = $service->listOrdersByNextToken($request);
			if ($response->isSetListOrdersByNextTokenResult()) {
				$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();
				if ($listOrdersByNextTokenResult->isSetNextToken())
				{
					$NextToken=$listOrdersByNextTokenResult->getNextToken();
				}
				if ($listOrdersByNextTokenResult->isSetOrders()) {
					$orders = $listOrdersByNextTokenResult->getOrders();
					$orderList = $orders->getOrder();
					foreach ($orderList as $order) {
						if ($order->isSetAmazonOrderId())
						{
							$AmazonOrderIds[]=$order->getAmazonOrderId();
						}
					}
				}
			}
			$return_array=array($NextToken,$AmazonOrderIds);
			return $return_array;
		}
		catch (MarketplaceWebServiceOrders_Exception $ex)
		{
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
	}
	function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		$order_info=array();
		try {
			$response = $service->getOrder($request);
			if ($response->isSetGetOrderResult()) { 
				$getOrderResult = $response->getGetOrderResult();
				if ($getOrderResult->isSetOrders()) {
					$orders = $getOrderResult->getOrders();
					$orderList = $orders->getOrder();
					if(empty($orderList))
					{
						return;
					}
					foreach ($orderList as $order) {
						if ($order->isSetAmazonOrderId()){$order_info["AmazonOrderId"]=$order->getAmazonOrderId();}
						if ($order->isSetSellerOrderId()){$order_info["SellerOrderId"]=$order->getSellerOrderId();}
						if ($order->isSetPurchaseDate()) {$order_info["PurchaseDate"]=$order->getPurchaseDate();}
						if ($order->isSetLastUpdateDate()){$order_info["LastUpdateDate"]=$order->getLastUpdateDate();}
						if ($order->isSetOrderStatus()){$order_info["OrderStatus"]=$order->getOrderStatus();}
						if ($order->isSetFulfillmentChannel()){$order_info["FulfillmentChannel"]=$order->getFulfillmentChannel();}
						if ($order->isSetSalesChannel()){$order_info["SalesChannel"]=$order->getSalesChannel();}
						if ($order->isSetOrderChannel()){$order_info["OrderChannel"]=$order->getOrderChannel();}
						if ($order->isSetShipServiceLevel()){$order_info["ShipServiceLevel"]=$order->getShipServiceLevel();}
						if ($order->isSetShippingAddress()) {
							$shippingAddress = $order->getShippingAddress();
							if ($shippingAddress->isSetName()){$order_info["Name"]=$shippingAddress->getName();}
							if ($shippingAddress->isSetAddressLine1()){$order_info["AddressLine1"]=$shippingAddress->getAddressLine1();}else{$order_info["AddressLine1"]='';}
							if ($shippingAddress->isSetAddressLine2()){$order_info["AddressLine2"]=$shippingAddress->getAddressLine2();}else{$order_info["AddressLine2"]='';}
							if ($shippingAddress->isSetAddressLine3()){$order_info["AddressLine3"]=$shippingAddress->getAddressLine3();}else{$order_info["AddressLine3"]='';}
							if ($shippingAddress->isSetCity()){$order_info["City"]=$shippingAddress->getCity();}else{$order_info["City"]='';}
							if ($shippingAddress->isSetCounty()){$order_info["County"]=$shippingAddress->getCounty();}else{$order_info["County"]='';}
							if ($shippingAddress->isSetDistrict()){$order_info["District"]=$shippingAddress->getDistrict();}else{$order_info["District"]='';}
							if ($shippingAddress->isSetStateOrRegion()){$order_info["StateOrRegion"]=$shippingAddress->getStateOrRegion();}
							if ($shippingAddress->isSetPostalCode()){$order_info["PostalCode"]=$shippingAddress->getPostalCode();}
							if ($shippingAddress->isSetCountryCode()){$order_info["CountryCode"]=$shippingAddress->getCountryCode();}
							if ($shippingAddress->isSetPhone()){$order_info["Phone"]=$shippingAddress->getPhone();}else{$order_info["Phone"]='';}
						}
						if ($order->isSetOrderTotal()) {
							$orderTotal = $order->getOrderTotal();
							if ($orderTotal->isSetCurrencyCode()){$order_info["CurrencyCode"]=$orderTotal->getCurrencyCode();}
							if ($orderTotal->isSetAmount()){$order_info["Amount"]=$orderTotal->getAmount();}
						}
						if ($order->isSetNumberOfItemsShipped()){$order_info["NumberOfItemsShipped"]=$order->getNumberOfItemsShipped();}
						if ($order->isSetNumberOfItemsUnshipped()){$order_info["NumberOfItemsUnshipped"]=$order->getNumberOfItemsUnshipped();}
						if ($order->isSetPaymentMethod()){$order_info["PaymentMethod"]=$order->getPaymentMethod();}
						if ($order->isSetMarketplaceId()){$order_info["MarketplaceId"]=$order->getMarketplaceId();}
						if ($order->isSetBuyerEmail()){$order_info["BuyerEmail"]=$order->getBuyerEmail();}
						if ($order->isSetBuyerName()){$order_info["BuyerName"]=$order->getBuyerName();}
						if ($order->isSetShipmentServiceLevelCategory()){$order_info["ShipmentServiceLevelCategory"]=$order->getShipmentServiceLevelCategory();}
						if ($order->isSetShippedByAmazonTFM()){$order_info["ShippedByAmazonTFM"]=$order->getShippedByAmazonTFM();}
						if ($order->isSetTFMShipmentStatus()){$order_info["TFMShipmentStatus"]=$order->getTFMShipmentStatus();}
					}
				}
			}
			return $order_info;
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
	}
	function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request)
	{
		$items=array();
		try {
			$response = $service->listOrderItems($request);
			if ($response->isSetListOrderItemsResult()) {
				$listOrderItemsResult = $response->getListOrderItemsResult();
				if ($listOrderItemsResult->isSetNextToken()){ echo $listOrderItemsResult->getNextToken();}
				if ($listOrderItemsResult->isSetOrderItems()) {
					$orderItems = $listOrderItemsResult->getOrderItems();
					$orderItemList = $orderItems->getOrderItem();
					$item=array();
					foreach ($orderItemList as $orderItem) {
						if ($orderItem->isSetASIN()){$item['ASIN']=$orderItem->getASIN();}
						if ($orderItem->isSetSellerSKU()){$item['SellerSKU']=$orderItem->getSellerSKU();}
						if ($orderItem->isSetOrderItemId()){$item['OrderItemId']=$orderItem->getOrderItemId();}
						if ($orderItem->isSetTitle()){$item['Title']=$orderItem->getTitle();}
						if ($orderItem->isSetQuantityOrdered()){$item['QuantityOrdered']=$orderItem->getQuantityOrdered();}
						if ($orderItem->isSetQuantityShipped()){$item['QuantityShipped']=$orderItem->getQuantityShipped();}
						if ($orderItem->isSetItemPrice()){
							$itemPrice = $orderItem->getItemPrice();
							if ($itemPrice->isSetCurrencyCode()){$item['CurrencyCode']=$itemPrice->getCurrencyCode();}
							if ($itemPrice->isSetAmount()){$item['Amount']=$itemPrice->getAmount();}
						}
						if ($orderItem->isSetShippingPrice()) {
							$shippingPrice = $orderItem->getShippingPrice();
							if ($shippingPrice->isSetAmount()){$item['ShippingAmount']=$shippingPrice->getAmount();}
						}
						if ($orderItem->isSetShippingDiscount()) {
							$shippingDiscount = $orderItem->getShippingDiscount();
							if ($shippingDiscount->isSetAmount()){$item['ShippingDiscountAmount']=$shippingDiscount->getAmount();}
						}
						if ($orderItem->isSetPromotionDiscount()) {
							$promotionDiscount = $orderItem->getPromotionDiscount();
							if ($promotionDiscount->isSetAmount()){$item['PromotionDiscountAmount']=$promotionDiscount->getAmount();}
						}
						$items[]=$item;
					}
					//end foreach
					return $items;
				}
			}
		} catch (MarketplaceWebServiceOrders_Exception $ex) {
			echo("Caught Exception: " . $ex->getMessage() . "\n");
			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
			echo("Error Code: " . $ex->getErrorCode() . "\n");
			echo("Error Type: " . $ex->getErrorType() . "\n");
			echo("Request ID: " . $ex->getRequestId() . "\n");
			echo("XML: " . $ex->getXML() . "\n");
			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
		}
	}
	public function test()
	{
		$this->load->config('config_mws_api');
		$amazon_app = $this->config->item('amazon_app');
		//var_dump($amazon_app);
		foreach($amazon_app as $amazon)
		{
			$serviceUrl=$amazon['serviceUrl'];
			$amazon_api = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
			);
			$all_order_id=array();
				
			$service = new MarketplaceWebServiceOrders_Client(
				$amazon['AWS_ACCESS_KEY_ID'],
				$amazon['AWS_SECRET_ACCESS_KEY'],
				$this->config->item('APPLICATION_NAME'),
				$this->config->item('APPLICATION_VERSION'),
				$amazon_api);
			$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
			$request->setSellerId($amazon['MERCHANT_ID']);

			//$request->setCreatedAfter(new DateTime('2012-12-17 00:00:00', new DateTimeZone('UTC')));
			$beginning_time=$this->order_model->get_amazon_import_beginning_time();
			
			$end_time=date('Y-m-d H:i:s',mktime(substr($beginning_time,11,2)+24,substr($beginning_time,14,2),substr($beginning_time,17,2),substr($beginning_time,5,2),substr($beginning_time,8,2),substr($beginning_time,0,4)));

			$startdate=strtotime($end_time);
			$enddate=strtotime(str_replace("Z","",str_replace("T"," ",get_utc_time('-20 minutes'))));
			if($enddate-$startdate<=0){
				$end_time = get_utc_time('-20 minutes');
			}
			$end_time=str_replace("Z","",str_replace("T"," ",$end_time));
			echo "beginning_time[utc]:".$beginning_time;
			echo "end_time[utc]:".$end_time;
			//die();


			//$request->setLastUpdatedAfter(new DateTime('2013-01-21 08:43:14', new DateTimeZone('UTC')));
			//$request->setLastUpdatedBefore(new DateTime('2013-01-21 11:10:25', new DateTimeZone('UTC')));
			$request->setLastUpdatedAfter(new DateTime($beginning_time, new DateTimeZone('UTC')));
			$request->setLastUpdatedBefore(new DateTime($end_time, new DateTimeZone('UTC')));
			$orderStatuses = new MarketplaceWebServiceOrders_Model_OrderStatusList();
			$orderStatuses->setStatus(array('Unshipped','PartiallyShipped'));
			$request->setOrderStatus($orderStatuses);

			$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
			$marketplaceIdList->setId(array($amazon['MARKETPLACE_ID']));
			$request->setMarketplaceId($marketplaceIdList);
			$orderids=$this->invokeListOrders($service, $request);
			$all_order_id=$orderids[1];
			//var_dump($orderids);die();
			$next_token=$orderids[0];
			while($next_token!='')
			{
				$request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
				$request->setSellerId($amazon['MERCHANT_ID']);
				$request->setNextToken($orderids[0]);
				$orderids=$this->invokeListOrdersByNextToken($service, $request);
				$next_token=$orderids[0];
				$all_order_id=array_merge($all_order_id,$orderids[1]);
				//var_dump($orderids);
			}
			//var_dump($all_order_id);
			foreach($all_order_id as $order_id)
			{
				$order_info=array();
				$items=array();
				$data=array();
				$amazon_pdf_data=array();
				$amazon_ack_failed_data=array();
				$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
				$request->setSellerId($amazon['MERCHANT_ID']);
				$request->setAmazonOrderId(array($order_id));
				// object or array of parameters
				$order_info=$this->invokeGetOrder($service, $request);
				//var_dump($order_info);
				$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
				$request->setSellerId($amazon['MERCHANT_ID']);
				$request->setAmazonOrderId($order_id);
				// object or array of parameters
				$items=$this->invokeListOrderItems($service, $request);
				//var_dump($items);
				if(!isset($order_info['AmazonOrderId']))
				{
					$this->log_report($order_id);
					$amazon_ack_failed_data=array(
						'amazonorderid'=>$order_id,
						'sellerid'=>$amazon['MERCHANT_ID'],
						);
					if(!$this->order_model->check_amazon_ack_failed_exists($order_id))
					{
						$this->order_model->add_amazon_ack_failed($amazon_ack_failed_data);
					}
					continue;
				}
				
				$data=$this->_make_common_order_list_data($order_info,$items);
				//var_dump($data);
				
				if ($this->order_model->check_exists('order_list', array('transaction_id' => $data['transaction_id']))) {
					echo $data['transaction_id']."存在\n";
					/*
					$invoice_begin=2372;
					$invoice_id=$invoice_begin+$this->order_model->fetch_all_amazon_count()+1;
					$amazon_pdf_data=array(
						'amazonorderid'=>$order_info['AmazonOrderId'],
						'sellerid'=>$amazon['MERCHANT_ID'],
						'invoice_id'=>$invoice_id,
						);
					if(!$this->order_model->check_wait_create_amazon_pdf_exists($order_id))
					{
						$this->order_model->add_wait_create_amazon_pdf($amazon_pdf_data);
					}*/
				}else{
					$this->order_model->add_order($data);
					$invoice_begin=2372;
					$invoice_id=$invoice_begin+$this->order_model->fetch_all_amazon_count()+1;
					$amazon_pdf_data=array(
						'amazonorderid'=>$order_info['AmazonOrderId'],
						'sellerid'=>$amazon['MERCHANT_ID'],
						'invoice_id'=>$invoice_id,
						);
					if(!$this->order_model->check_wait_create_amazon_pdf_exists($order_id) && $order_info['AmazonOrderId']!='')
					{
						$this->order_model->add_wait_create_amazon_pdf($amazon_pdf_data);
					}
					
					echo $data['transaction_id']."保存成功\n";
				}sleep(2);
				
			}
			$this->order_model->update_amazon_import_beginning_time(array('value' => $end_time));
		}
	}
	private function log_report($AmazonOrderId)
        {
        $message = <<<MSG
        $AmazonOrderId
        amazon无法获取订单详情，请检查。
MSG;

        $data = array(
        'import_date' => date("Y-m-d H:i:s"),
        'user_name'   => 'amazon',
        'descript'    => $message,
        'user_login'  => 'amazon',
        );
        $this->paypal_model->import_log($data);
    }
	private function _make_common_order_list_data($transaction_details,$items)
	{
		if(!isset($transaction_details['AmazonOrderId']))
		{
			return;
		}
		$ship_to_country = $this->mixture_model->get_country_name_in_english_by_code(strtoupper($transaction_details['CountryCode']));
		$shipping_address=preg_replace("/\s/"," ",$transaction_details['AddressLine1']).' '.$transaction_details['AddressLine2'].' '.$transaction_details['AddressLine3'].' '.$transaction_details['County'].' '.$transaction_details['District'].' '.$transaction_details['City'].' '.$transaction_details['StateOrRegion'].' '.$ship_to_country;
		$import_date=date('Y-m-d H:i:s');
		$auction_sites=$transaction_details['MarketplaceId'];

		$item_titles=array();
		$item_ids=array();
		$item_qties=array();
		$item_codes=array();
		$transaction_details['discount_amount']=0;
		$transaction_details['shipping_amount']=0;
		
		foreach($items as $item)
		{
			$item_titles[]=$item['Title'];
			$item_ids[]=$item['OrderItemId'];
			$item_qties[]=(int)$item['QuantityOrdered'];
			$item_codes[]=$item['SellerSKU'];
			$transaction_details['discount_amount']=+$item['ShippingDiscountAmount'];
			$transaction_details['discount_amount']=+$item['PromotionDiscountAmount'];
			$transaction_details['shipping_amount']=+$item['ShippingAmount'];
		}
		
		$is_register='AMDD';
		$user='001';
		$item_no = $this->order_model->create_item_no($user, date("ymd"), substr($item[0]['OrderItemId'], -5), str_replace('-','',$transaction_details['AmazonOrderId']), $is_register);
		$sys_remark = $this->_create_sys_remark($import_date,$item_no);
		if($transaction_details['OrderStatus']=='PartiallyShipped')
		{
			$sys_remark.=" Order Status is PartiallyShipped!\n";
		}
		$data = array(
                    'list_date'                 => gmt_to_pdt($transaction_details['PurchaseDate'], 'D'),
                    'list_time'                 => gmt_to_pdt($transaction_details['PurchaseDate'], 'T'),
                    'time_zone'                 => 'PDT',
                    'name'                      => $transaction_details['Name'],
                    'item_title_str'            => implode(ITEM_TITLE_SEP, $item_titles),
                    'item_id_str'				=> implode(',', $item_ids),
                    'qty_str'                   => implode(',', $item_qties),
					'sku_str'                   => implode(',', $item_codes),
                    'currency'                  => $transaction_details['CurrencyCode'],
                    'gross'                     => $transaction_details['Amount'],
                    'fee'                       => $transaction_details['discount_amount'],
                    'net'                       => $transaction_details['Amount'] - $transaction_details['discount_amount'],
					'shippingamt'               => $transaction_details['shipping_amount'],
                    'note'                      => '',
                    'from_email'                => $transaction_details['BuyerEmail'],
                    'to_email'                  => 'yorbay.de@gmail.com',
                    'transaction_id'            => $transaction_details['AmazonOrderId'],
					'item_no'					=> $item_no,
                    'payment_type'              => $transaction_details['PaymentMethod'],
                    'counterparty_status'       => '',
                    'shipping_address'          => $shipping_address,
                    'address_status'            => '',
                    'shipping_handling_amount'  => '',
                    'insurance_amount'          => '',
                    'sales_tax'                 => '',
                    'auction_site'              => $transaction_details['SalesChannel'],
                    'buyer_id'                  => $transaction_details['BuyerName'],
                    'item_url'                  => '',
                    'closing_date'              => '',
                    'reference_txn_id'          => '',
                    'invoice_number'            => '',
                    'subscription_number'       => '',
                    'custom_number'             => '',
                    'receipt_id'                => '',
					'input_user'				=> $user,
					'is_register'				=> $is_register,
                    'sys_remark'                => $sys_remark,
                    'address_line_1'            => preg_replace("/\s/"," ",$transaction_details['AddressLine1']),
                    'address_line_2'            => preg_replace("/\s/"," ",$transaction_details['AddressLine2']),
                    'town_city'                 => $transaction_details['City'],
                    'state_province'            => $transaction_details['StateOrRegion'],
                    'zip_code'                  => $transaction_details['PostalCode'],
                    'country'                   => $ship_to_country,
                    'contact_phone_number'      => $transaction_details['Phone'],
                    'balance_impact'            => '',
                    'income_type'               => 'Amazon',
                    'input_date'                => $import_date,
                    'paid_time'                 => '',
					'order_status'				=> $this->order_statuses['wait_for_confirmation'],
                );

        return $data;
	}
	private function _create_sys_remark($import_date,$item_no)
    {
       
        $sys_remark = <<<DATA
		{$import_date}由程序导入本订单(amazon自动导单),编号为:{$item_no}, 进入待客服确认\n;
DATA;
        
        return $sys_remark;
    }
	public function import_amazon_ack_failed()
	{
		if (strpos($_SERVER['SCRIPT_FILENAME'], 'import_amazon_ack_failed.php') === FALSE)
        {
            exit;
        }
		$this->load->config('config_mws_api');
		$amazon_app = $this->config->item('amazon_app');
		//var_dump($amazon_app);
		$orders = $this->order_model->fetch_all_amazon_ack_failed();
		foreach($orders as $order)
		{
			$AmazonOrderId=$order->amazonorderid;
		foreach($amazon_app as $amazon)
		{
			$serviceUrl=$amazon['serviceUrl'];
			$amazon_api = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
			);
			if($amazon['MERCHANT_ID']!=$order->sellerid)
			{
				continue;
			}
			$service = new MarketplaceWebServiceOrders_Client(
				$amazon['AWS_ACCESS_KEY_ID'],
				$amazon['AWS_SECRET_ACCESS_KEY'],
				$this->config->item('APPLICATION_NAME'),
				$this->config->item('APPLICATION_VERSION'),
				$amazon_api);
			$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
			$request->setSellerId($amazon['MERCHANT_ID']);
			$request->setAmazonOrderId(array($AmazonOrderId));
			// object or array of parameters
			$order_info=$this->invokeGetOrder($service, $request);
			var_dump($order_info);
			$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
			$request->setSellerId($amazon['MERCHANT_ID']);
			$request->setAmazonOrderId($AmazonOrderId);
			// object or array of parameters
			$items=$this->invokeListOrderItems($service, $request);
			//var_dump($items);
			if($order_info["AmazonOrderId"]!='')
			{
				if (!$this->order_model->check_exists('order_list', array('transaction_id' => $data['transaction_id']))) {
					$data=$this->_make_common_order_list_data($order_info,$items);
					$this->order_model->add_order($data);

					$invoice_begin=2372;
					$invoice_id=$invoice_begin+$this->order_model->fetch_all_amazon_count()+1;
					$amazon_pdf_data=array(
						'amazonorderid'=>$order_info['AmazonOrderId'],
						'sellerid'=>$amazon['MERCHANT_ID'],
						'invoice_id'=>$invoice_id,
						);
					if(!$this->order_model->check_wait_create_amazon_pdf_exists($AmazonOrderId) && $order_info['AmazonOrderId']!='')
					{
						$this->order_model->add_wait_create_amazon_pdf($amazon_pdf_data);
					}
					$this->order_model->drop_amazon_ack_failed($AmazonOrderId);
				}
				
			}
		}
		break;
		
		}
	}

	public function create_amazon_pdf()
	{
		if (strpos($_SERVER['SCRIPT_FILENAME'], 'create_amazon_pdf.php') === FALSE)
        {
            exit;
        }
		$this->load->config('config_mws_api');
		$amazon_app = $this->config->item('amazon_app');
		//var_dump($amazon_app);
		$orders = $this->order_model->fetch_all_wait_create_amazon_pdf();
		foreach($orders as $order)
		{
			$AmazonOrderId=$order->amazonorderid;
		foreach($amazon_app as $amazon)
		{
			$serviceUrl=$amazon['serviceUrl'];
			$amazon_api = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
			);
			if($amazon['MERCHANT_ID']!=$order->sellerid)
			{
				continue;
			}
			$service = new MarketplaceWebServiceOrders_Client(
				$amazon['AWS_ACCESS_KEY_ID'],
				$amazon['AWS_SECRET_ACCESS_KEY'],
				$this->config->item('APPLICATION_NAME'),
				$this->config->item('APPLICATION_VERSION'),
				$amazon_api);
			$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
			$request->setSellerId($amazon['MERCHANT_ID']);
			$request->setAmazonOrderId(array($AmazonOrderId));
			// object or array of parameters
			$order_info=$this->invokeGetOrder($service, $request);
			var_dump($order_info);
			$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
			$request->setSellerId($amazon['MERCHANT_ID']);
			$request->setAmazonOrderId($AmazonOrderId);
			// object or array of parameters
			$items=$this->invokeListOrderItems($service, $request);
			//var_dump($items);
			if($order_info["AmazonOrderId"]!='')
			{
				$this->_create_amazon_pdf($order_info,$items);
				$amazon_pdf_data=array(
						'status'=>1,
						);
					if($this->order_model->check_wait_create_amazon_pdf_exists($AmazonOrderId))
					{
						$this->order_model->update_wait_create_amazon_pdf($AmazonOrderId,$amazon_pdf_data);
					}
					break;
			}
			
			
		}
		break;
		}
	}
	private function _create_amazon_pdf($transaction_details,$items)
	//public function test()
	{
		//var_dump($transaction_details);
		//var_dump($items);
		//die();
		if($transaction_details["AmazonOrderId"]==''){return;}
		
		$style = array(
			'position' => 'S',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => false,
			'cellfitalign' => '',
			'border' => false,
			'padding' => 0,
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => true,
			'font' => 'helvetica',
			'fontsize' => 12,
			'stretchtext' => 4
			);
		//$this->load->library('pdf');
		//$pagelayout = array($width,$height);
		//$my_tcpdf['page_format'] = $pagelayout;
		$my_tcpdf['page_orientation'] = 'L';
		$tcpdf['encoding'] = 'UTF-8';
		$this->load->library('pdf',$my_tcpdf);
		// set document information
		
        
		$this->pdf->SetCreator('Mallerp');
		$this->pdf->SetAuthor('Mansea');
		$this->pdf->SetTitle('amazon orders');
		$this->pdf->SetSubject('Mallerp');
		$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        // set font
        $this->pdf->SetFont('arialunicid0', '', 23);
		
$html0= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif; font-size:70;font-weight:bold;">Rechnung</span>
EOD;
$html1= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:20;">Yorbay eBusiness GmbH<br/>
Willerstwiete 17<br/>
D-22415 Hamburg</span>
EOD;
$html2= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">Yorbay eBusiness GmbH Willerstwiete 17 22415 Hamburg</span>
EOD;
$ship_to_country = $this->mixture_model->get_country_name_in_english_by_code(strtoupper($transaction_details['CountryCode']));
$shipping_address=$transaction_details['Name'].'<br/>';
$shipping_address.=isset($transaction_details['AddressLine1'])?preg_replace("/\s/"," ",$transaction_details['AddressLine1']).' ':'';
$shipping_address.=isset($transaction_details['AddressLine2'])?preg_replace("/\s/"," ",$transaction_details['AddressLine2']).' ':'';
$shipping_address.=isset($transaction_details['AddressLine3'])?preg_replace("/\s/"," ",$transaction_details['AddressLine3']).' ':'';
$shipping_address.=isset($transaction_details['County'])?$transaction_details['County'].' ':'';
$shipping_address.=isset($transaction_details['District'])?$transaction_details['District'].' ':'';
$shipping_address.='<br/>'.$transaction_details['PostalCode'].' ';
$shipping_address.=isset($transaction_details['City'])?$transaction_details['City'].' ':'';
$shipping_address.=isset($transaction_details['StateOrRegion'])?$transaction_details['StateOrRegion'].' ':'';
$shipping_address.='<br/>'.$ship_to_country;
$currencycode=($transaction_details['CurrencyCode']=='EUR')?'€':$transaction_details['CurrencyCode'];
$invoice_order=$this->order_model->fetch_wait_create_amazon_pdf($transaction_details["AmazonOrderId"]);
$invoice_id=$invoice_order->invoice_id;
if(strlen($invoice_id)<4)
{
	$invoice_id=str_pad($invoice_id,4,"0",STR_PAD_LEFT);
}
$html3= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:20;font-weight:bold;">{$shipping_address}<br/>
Telefon:{$transaction_details['Phone']}</span>
EOD;
$html4= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">Rechnung Nr.:0010{$invoice_id}</span>
EOD;
$date1=date("d-m-Y");
$html5= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">{$date1}</span>
EOD;
$html6= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">Sehr geehrte Damen und Herren,<br/>
gem. Ihrer Bestellung berechnen wir Ihnen folgenden Auftrag:</span>
EOD;
$html7= <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">Kunden-Nr.: <span style="color:green;">80{$invoice_id}</span></span>
EOD;
$date2=$transaction_details["PurchaseDate"];
$html8= <<<EOD
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-family:Arial, Helvetica, sans-serif;font-size:12;">
  <tr>
    <td width="5%">Pos</td>
    <td width="17%">Art-Nr.</td>
    <td width="44%">Bezeichnung</td>
    <td width="7%">Anz</td>
    <td width="7%">Mwst-Satz</td>
    <td width="7%">Netto Preis</td>
    <td width="13%">Zzgl.19% MwSt.t</td>
  </tr>
EOD;
$shipping_gross=0;
$grand_tatol=0;
foreach($items as $key=>$item)
{
	$pos=$key+1;
	$price=round($item['Amount']/$item['QuantityOrdered']/1.19,2);
	$price_tax=round($item['Amount']/$item['QuantityOrdered']/1.19*0.19,2);
	$shipping_gross+=$item['ShippingAmount'];
	$grand_tatol+=$item['Amount'];
$html8.= <<<EOD
	<tr>
    <td width="5%">{$pos}</td>
    <td width="17%">{$item['OrderItemId']}</td>
    <td width="44%">{$item['Title']}</td>
    <td width="7%">{$item['QuantityOrdered']}</td>
    <td width="7%">19%</td>
    <td width="7%">{$price}{$currencycode}</td>
    <td width="13%">{$price_tax}{$currencycode}</td>
  </tr>
EOD;
}
$shipping_amt=round($shipping_gross/1.19,2);
$shipping_fee=round($shipping_gross/1.19*0.19,2);
$html8.= <<<EOD
  <tr align="right">
    <td colspan="6">Gesamt</td>
    <td>{$grand_tatol}{$currencycode}</td>
  </tr>
<tr align="right">
    <td colspan="6">Versandkosten (Netto)</td>
    <td>{$shipping_amt}{$currencycode}</td>
  </tr>
<tr align="right">
    <td colspan="6">Versandkosten  (Mwst)</td>
    <td>{$shipping_fee}{$currencycode}</td>
  </tr>
<tr align="right" style="font-family:Arial, Helvetica, sans-serif;font-size:16;font-weight:bold;">
    <td colspan="6">Gesamtbetrag</td>
    <td>{$transaction_details['Amount']}{$currencycode}</td>
  </tr>
	<tr align="left">
    <td colspan="2">Bestellungsnummer</td>
    <td colspan="2">{$transaction_details['AmazonOrderId']}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
	<tr align="left">
    <td colspan="2">Versandart</td>
    <td>Packetversand</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
	<tr align="left">
    <td colspan="7">Vielen Dank für Ihren Auftrag am {$date2}</td>
  </tr>
</table>
EOD;
$html9= <<<EOD
<hr><span style="font-family:Arial, Helvetica, sans-serif;font-size:12;">Ust-ID: DE283478018    SteuerNr.: 49 / 769 / 00648</span>
EOD;
		$this->pdf->AddPage('P','A4');
		//$this->pdf->writeHTMLCell($w=200, $h=290, $x=1, $y=1, '', $border=1, $ln=1, $fill=0, $reseth=false, $align='L', $autopadding=false);
		$this->pdf->writeHTMLCell($w=0, $h=0, $x=5, $y=5, $html0, $border=0, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=0, $h=0, $x=50, $y=40, $html1, $border=0, $ln=0, $fill=0, $reseth=true, $align='R', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=70, $html2, $border=0, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=80, $html3, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=130, $html4, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=30, $h=0, $x=170, $y=130, $html5, $border=0, $ln=0, $fill=0, $reseth=true, $align='R', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=140, $html6, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=60, $h=0, $x=140, $y=150, $html7, $border=0, $ln=0, $fill=0, $reseth=true, $align='R', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=160, $html8, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=275, $html9, $border=0, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=true);
		$filename="/var/www/html/mallerp/static/amazon_pdf/".$transaction_details["AmazonOrderId"].".pdf";
		$this->pdf->Output($filename, 'F');
	}

    
}
