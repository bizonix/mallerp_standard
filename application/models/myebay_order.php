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
		$end_time=date('Y-m-d\TH:i:s\Z',mktime(substr($begin_time,11,2)+24,substr($begin_time,14,2),substr($begin_time,17,2),substr($begin_time,5,2),substr($begin_time,8,2),substr($begin_time,0,4)));
		$begin_time = date('Y-m-d\TH:i:s\Z',mktime(substr($begin_time,11,2)-12,substr($begin_time,14,2),substr($begin_time,17,2),substr($begin_time,5,2),substr($begin_time,8,2),substr($begin_time,0,4)));
		
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
        $this->ebay_config['callName'] = 'GetSellerTransactions';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = get_seller_transactions($this->appToken, $itemInfo);
        $response = $this->xmlRequest($this->ebaysession, $xml);

        $total_pages = 0;
        $orders = array();
        if ( (! isset($response->Ack)) OR ($response->Ack != 'Success' && $response->Ack != 'Warning'))
        {
            echo "Error\n";
            var_dump($response);
            die('');
        }

        if (isset($response->TransactionArray->Transaction)) {
            foreach ($response->TransactionArray->Transaction as $order)
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


                $buyer_id       = (string)$order->Buyer->UserID;
                $paid_time      = (string)$order->PaidTime;
				$order_id		= (string)$order->ContainingOrder->OrderID;
				$orderlineitemid= (string)$order->OrderLineItemID;
                $amount_paid    = (string)$order->AmountPaid;
                $order_status   = (string)$order->ContainingOrder->OrderStatus;
                $name           = (string)$order->Buyer->BuyerInfo->ShippingAddress->Name;
                $phone          = (string)$order->Buyer->BuyerInfo->ShippingAddress->Phone;
                $postal_code    = (string)$order->Buyer->BuyerInfo->ShippingAddress->PostalCode;
                $country        = (string)$order->Buyer->BuyerInfo->ShippingAddress->CountryName;
                $province       = (string)$order->Buyer->BuyerInfo->ShippingAddress->StateOrProvince;
                $city           = (string)$order->Buyer->BuyerInfo->ShippingAddress->CityName;
                $street1        = (string)$order->Buyer->BuyerInfo->ShippingAddress->Street1;
                $street2        = isset($order->Buyer->BuyerInfo->ShippingAddress->Street2) ? 
                    (string)$order->Buyer->BuyerInfo->ShippingAddress->Street2 : '';
                $paypal_tid     = isset($order->ExternalTransaction->ExternalTransactionID) ?
                    (string)$order->ExternalTransaction->ExternalTransactionID : '';
                
                $tid = (string)$order->TransactionID;
                $transaction_price = (string)$order->TransactionPrice;
                $fvf_attr = $order->FinalValueFee->attributes();
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
                $fvf = (string)$order->FinalValueFee;
				$sales_qty=(int)$order->QuantityPurchased;//mansea新增加
				$salesrecordnumber=(int)$order->ShippingDetails->SellingManagerSalesRecordNumber;
                $item_id = (string)$order->Item->ItemID;
                if (isset($order->Variation->VariationTitle)) {
                    $item_title = (string)$order->Variation->VariationTitle;
                    $sku_str = (string)$order->Variation->SKU;
                }                    
                else
                {
                   $item_title = (string)$order->Item->Title;
                    $sku_str = (string)$order->Item->SKU;
                }
				/*德国站点需要抓取ebay的付款状态*/
				$checkoutstatus=(string)$order->Status->CheckoutStatus;
				$completestatus=(string)$order->Status->CompleteStatus;
				$ebaypaymentmismatchdetails=(string)$order->Status->eBayPaymentMismatchDetails;
				$paymentmethodused=(string)$order->Status->PaymentMethodUsed;
				$paymentholdstatus=(string)$order->Status->PaymentHoldStatus;
				$lasttimemodified=(string)$order->Status->LastTimeModified;
				$integratedmerchantcreditcardenabled=(string)$order->Status->IntegratedMerchantCreditCardEnabled;
				$ebaypaymentstatus=(string)$order->Status->eBayPaymentStatus;
				$mismatchtype=(string)$order->Status->eBayPaymentMismatchDetails->MismatchType;
				$actionrequiredby=(string)$order->Status->eBayPaymentMismatchDetails->ActionRequiredBy;
				$shipped_time=(string)$order->ShippedTime;
				/*新增字段*/
				$buyer_email=(string)$order->Buyer->Email;
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
                );
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
        if ($order->is_register == 'H')
        {
            return;
        }
        if (empty($order->to_email))
        {
            return;
        }
        $ebay_ids = $this->config->item('ebay_id');
        $ebay_id = $order->ebay_id;
        if (empty($ebay_id))
        {
			$ebay_id = $ebay_ids[strtolower($order->to_email)];
			if (empty($ebay_id))
			{
				return;
			}
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
        $carrier = $shipping_method->taobao_company_code;

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
    }

	public function complete_sale_merged_orders(){
		$all_wait_complete_merged_orders_transaction_ids=$this->order_model->fetch_all_wait_complete_merged_orders();
		var_dump($all_wait_complete_merged_orders_transaction_ids);
		foreach($all_wait_complete_merged_orders_transaction_ids as $all_wait_complete_merged_orders_transaction_id){
			
			$ebay_orders=$this->ebay_order_model->get_ebay_order_by_paypal_transaction_id($all_wait_complete_merged_orders_transaction_id['transaction_id']);
			$order = $this->order_model->get_order($all_wait_complete_merged_orders_transaction_id['old_id']);
			if (empty($order)){
				return;
			}
			// skip Epacket order
			if ($order->is_register == 'H')
			{
				return;
			}
			if (empty($order->to_email))
			{
				return;
			}
			$ebay_ids = $this->config->item('ebay_id');
			$ebay_id = $order->ebay_id;
			if (empty($ebay_id))
			{
				$ebay_id = $ebay_ids[$order->to_email];
				if (empty($ebay_id))
				{
					return;
				}
			}
			$item_ids = explode(',', $order->item_id_str);
			$item_titles = explode(ITEM_TITLE_SEP, $order->item_title_str);
			$track_numbers = explode(',', $order->track_number);
			$order_id=$all_wait_complete_merged_orders_transaction_id['old_id'];


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
				print_r($data);
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
				if(isset($new_order[$order_info->order_id]))
				{
				}else{
				}
			}
			/*添加新订单*/
		}
	}
}
