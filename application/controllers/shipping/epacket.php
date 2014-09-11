<?php
require_once APPPATH . 'controllers/mallerp_no_key' . EXT;
class sku
{

    public $CustomsTitleCN;
    public $CustomsTitleEN;
    public $DeclaredValue;
    public $OriginCountryName;
    public $OriginCountryCode;
    public $SKUID;
    public $Weight;

}
class item
{
    public $EBayItemID;
    public $EBayTransactionID;
    public $EBaySiteID;
    public $OrderSalesRecordNumber;
    public $PaymentDate;
    public $PostedQTY;
    public $ReceivedAmount;
    public $SalesRecordNumber;
    public $SKU;
    public $SoldDate;
    public $SoldPrice;
    public $SoldQTY;
}
class Epacket extends Mallerp_no_key
{
    private $user;
    private $token;
	private $is_register;
    protected $order_statuses = array();
    public function __construct()
    {
        parent::__construct();

        $this->user = 'Mallerp';
        $this->config->load('config_epacket');
        $this->load->model('epacket_model');
        $this->load->model('order_model');
		$this->load->model('product_model');
		$this->load->model('product_makeup_sku_model');
		$this->load->model('ebay_order_model');
		$this->load->library('script');
		$this->is_register=(!empty($this->is_register))?$this->is_register:'H';
		$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($this->is_register);

        // pick up address
        $this->load->library('epacket/PickUpAddress');
        $this->pickUpAddress->Company = $epacket_config->pickupaddress_company;
        $this->pickUpAddress->Contact = $epacket_config->pickupaddress_contact;
        $this->pickUpAddress->Email = $epacket_config->pickupaddress_email;
        $this->pickUpAddress->Mobile = $epacket_config->pickupaddress_mobile;
        $this->pickUpAddress->Phone = $epacket_config->pickupaddress_phone;
        $this->pickUpAddress->Postcode = $epacket_config->pickupaddress_postcode;
        $this->pickUpAddress->Country = $epacket_config->pickupaddress_country;
        $this->pickUpAddress->Province = $epacket_config->pickupaddress_province;
        $this->pickUpAddress->City = $epacket_config->pickupaddress_city;
        $this->pickUpAddress->District = $epacket_config->pickupaddress_district;
        $this->pickUpAddress->Street = $epacket_config->pickupaddress_street;

        // ship from address
        $this->load->library('epacket/ShipFromAddress');
        $this->shipFromAddress->Company = $epacket_config->shipfromaddress_company;
        $this->shipFromAddress->Contact = $epacket_config->shipfromaddress_contact;
        $this->shipFromAddress->Email = $epacket_config->shipfromaddress_email;
        $this->shipFromAddress->Mobile = $epacket_config->shipfromaddress_mobile;
        $this->shipFromAddress->Postcode = $epacket_config->shipfromaddress_postcode;
        $this->shipFromAddress->Country = $epacket_config->shipfromaddress_country;
        $this->shipFromAddress->Province = $epacket_config->shipfromaddress_province;
        $this->shipFromAddress->City = $epacket_config->shipfromaddress_city;
        $this->shipFromAddress->District = $epacket_config->shipfromaddress_district;
        $this->shipFromAddress->Street = $epacket_config->shipfromaddress_street;

		// Return Address
        $this->load->library('epacket/ReturnAddress');
        $this->ReturnAddress->Company = $epacket_config->returntoaddress_company;
        $this->ReturnAddress->Contact = $epacket_config->returntoaddress_contact;
        $this->ReturnAddress->Postcode = $epacket_config->returntoaddress_postcode;
        $this->ReturnAddress->Country = $epacket_config->returntoaddress_country;
        $this->ReturnAddress->Province = $epacket_config->returntoaddress_province;
        $this->ReturnAddress->City = $epacket_config->returntoaddress_city;
        $this->ReturnAddress->District = $epacket_config->returntoaddress_district;
        $this->ReturnAddress->Street = $epacket_config->returntoaddress_street;
        $this->load->helper('url');
        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }

