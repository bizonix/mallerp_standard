<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/sale/myebay'.EXT;

class Myebay_order extends Myebay 
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('paypal');
        $this->load->model('ebay_order_model');
        $this->load->model('epacket_model');
		$this->load->model('paypal_model');
		$this->load->library('script');
    }

    public function get_orders($ebay_id)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_ebay_orders.php') === FALSE)
        {
            exit;
        }

        if (empty($ebay_id))
        {
            return;
        }
        $itemsPerPage = 100;
        $pageIndex = 1;
        $begin_time = $this->ebay_order_model->get_order_begin_time($ebay_id);
        //$end_time = get_current_utc_time();
		$end_time=date('Y-m-d\TH:i:s\Z',mktime(substr($begin_time,11,2)+48,substr($begin_time,14,2),substr($begin_time,17,2),substr($begin_time,5,2),substr($begin_time,8,2),substr($begin_time,0,4)));
		$begin_time = date('Y-m-d\TH:i:s\Z',mktime(substr($begin_time,11,2)-1,substr($begin_time,14,2),substr($begin_time,17,2),substr($begin_time,5,2),substr($begin_time,8,2),substr($begin_time,0,4)));
		
		$startdate=strtotime($end_time);
		$enddate=strtotime(get_current_utc_time());
		if($enddate-$startdate<=0){
			$end_time = get_current_utc_time();
		}

        if (empty($begin_time))
        {
            return;
        }
        echo "starttime: $begin_time\n";
		echo "end_time: $end_time\n";
        echo "starting $ebay_id\n";

        do
        {
            list($total_pages, $orders) = $this->_proccess_get_orders($ebay_id, $itemsPerPage, $pageIndex, $begin_time, $end_time);

            foreach ($orders as $order)
            {
                $this->ebay_order_model->save_ebay_order($order);
            }
            echo "total page: $total_pages, page index: $pageIndex\n";
            $pageIndex++;
        }
        while($pageIndex <= $total_pages);

        $this->_process_get_auction_listing_fee($ebay_id, $begin_time, $end_time);

        $this->ebay_order_model->update_order_begin_time($ebay_id, $end_time);

        echo "finish $ebay_id\n";
    }

    private function _proccess_get_orders($ebay_id, $itemsPerPage, $pageIndex, $begin_time, $end_time)
    {
        $site = 'US';

        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
        $itemInfo = array(
            'site'          => $site,
            'itemsPerPage'  => $itemsPerPage,
            'pageIndex'     => $pageIndex,
            'begin_time'    => $begin_time,
            'end_time'      => $end_time,
        );

        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetOrders';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = get_ebay_orders($this->appToken, $itemInfo);
        $response = $this->xmlRequest($this->ebaysession, $xml);

        $total_pages = 0;
        $orders = array();
        if ( (! isset($response->Ack)) OR ($response->Ack != 'Success' && $response->Ack != 'Warning'))
        {
            echo "Error\n";
            var_dump($response);
            die('');
        }
		
        if (isset($response->OrderArray->Order)) {
            foreach ($response->OrderArray->Order as $order)
            {
                $price_attr = $order->AmountPaid->attributes();
                $currency_code = '';
                foreach ($price_attr as $key => $value)
                {   
                    if ($key == 'currencyID')
                    { 
                        $currency_code = (string)$value;
                        break;
                    }
                }


                $buyer_id       = (string)$order->BuyerUserID;
				$buyercheckoutmessage       = isset($order->BuyerCheckoutMessage)?(string)$order->BuyerCheckoutMessage:'';
                $paid_time      = (string)$order->PaidTime;
				$order_id		= (string)$order->OrderID;
				
                $amount_paid    = (string)$order->AmountPaid;
                $order_status   = (string)$order->OrderStatus;
                $name           = (string)$order->ShippingAddress->Name;
                $phone          = (string)$order->ShippingAddress->Phone;
                $postal_code    = (string)$order->ShippingAddress->PostalCode;
                $country        = (string)$order->ShippingAddress->CountryName;
                $province       = (string)$order->ShippingAddress->StateOrProvince;
                $city           = (string)$order->ShippingAddress->CityName;
                $street1        = (string)$order->ShippingAddress->Street1;
                $street2        = isset($order->ShippingAddress->Street2) ? 
                    (string)$order->ShippingAddress->Street2 : '';
                $paypal_tid     = isset($order->ExternalTransaction->ExternalTransactionID) ?
                    (string)$order->ExternalTransaction->ExternalTransactionID : '';
               
                
				/*德国站点需要抓取ebay的付款状态*/
				$checkoutstatus='';
				$completestatus='';
				$ebaypaymentmismatchdetails='';
				$mismatchtype='';
				$actionrequiredby='';
				
				/*新增字段*/
				$ebaypaymentstatus=(string)$order->CheckoutStatus->eBayPaymentStatus;
				$lasttimemodified=(string)$order->CheckoutStatus->LastModifiedTime;
				$paymentmethodused=(string)$order->CheckoutStatus->PaymentMethod;
				$paymentholdstatus=(string)$order->CheckoutStatus->Status;
				$integratedmerchantcreditcardenabled=(string)$order->CheckoutStatus->IntegratedMerchantCreditCardEnabled;
				$shipped_time=(string)$order->ShippedTime;
				$shippingservice = (string)$order->ShippingServiceSelected->ShippingService;
				if (isset($order->TransactionArray->Transaction)) {
					foreach ($order->TransactionArray->Transaction as $transaction)
					{
						$buyer_email = (string)$transaction->Buyer->Email;
						$orderlineitemid= (string)$transaction->OrderLineItemID;
						$tid = (string)$transaction->TransactionID;
						$transaction_price = (string)$transaction->TransactionPrice;
						$created_time = (string)$transaction->CreatedDate;
                		$fvf_attr = $transaction->FinalValueFee->attributes();
                		$fvf_currency_code = $currency_code;
                		if (is_array($fvf_attr))
                		{
                    		foreach ($fvf_attr as $key => $value)
                    		{   
                        		if ($key == 'currencyID')
                        		{ 
                            		$fvf_currency_code = (string)$value;
                            		break;
                        		}
                    		}
                		}
               			$fvf = (string)$transaction->FinalValueFee;
						$sales_qty=(int)$transaction->QuantityPurchased;//mansea新增加
						$salesrecordnumber=(int)$transaction->ShippingDetails->SellingManagerSalesRecordNumber;
                		$item_id = (string)$transaction->Item->ItemID;
                		if (isset($transaction->Variation->VariationTitle)) {
                    		$item_title = (string)$transaction->Variation->VariationTitle;
                    		$sku_str = (string)$transaction->Variation->SKU;
                		}
                		else
                		{
                   			$item_title = (string)$transaction->Item->Title;
                    		$sku_str = (string)$transaction->Item->SKU;
                		}
						$orders[] = array(
                   			'buyer_id'              => $buyer_id,
                    		'paid_time'             => $paid_time,
                    		'amount_paid'           => $amount_paid,
                    		'currency'              => $currency_code,
                    		'buyer_name'            => $name,
                    		'country'               => $country,
                    		'province'              => $province,
                    		'city'                  => $city,
                    		'street1'               => $street1,
                    		'street2'               => $street2,
                   			'phone'                 => $phone,
                    		'postal_code'           => $postal_code,
                    		'order_status'          => $order_status,
                    		'paypal_transaction_id' => $paypal_tid,
                    		'transaction_id'        => $tid,
                    		'transaction_price'     => $transaction_price,
                    		'fvf_currency'          => $fvf_currency_code,
                    		'final_value_fee'       => $fvf,
                    		'item_id'               => $item_id,
                    		'item_title'            => $item_title,
                    		'sku_str'               => $sku_str,
                    		'ebay_id'               => $ebay_id,
							'quantitysold'			=> $sales_qty,
							'salesrecordnumber'		=> $salesrecordnumber,
							'order_id'				=> $order_id,
							'orderlineitemid'		=> $orderlineitemid,
							'checkoutstatus'		=>	$checkoutstatus,
							'completestatus'		=>	$completestatus,
							'ebaypaymentmismatchdetails'=>	$ebaypaymentmismatchdetails,
							'paymentmethodused'		=>	$paymentmethodused,
							'paymentholdstatus'		=>	$paymentholdstatus,
							'lasttimemodified'		=>	$lasttimemodified,
							'integratedmerchantcreditcardenabled'=>	$integratedmerchantcreditcardenabled,
							'ebaypaymentstatus'		=>	$ebaypaymentstatus,
							'mismatchtype'			=>	$mismatchtype,
							'actionrequiredby'		=>	$actionrequiredby,
							'shipped_time'			=>	$shipped_time,
							'buyer_email'			=>	$buyer_email,
							'order_created_date'	=>	$created_time,
							'shippingservice'		=>	$shippingservice,
							'buyercheckoutmessage'	=>	$buyercheckoutmessage,
                		);
						//echo "<pre>";var_dump($orders);echo "<pre>";die("**************");
					}
				}
                
				//echo "order id".$order_id ."\n";

            }
            $total_pages = $response->PaginationResult->TotalNumberOfPages;
        }

        return array($total_pages, $orders);
    }

    private function _process_get_auction_listing_fee($ebay_id, $begin_time, $end_time) {
        $app_tokens = $this->config->item('appToken');
        $app_token = $app_tokens[$ebay_id];
        $itemsPerPage = 200;
        $pageIndex = 1;

        $this->ebay_config['siteToUseID'] = 0;
        $this->ebay_config['callName'] = 'GetAccount';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);
        $this->ebaysession->init($this->ebay_config);
        $has_more = true;

        do
        {
            $itemInfo = array(
                'itemsPerPage' => $itemsPerPage,
                'pageIndex'    => $pageIndex,
                'beginTime'    => $begin_time,
                'endTime'      => $end_time,
            );

            $xml = get_account($app_token, $itemInfo);
            $resp = $this->xmlRequest($this->ebaysession, $xml);

            if ( ! isset($resp->Ack) || $resp->Ack == 'Failure') {
                echo "Listing Fee Error\n";
                die('');
            }

            $total_pages = $resp->PaginationResult->TotalNumberOfPages;

            if ( ! isset($resp->AccountEntries))
            {
                break;
            }

            $accounts = $resp->AccountEntries->AccountEntry;
            foreach ($accounts as $account)
            {
                // check the listing fee.
                if ($account->AccountDetailsEntryType == 'FeeInsertion')
                {
                    $amount_attr = $account->GrossDetailAmount->attributes();
                    $currency_code = '';
                    foreach ($amount_attr as $key => $value)
                    {
                        if ($key == 'currencyID')
                        { 
                            $currency_code = (string)$value;
                            break;
                        }
                    }
                    $listing_fee = (string)$account->GrossDetailAmount;
                    $item_id = (string)$account->ItemID;

                    echo "item id: " . $item_id . "\n";
                    $data = array(
                        'item_id'               => $item_id,
                        'ebay_id'               => $ebay_id,
                        'listing_fee'           => $listing_fee,
                        'listing_fee_currency'  => $currency_code,
                    );
                    $this->ebay_order_model->save_myebay_listing_fee($data);
                }
            }

            $pageIndex++;
        } 
        while ($pageIndex <= $total_pages);

        return TRUE;
    }

    public function complete_sale($order_id)
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'complete_ebay_sale.php') === FALSE)
        {
            exit;
        }
        $this->_complete_sale($order_id);
    }

    public function batch_complete_sale()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'batch_complete_ebay_sale.php') === FALSE)
        {
            exit;
        }
        $orders = $this->ebay_order_model->get_order_ids();
        
        foreach ($orders as $order)
        {
            $this->_complete_sale($order->order_id);
        }  
    }

    private function _complete_sale($order_id)
    {
        $order = $this->order_model->get_order($order_id);
        if (empty($order))
        {
            return;
        }
        // skip Epacket order
		if ($order->is_register=='H')
        {
			$this->ebay_order_model->delete_order_id($order_id);
            return;
        }
		if ($order->auction_site_type == 'mallerp')
        {
			$this->ebay_order_model->delete_order_id($order_id);
            return;
        }
        if ($order->auction_site_type == 'zencart')
        {
			$this->script->complete_zencart_sale(array('order_id' => $order->id));
            return;
        }
		if ($order->auction_site_type == 'wish')
        {
			$this->script->complete_wish_sale(array('order_id' => $order->id));
            return;
        }
		if ($order->auction_site_type == 'aliexpress')
        {
			$this->script->complete_aliexpress_sale(array('order_id' => $order->id));
            return;
        }
        if (empty($order->to_email))
        {
            //return;
        }
        $ebay_ids = $this->config->item('ebay_id');
        $ebay_id = $order->ebay_id;
        if (empty($ebay_id))
        {
			//$ebay_id = $ebay_ids[$order->to_email];
				//if (empty($ebay_id))
				//{
					echo $order->id."ebay id is empty!\n";
					return;
				//}
        }

        $item_ids = explode(',', $order->item_id_str);
        $item_titles = explode(ITEM_TITLE_SEP, $order->item_title_str);
        $track_numbers = explode(',', $order->track_number);
		/*
        $merged_transaction_id = NULL;
        if ($order->is_merged)
        {
            $merged_order = $this->ebay_order_model->fetch_merged_order($order->id);
            if (isset($merged_order->transaction_id))
            {
                $merged_transaction_id = $merged_order->transaction_id;
            }
        }*/
		$ebay_orders=$this->ebay_order_model->get_ebay_order_by_paypal_transaction_id($order->transaction_id);

        $i = 0;
        //foreach ($item_titles as $item_title)
		foreach ($ebay_orders as $ebay_order)
        {
            $item_id = $ebay_order->item_id;
            if (empty($item_id))
            {
                continue;
            }
            $ebay_transaction_id = $ebay_order->transaction_id;
            if ($ebay_transaction_id === NULL)
			{
				continue;
            }
           
            $shipping_method = shipping_method($order->is_register);
            $shipping_carrier = $this->_shipping_carrier($shipping_method, $order->country);
            $shipping_date = get_utc_time($order->ship_confirm_date);
            $shipping_note = $shipping_method->name_en;

            $data = array(
                'order_id'          => $order_id,
                'ebay_id'           => $ebay_id,
                'track_numbers'     => $track_numbers,
                'item_id'           => $item_id,
                'transaction_id'    => $ebay_transaction_id,
                'shipping_carrier'  => $shipping_carrier,
                'shipping_date'     => $shipping_date,
                'shipping_note'     => $shipping_note,
            );
            $this->_proccess_complete_sale($data);
        }
    }

    private function _shipping_carrier($shipping_method, $country)
    {
        $carrier = $shipping_method->name_en;

        return $carrier;
    }

    private function _proccess_complete_sale($itemInfo)
    {
        $site = 'US';

        $ebay_id = $itemInfo['ebay_id'];
        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'CompleteSale';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = complete_sale($this->appToken, $itemInfo);
        echo $itemInfo['order_id'];
        var_dump($xml);
        $response = $this->xmlRequest($this->ebaysession, $xml);
		var_dump($response);
        if ( ! isset($response->Ack) || $response->Ack == 'Failure')
        {
            $this->ebay_order_model->save_wait_complete_sale($itemInfo['order_id']);
        }
		if($response->Ack == 'Success')
		{
			$this->ebay_order_model->delete_order_id($itemInfo['order_id']);
		}
		$filename = '/var/www/html/log/ebay/';
		if (!file_exists($filename))
        {
            mkdir($filename);
        }
		$filename .= date('Y-m-d').'.log';
		$requestInformation=$xml;
		writefile($filename, $requestInformation, 'a');
		$requestInformation=var_export($response,true);
		writefile($filename, $requestInformation, 'a');
    }

	public function complete_sale_merged_orders(){
		$all_wait_complete_merged_orders_transaction_ids=$this->order_model->fetch_all_wait_complete_merged_orders();
		var_dump($all_wait_complete_merged_orders_transaction_ids);
		foreach($all_wait_complete_merged_orders_transaction_ids as $all_wait_complete_merged_orders_transaction_id){
			
			$ebay_orders=$this->ebay_order_model->get_ebay_order_by_paypal_transaction_id($all_wait_complete_merged_orders_transaction_id['transaction_id']);
			$order = $this->order_model->get_order($all_wait_complete_merged_orders_transaction_id['old_id']);
			
			if (empty($order)){
				continue;
			}
			if ($order->auction_site_type == 'wish')
        	{
				echo "wish merged order:".$order->id."\n";
				$this->script->complete_wish_sale(array('order_id' => $order->id));
            	continue;
        	}

			//echo "********0";var_dump($ebay_orders);
			$ebay_ids = $this->config->item('ebay_id');
			$ebay_id = $order->ebay_id;
			if (empty($ebay_id))
			{
				//$ebay_id = $ebay_ids[$order->to_email];
				//if (empty($ebay_id))
				//{
					echo $order->id."ebay id is empty!\n";
					continue;
				//}
			}
			$item_ids = explode(',', $order->item_id_str);
			$item_titles = explode(ITEM_TITLE_SEP, $order->item_title_str);
			$track_numbers = explode(',', $order->track_number);
			$order_id=$all_wait_complete_merged_orders_transaction_id['old_id'];

			//echo "********1";var_dump($ebay_orders);
			foreach($ebay_orders as $ebay_order){
				$shipping_method = shipping_method($order->is_register);
				$shipping_carrier = $this->_shipping_carrier($shipping_method, $order->country);
				$shipping_date = get_utc_time($order->ship_confirm_date);
				$shipping_note = $shipping_method->name_en;
				$data = array(
					'order_id'          => $order_id,
					'ebay_id'           => $ebay_id,
					'track_numbers'     => $track_numbers,
					'item_id'           => $ebay_order->item_id,
					'transaction_id'    => $ebay_order->transaction_id,
					'shipping_carrier'  => $shipping_carrier,
					'shipping_date'     => $shipping_date,
					'shipping_note'     => $shipping_note,
					);
				echo "-----";print_r($data);
				$this->_proccess_complete_sale($data);
				$data_order=array('is_shiped_ebay'=>1);
				$this->order_model->update_order_merged_list_information($all_wait_complete_merged_orders_transaction_id['transaction_id'],$data_order);
			}
			
		}
	}


	public function get_has_ebay_order()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'get_has_ebay_order.php') === FALSE)
        {
            exit;
        }
        $orders = $this->ebay_order_model->get_has_ebay_orders();
		$site = 'US';
        
        foreach ($orders as $order)
        {
			$ebay_id = $order->ebay_id;
			if (empty($ebay_id))
			{
				$ebay_id = $ebay_ids[$order->to_email];
				if (empty($ebay_id))
				{
					return;
				}
			}
			if ($this->config->item('production'))
			{
				$this->appToken = $this->config->item('appToken');
				$this->appToken = $this->appToken[$ebay_id];
			}
			$this->ebay_config['siteToUseID'] = get_site_id($site);
			$this->ebay_config['callName'] = 'GetOrderTransactions';
			$this->load->library('ebayapi/EbaySession', $this->ebay_config);
			$xml = get_transaction_by_id($this->appToken, $order->transaction_id,$order->item_id);
			var_dump($xml);
			$response = $this->xmlRequest($this->ebaysession, $xml);
			var_dump($response);
			die("zhaosenlin");

			echo $order->transaction_id.'\n';
            //$this->_complete_sale($order->order_id);
        }  
    }
	/*2012-06德国订单付款方式改变*/
	public function get_need_ship_orders()
	{
		$order_ids = $this->ebay_order_model->get_need_ship_ebay_orders_ids();
		foreach($order_ids as $order)
		{
			echo $order->order_id."======\n";
			$new_order=array();
			$orders=$this->ebay_order_model->get_ebay_order_by_order_id($order->order_id);
			foreach($orders as $order_info)
			{
				//var_dump($order_info);
				$import_date=date("Y-m-d H:i:s");
				if(!isset($new_order[$order_info->order_id]))
				{
					$shipping_address = $order_info->buyer_name ." ".$order_info->street1." ".$order_info->street2." ".$order_info->city." ".$order_info->province." ".$order_info->country;
					$paypal_emails = $this->config->item('ebay_id');
					foreach($paypal_emails as $key=>$value)
					{
						if($value==$order_info->ebay_id){
							$to_email=$key;
							break;
						}
					}
					$order_created_date=(!empty($order_info->order_created_date))?$order_info->order_created_date:$order_info->paid_time;
					$data = array(
						'list_date'                 => gmt_to_pdt($order_created_date, 'D'),
						'list_time'                 => gmt_to_pdt($order_created_date, 'T'),
						'time_zone'                 => 'PDT',
						'name'                      => $order_info->buyer_name,
						'payment_status'            => $order_info->checkoutstatus,
						'currency'                  => $order_info->currency,
						'gross'                     => $order_info->amount_paid,
						'fee'                       => $order_info->final_value_fee,
						'net'                       => $order_info->amount_paid - $order_info->final_value_fee,
						'shippingamt'               => $order_info->amount_paid - ($order_info->transaction_price)*($order_info->quantitysold),
						'from_email'                => $order_info->buyer_email,
						'to_email'                  => $to_email,
						'transaction_id'            => $order_info->order_id,
						'payment_type'              => $order_info->paymentmethodused,
						'counterparty_status'       => $order_info->paymentmethodused,
						'shipping_address'          => $shipping_address,
						'auction_site'              => 'Ebay',
						'buyer_id'                  => $order_info->buyer_id,
						'address_line_1'            => $order_info->street1,
						'address_line_2'            => $order_info->street2,
						'town_city'                 => $order_info->city,
						'state_province'            => $order_info->province,
						'zip_code'                  => $order_info->postal_code,
						'country'                   => $order_info->country,
						'contact_phone_number'      => $order_info->phone,
						'balance_impact'            => 'CREDIT',
						'income_type'               => 'Ebay',
						'input_date'                => $import_date,
						'paid_time'                 => $order_info->paid_time,
						'ebay_id'					=> $order_info->ebay_id,
						);
						$item_id_str=$order_info->item_id;
						$item_title_str=$order_info->item_title;
						$qty_str=$order_info->quantitysold;
						$new_order[$order_info->order_id] = $order_info->orderlineitemid;
				}else{
						$item_id_str.=','.$order_info->item_id;
						$item_title_str.=ITEM_TITLE_SEP.$order_info->item_title;
						$qty_str.=','.$order_info->quantitysold;
				}
			}
			$order_status = 0;
			$is_register = 'PD';
			$user_obj = $this->paypal_model->fetch_user_by_paypal_email($data['to_email']);
			$data['is_register'] = $is_register;
			$data['item_title_str'] = $item_title_str;
			$data['item_id_str'] = $item_id_str;
			$data['qty_str'] = $qty_str;
			//$data['input_user'] = $this->item_info['user'];
			//$data['sku_str'] = implode(',', $this->item_info['item_codes']);
			$data['order_status'] = $order_status;
			$data['item_no'] = $this->order_model->create_item_no($user_obj->login_name, date("ymd"), substr($data['item_id_str'], -5), $data['transaction_id'], $is_register);
			$data['sys_remark']=sprintf(lang('my_ebay_order_import_sys_remark'), date('Y-m-d H:i:s'), $data['item_no']);
			//$data['sys_remark'] = $this->_create_sys_remark('wait_for_handle', $data['transaction_id']);
			var_dump($data);
			$order_id = $this->paypal_model->save_order_list($data, FALSE);
			$my_ebay_order_data=array('is_import'=>1);
			$this->ebay_order_model->update_ebay_order($order->order_id,$my_ebay_order_data);
		//更新myebay_order_list的is_import为1防止下次在导入。
		/*添加新订单*/
		}
	}
}