    public function batch_add_order()
    {
        $this->load->library('script');
        $this->order_model->enable_get_track_number();
        // 5 minutes ago.
        $orders = $this->epacket_model->fetch_unconfirmed_list_by_time(5);
        $counter = 0;//var_dump($orders);
        foreach ($orders as $order)
        {
            $shipped = $this->order_model->check_order_shipped_or_not($order->id);
            if ($shipped)
            {
                continue;
            }
            $this->script->fetch_epacket_track_number(array('order_id' => $order->id));

            if ($counter++ > 10)
            {
                break;
            }
        }
        $this->order_model->reset_get_track_number();
        $orders = $this->epacket_model->fetch_print_no_confirmed(5);
        $counter = 0;
        foreach ($orders as $order)
        {
            if ($this->config->item('production'))
            {
                $secret = $this->config->item('secret');
                $ebay_id = strtolower($order->ebay_id);
                $this->token = $secret[$ebay_id];
            }
            else
            {
                $this->token = $this->config->item('token');
            }
            $track_number = $order->track_number;
            $this->_process_confirm_package($track_number);
            if ($counter++ > 10)
            {
                break;
            }
        }
    }

    public function add_order($piece)
    {
        $orders = $this->epacket_model->fetch_unconfirmed_list($piece);
        foreach ($orders as $order)
        {
            /*
            if ($this->Erp_model->is_get_track_number_stop()) {
                break;
            }
             *
             */
            $this->_process_add_order($order);
        }
    }

    public function auto_add_order($order_id)
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
        if ($this->config->item('production')) {
            $secret = $this->config->item('secret');
            $ebay_id = strtolower($data->ebay_id);
			//$ebay_id = strtolower('mallerp');
            if(is_int(strpos($ebay_id,'...'))){
                $sub_ebay_id = rtrim($ebay_id, '...');
                foreach ($secret as $key => $value) {
                    if(is_int(strpos($key,$sub_ebay_id))){
                        $ebay_id = $key;
                    }
                }
            }
            $this->token = $secret[$ebay_id];
        } else {
            $this->token = $this->config->item('token');
			//var_dump($this->token);die();
        }
        $transaction_id = $data->transaction_id;
        $order_id = $data->id;
        $track_code = $this->epacket_model->get_track_number($transaction_id,$order_id);
        if ($track_code)
        {
            return $this->_process_print_label($track_code, $transaction_id, $order_id);
        }
        $ItemList = array();
        $skus = explode(',', trim($data->sku_str, ','));
        $item_ids = explode(',', trim($data->item_id_str, ','));
        $item_titles = explode(ITEM_TITLE_SEP, trim($data->item_title_str, ','));
        //$item_ids = array_unique($item_ids);
        $qties = explode(',', trim($data->qty_str, ','));
        //$data->address_line_1 = preg_replace('/\d+/', '', $data->address_line_1, 1);
		//$this->stock_code=$data->stock_code;
        $item_count = count($item_ids);
        $i = 0;
        /*$price = ( ! empty($data->gross)) ? ($data->gross / 2) : ($data->net / 2);
        $price = $price / $item_count;*/
		$price=5;
        $weight = ($data->ship_weight / 1000) / $item_count;
		if(empty($weight)){$weight=0.05;}
		if($weight<0.05){$weight=0.05;}
		echo "weight:".$weight."\n";
		$ebay_order_ids=array();
        foreach ($item_ids as $item_id)
        {
            $item_title = $item_titles[$i];
            $sku = $skus[$i];
            $qty = $qties[$i];
            $product = $this->epacket_model->get_product_info_for_epacket($sku);
            // SKU
            $sku_obj = new sku();
            $sku_obj->CustomsTitleCN = $sku."  ".$product->name_cn;
            //$sku_obj->CustomsTitleEN = $product->name_en;
	    	$sku_obj->CustomsTitleEN = 'Accessories ';
            $sku_obj->DeclaredValue = $price;
            $sku_obj->OriginCountryName = 'China';
            $sku_obj->OriginCountryCode = "CN";
            $sku_obj->SKUID = $sku;
            $sku_obj->Weight = $weight;
            // Item(s)
            $item_obj = new item();
            $item_obj->EBayBuyerID = $data->buyer_id;
            $item_obj->EBayItemID = $item_id;
			/*修改获取ebay交易id的方法*/
			$ebay_order_infos=$this->epacket_model->get_ebay_transaction_id($item_id, $data->transaction_id, $item_title);
			foreach($ebay_order_infos as $ebay_order_info){
				if(in_array($ebay_order_info->id,$ebay_order_ids)){//如果ebayorder的id已经被用过一次就不要用了。
				}else
				{
					$ebay_transaction_id = $ebay_order_info->transaction_id;
					$ebay_order_ids[]=$ebay_order_info->id;
					break;//跳出本次foreach循环
				}
			}
            //$ebay_transaction_id = $this->epacket_model->get_ebay_transaction_id($item_id, $data->transaction_id, $item_title);
			//echo 'ebay transaction id: ' . $ebay_transaction_id . "\n";
            $item_obj->EBayTransactionID = $ebay_transaction_id;
            $item_obj->EBaySiteID = 0;
            $item_obj->OrderSalesRecordNumber = 0;
            $item_obj->PaymentDate = $data->list_date;
            $item_obj->PostedQTY = $qty;
            $item_obj->ReceivedAmount = $price;
            $item_obj->SalesRecordNumber = 0;
            $item_obj->SKU = $sku_obj;
            $item_obj->SoldDate = $data->list_date;
            $item_obj->SoldPrice = $price;
            $item_obj->SoldQTY = $qty;
            $ItemList[] = $item_obj;
            $i++;
        }

		//$user_id = get_current_user_id();
		$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($this->is_register);
        // ship to address
        $this->load->library('epacket/ShipToAddress');
        $this->shipToAddress->City = $data->town_city;
        //$this->shipToAddress->Company = 'company test';
        $this->shipToAddress->Contact = $data->name;
        $this->shipToAddress->Country = $data->country;
        //$this->shipToAddress->CountryCode = 'US';
		$this->shipToAddress->CountryCode = get_country_code($data->country);
        //$this->shipToAddress->District = 'tesasdt';
        $this->shipToAddress->Email = $data->from_email;
        $this->shipToAddress->Phone = $data->contact_phone_number ? $data->contact_phone_number : ' ';
        $this->shipToAddress->Postcode = $data->zip_code ? $data->zip_code : ' ';
        $this->shipToAddress->Province = $data->state_province ? $data->state_province : ' ';
        $street = '';
        $street = empty($street) ? $data->address_line_1 : $street . ', ' . $data->address_line_1;
		if(!empty($data->address_line_2))
		{
			$street=$street.','.$data->address_line_2;
		}
        $this->shipToAddress->Street = $street;
        // order detail
        $this->load->library('epacket/OrderDetail');
        $this->orderDetail->PickUpAddress = $this->pickUpAddress;
        $this->orderDetail->ShipFromAddress = $this->shipFromAddress;
		//$this->orderDetail->ReturnAddress = $this->ReturnAddress;
		$this->orderDetail->ReturnAddress = $this->ReturnAddress;
        $this->orderDetail->ShipToAddress = $this->shipToAddress;
        $this->orderDetail->EMSPickUpType = 0;
        $this->orderDetail->ItemList = $ItemList;
        // add order
        $this->load->library('epacket/AddOrder');
        $this->addOrder->APIDevUserID = $this->token['dev_id'];
        $this->addOrder->APISellerUserID = $this->token['user_id'];
        $this->addOrder->APIPassword = $this->token['api_key'];
        $this->addOrder->MessageID = $this->config->item('message_id');
        $this->addOrder->Version = $this->config->item('version');
        $this->addOrder->OrderDetail = $this->orderDetail;
        $track_code = $this->_call_add_order($this->addOrder, $transaction_id);
        if ($track_code)
        {
            $this->epacket_model->save_track_number($transaction_id, $track_code,$order_id);
            $this->_process_print_label($track_code, $transaction_id, $order_id);
            echo 'done!';
        }
        else
        {
            echo 'not done!';
        }
    }
    private function _call_add_order($add_order, $transaction_id)
    {
        echo "start call API\n";
        try
        {
            $wsdl_url = $this->config->item('wsdl_url');
            $client = new SoapClient($wsdl_url);
			echo "AddAPACShippingPackageRequest:";var_dump($add_order);
            $response = $client->AddAPACShippingPackage(array('AddAPACShippingPackageRequest' => $add_order));
            $result = $response->AddAPACShippingPackageResult;
            var_dump($result);
            if ($result->Ack == 'Success')
            {
                return $result->TrackCode;
            }
            else
            {
                if (isset($result->Message))
                {
                    $this->epacket_model->save_failure_message($transaction_id, $result->Message);
                }
				var_dump($add_order);
				var_dump($result);
                return false;
            }
        }
        catch (SOAPFault $exception)
        {
            ob_start();
            print($exception);
            $error_message = ob_get_contents();
            ob_end_clean();
            $this->epacket_model->save_failure_message($transaction_id, $error_message);
        }
    }

    private function _process_print_label($track_code, $transaction_id, $order_id)
    {
		//$this->print_sku_list($track_code,$order_id);//转移到eub标签下载成功后再生成标签
        if ($this->_check_label_exists($transaction_id))
        {
            return true;
        }
		//$user_id = get_current_user_id();
		$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($this->is_register);
        $this->load->library('epacket/TrackDetail');
        $this->trackDetail->PageSize = (int)$epacket_config->pagesize;
        $this->trackDetail->TrackCode = $track_code;
        $this->load->library('epacket/PrintLabel');
        $this->printLabel->APIDevUserID = $this->token['dev_id'];
        $this->printLabel->APISellerUserID = $this->token['user_id'];
        $this->printLabel->APIPassword = $this->token['api_key'];
        $this->printLabel->MessageID = $this->config->item('message_id');
        $this->printLabel->Version = $this->config->item('version');
        //$this->printLabel->TrackDetail = $this->trackDetail;
        $this->printLabel->PageSize = (int)$epacket_config->pagesize;
        $this->printLabel->TrackCode = $track_code;
        $this->_call_print_label($this->printLabel, $transaction_id, $order_id, $track_code);
		
    }
    private function _check_label_exists($transaction_id)
    {
        $status = $this->epacket_model->get_print_label_status($transaction_id);
        $pdf_file = $this->_pdf_path() . '/' . $transaction_id . '.pdf';
        if ($status && file_exists($pdf_file))
        {
            return true;
        }
        return false;
    }

    private function _call_print_label($print_label, $transaction_id, $order_id, $track_code)
    {
        try
        {
            $wsdl_url = $this->config->item('wsdl_url');
            $client = new SoapClient($wsdl_url);
			echo "GetAPACShippingLabelRequest:";var_dump($print_label);
            $response = $client->GetAPACShippingLabel(array('GetAPACShippingLabelRequest' => $print_label));
            $result = $response->GetAPACShippingLabelResult;
            if ($result->Ack == 'Success')
            {
                $label = $result->Label;
                $pdf_path = $this->_pdf_path();
                file_put_contents($pdf_path . '/' . $transaction_id . '.pdf', $label);
                $this->epacket_model->update_print_label($transaction_id);
                // everything is ok now! update the order status
                $order = $this->order_model->get_order($order_id);
                if (isset($order->ship_confirm_user))
                {
                    $user_name = $order->ship_confirm_user;
                }
                else
                {
                    $user_name = 'script';
                }
				$this->print_sku_list($track_code,$order_id);//生成发货清单pdf
                unset($order);
                $wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
                $remark = $this->order_model->get_sys_remark($order_id);
                $remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);
                $data = array(
                            'track_number' => $track_code,
                            'ship_confirm_date' => date('Y-m-d H:i:s'),
                            'order_status' => $wait_for_feedback_status,
                            'sys_remark' => $remark . ' epacket: order status id is ' . $wait_for_feedback_status,
                        );
                $this->order_model->update_order_information($order_id, $data);
                echo "starting confirm packet\n";
                $this->_process_confirm_package($track_code);
                /*
                  $type_extra = $user_name . '/' . date('Y-m-d H:i:s');
                  $this->product_model->update_product_stock_count_by_order_id($order_id, 'order_outstock', $type_extra);
                 */

                // notify customer with email in another process
                $this->events->trigger(
                    'shipping_confirmation_after', array(
                        'order_id' => $order_id,
                    ));
            }
            else
            {
                var_dump($result);
                $this->epacket_model->save_failure_message($transaction_id, $result->Message);

                return false;
            }
        }
        catch (SOAPFault $exception)
        {
            print $exception;
        }
    }

    private function _pdf_path()
    {
        $pdf_path = $this->config->item('pdf_path');
        $sub_path = date('Y-m-d');
        $full_path = $pdf_path . $sub_path;
        if (!file_exists($full_path))
        {
            mkdir($full_path);
        }
        return $full_path;
    }
	
	public function print_sku_list($track_code,$order_id)
	{
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
			$pdf_path = $this->_pdf_path();
			$filename = $pdf_path . "/sku_list_" . $track_code . ".pdf";
			$this->pdf->Output($filename, 'F');
		}else{
			return;
		}
	}

    private function _process_confirm_package($track_code)
    {
        $this->load->library('epacket/Order');
        $this->Order->TrackCode = $track_code;
        $this->load->library('epacket/ConfirmPackage');
        $this->confirmPackage->APIDevUserID = $this->token['dev_id'];
        $this->confirmPackage->APISellerUserID = $this->token['user_id'];
        $this->confirmPackage->APIPassword = $this->token['api_key'];
        $this->confirmPackage->MessageID = $this->config->item('message_id');
        $this->confirmPackage->Version = $this->config->item('version');
        $this->confirmPackage->Order = $this->Order;
        $this->confirmPackage->TrackCode = $track_code;
        try
        {
            $wsdl_url = $this->config->item('wsdl_url');
            $client = new SoapClient($wsdl_url);
            $response = $client->ConfirmAPACShippingPackage (array('ConfirmAPACShippingPackageRequest' => $this->confirmPackage));
            $result = $response->ConfirmAPACShippingPackageResult;
            $this->epacket_model->confirm_package($track_code);
            var_dump($result);
        }
        catch (SOAPFault $exception)
        {
            print $exception;
        }
    }
	
	public function auto_make_epacket_shipped() {
		//echo "*************";die();
        $orders=$this->order_model->fetch_all_epacket_wait_for_shipping_label_order_ids();
		if($orders)
		{
			foreach($orders as $order)
			{
				echo $order->item_no."\n";
				if ($order->order_status != 0 && $order->order_status != $this->order_statuses['wait_for_shipping_label']) {
					$order_status = $order->item_no .lang('order_status_is') . lang(fetch_status_name('order_status', $order->order_status));
					echo $order_status."\n";
					continue;
				}
				$order_id = $order->id;
				$is_register = strtoupper($order->is_register);
				/*
				* Epacket:
				*/
				if (strtoupper($is_register) == 'H') {
					$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($is_register);
					/* check if there is any available ebay transaction id, or return false */
            		$paypal_transaction_id = $order->transaction_id;
            		$item_ids = explode(',', trim($order->item_id_str, ','));
            		$item_ids = array_unique($item_ids);
            		foreach ($item_ids as $item_id)
            		{
                		if ( ! $this->epacket_model->ebay_transaction_id_exists($item_id, $paypal_transaction_id)) 
                		{
                    		echo $order->item_no.lang('no_ebay_transaction_id_info')."\n";
                    		continue;
                		}
            		}

            		if ($order->ship_weight && $order->ship_confirm_user)
            		{
                		echo $order->item_no.lang('shipping_weight_exists_no_need_try_again')."\n";
						continue;
            		}
					$remark = $order->sys_remark;
					$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), lang('program'));

            		$data = array(
                		'descript' => lang('program').'auto shipped!',
                		'ship_confirm_user' => lang('program'),
						'order_status' => $this->order_statuses['wait_for_feedback'],
						'ship_confirm_date' => date('Y-m-d H:i:s'),
						'sys_remark' => $remark,
						'ship_weight' => 0.05,
            		);
            		$this->order_model->update_order_information($order_id, $data);
					$this->product_model->update_product_stock_count_by_order_id($order_id);

            		$data = array(
                		'order_id' => $order_id,
                		'transaction_id' => $paypal_transaction_id,
                		'input_user' => $epacket_config->user_id,
            		);
            		$this->epacket_model->save_epacket_confirm_list($data);
					//$this->ebay_order_model->save_wait_complete_sale($order_id);
            		$this->script->fetch_epacket_track_number(array('order_id' => $order_id));
					echo $order->item_no." has shiped success!\n";
        		}elseif(strtoupper($is_register) == 'EUB'){
					/*线下eub*/
					$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($is_register);
					$remark = $order->sys_remark;
					$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), lang('program'));
					$data = array(
                		'descript' => lang('program').'auto shipped!',
                		'ship_confirm_user' => lang('program'),
						'print_label_user' => lang('program'),
						'order_status' => $this->order_statuses['wait_for_feedback'],
						'ship_confirm_date' => date('Y-m-d H:i:s'),
						'print_label_date' => date('Y-m-d H:i:s'),
						'sys_remark' => $remark,
						'ship_weight' => 0.05,
            		);
            		$this->order_model->update_order_information($order_id, $data);
					$this->product_model->update_product_stock_count_by_order_id($order_id);
					$this->ebay_order_model->save_wait_complete_sale($order_id);
            		$data = array(
                		'order_id' => $order_id,
                		'input_user' => $epacket_config->user_id,
            		);
			
            		$this->epacket_model->save_specification_epacket_confirm_list($data);
            		$this->script->fetch_specification_epacket_track_number(array('order_id' => $order_id));
					echo $order->item_no." has shiped success!\n";
				}/******************************************************************/
			}
		}
    }
	
	public function auto_get_epacket_track_number() {
		$wait_for_feedback_id = fetch_status_id('order_status', 'wait_for_feedback');
		$sql = <<<SQL
SELECT * from order_list
WHERE
   track_number='' AND
   is_register='H' AND
   order_status=$wait_for_feedback_id
SQL;
        $query = $this->db->query($sql);
        $orders=$query->result();
		foreach($orders as $order)
		{
			echo $order->id."\n";
			$this->script->fetch_epacket_track_number(array('order_id' => $order->id));
		}
	}
	public function track_numberdownload_part_pdf()
	{
		if (!$this->input->is_post()) {
            return;
        }
		$track_number = $this->input->post('track_number');
		//echo $track_number;
		if(substr($track_number,0,2)=='LN')
		{
			$pdf_folder = "/var/www/html/mallerp/static/ems/";
			$confirmed_list = $this->epacket_model->get_specification_epacket_confirm_list_with_track_number($track_number);
		}else{
			$pdf_folder = "/var/www/html/mallerp/static/pdf/";
			$confirmed_list = $this->epacket_model->get_epacket_confirm_list_with_track_number($track_number);
		}
		if (empty($confirmed_list)) {
            echo 'No order is ' . $track_number;
            return;
        }
		$date = substr($confirmed_list->input_date,0,10);
		$pdf_folder.=$date.'/';
		if(substr($track_number,0,2)=='LN')
		{
			//$track_number = $order->track_number;
            $pdf_url = $pdf_folder . $track_number . '.pdf';
			$sku_pdf_url = $pdf_folder . 'sku_list_'.$track_number . '.pdf';
		}else{
			$transaction_id = $order->transaction_id;
			//$track_number = $order->track_number;
            $pdf_url = $pdf_folder . $transaction_id . '.pdf';
			$sku_pdf_url = $pdf_folder . 'sku_list_'.$track_number . '.pdf';
		}
		if (!file_exists($pdf_folder)) {
            echo '1.No pdf for ' . $track_number;
            return;
        }
		if ($confirmed_list->print_label==0) {
            echo '2.pdf not download for ' . $date;
            return;
        }
		//echo $pdf_folder."<pre>";var_dump($confirmed_list);echo "</pre>";
		
		require_once APPPATH . 'libraries/pdf/PDFMerger.php';

        $pdf = new PDFMerger;
			if ( ! file_exists($pdf_url))
            {
				echo '3.No pdf for ' . $track_number;
                return;
            }
			if ($confirmed_list->downloaded==0) {
				$data = array(
                	'downloaded' => 1,
            	);
				if(substr($track_number,0,2)=='LN')
				{
					$this->epacket_model->update_ems_confirmed_list($confirmed_list->id, $data);
				}else{
					$this->epacket_model->update_confirmed_list($confirmed_list->id, $data);
				}
			}
            $pdf->addPDF($pdf_url, 'all');
			if ( ! file_exists($sku_pdf_url))
            {
                continue;
            }
			$pdf->addPDF($sku_pdf_url, 'all');
			$pdf->merge('download', "$track_number.pdf");
		
	}

}

?>
