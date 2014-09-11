<?php

require_once APPPATH . 'controllers/mallerp_no_key' . EXT;

class sku {

    public $CustomsTitleCN;
    public $CustomsTitleEN;
    public $DeclaredValue;
    public $OriginCountryName;
    public $OriginCountryCode;
    public $SKUID;
    public $Weight;

}

class item {

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

class Epacket extends Mallerp_no_key {

    private $user;
    private $token;
    protected $order_statuses = array();

    public function __construct() {
        parent::__construct();

        $this->user = '李漂洋';
        $this->config->load('config_epacket');
        $this->load->model('epacket_model');
        $this->load->model('order_model');

        // pick up address
        $this->load->library('epacket/PickUpAddress');
        $this->pickUpAddress->Company = '通拓集团有限公司';
        $this->pickUpAddress->Contact = '李漂洋';
        $this->pickUpAddress->Email = 'shipping01@tomtop.net';
        $this->pickUpAddress->Mobile = '13689519161';
        $this->pickUpAddress->Phone = '0755 83998006 8020';
        $this->pickUpAddress->Postcode = 518129;
        $this->pickUpAddress->Country = '中国';
        $this->pickUpAddress->Province = 440000;
        $this->pickUpAddress->City = 440300;
        $this->pickUpAddress->District = 440307;
        $this->pickUpAddress->Street = '五和大道和磡工业区A3栋四楼';

        // ship from address
        $this->load->library('epacket/ShipFromAddress');
        $this->shipFromAddress->Company = 'MALLERP Inc';
        $this->shipFromAddress->Contact = 'MALLERP.COM';
        $this->shipFromAddress->Email = 'shipping01@tomtop.net';
        $this->shipFromAddress->Mobile = '+86-755-83998006-8015';
        $this->shipFromAddress->Phone = '0755 83998006 8020';
        $this->shipFromAddress->Postcode = 518129;
        $this->shipFromAddress->Country = 'China';
        $this->shipFromAddress->Province = 'Guangdong';
        $this->shipFromAddress->City = 'Shenzhen';
        $this->shipFromAddress->District = 'Longgang';
        $this->shipFromAddress->Street = '4/F, No. A3 building HeKan Industrial Park, Bantian';

        $this->load->helper('url');

        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o) {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }

    public function batch_add_order() {
        $this->order_model->enable_get_track_number();
        system("bash /var/www/html/mallerp/scripts/epacket/batch_add_order.sh");
        $this->order_model->reset_get_track_number();
    }

    public function auto_add_order($order_id) {
        $order = $this->order_model->get_order($order_id);
        if (!isset($order->id)) {
            return false;
        }
        $this->_process_add_order($order);
    }

    private function _process_add_order($data) {
        if ($this->config->item('production')) {
            $to_email = strtolower($data->to_email);
            $tokens = $this->config->item('token');
            $this->token = $tokens[$to_email];
        } else {
            $this->token = $this->config->item('token');
        }

        $transaction_id = $data->transaction_id;
        $order_id = $data->id;
        $track_code = $this->epacket_model->get_track_number($transaction_id);

        if ($track_code) {
            return $this->_process_print_label($track_code, $transaction_id, $order_id);
        }

        $ItemList = array();
        $product_codes = explode(',', $data->sku_str);
        $item_ids = explode(',', $data->item_id_str);
        $amounts = explode(',', $data->qty_str);
        $product = $this->epacket_model->get_product_info_for_epacket($product_codes[0]);
        // SKU

        $price = !empty($data->gross) ? $data->gross / 2 : $data->net / 2;
        $this->sku = new sku();
        $this->sku->CustomsTitleCN = ' ';
        $this->sku->CustomsTitleEN = $product->name_en;
        $this->sku->DeclaredValue = $price;
        $this->sku->OriginCountryName = 'China';
        $this->sku->OriginCountryCode = "CN";
        $this->sku->SKUID = $product_codes[0];
        $this->sku->Weight = $data->ship_weight / 1000;


        // Item(s)
        $this->item = new item();
        //$this->item->CurrencyCode = 'USD';
        $this->item->EBayBuyerID = $data->buyer_id;
        //$this->item->EBayEmail = 'txtE@sadf.com';
        $this->item->EBayItemID = $item_ids[0];

        $ebay_transaction_id = $this->epacket_model->get_ebay_transaction_id($data->transaction_id);
        if ($ebay_transaction_id === false) {
            $ebay_transaction_id = $data->transaction_id;
        }

        $this->item->EBayTransactionID = $ebay_transaction_id;
        //$this->item->EBayItemTitle = 'txtEbayItemTitle->Text->Trim()';
        //$this->item->EBayMessage = 'txtEbayMsg->Text->Trim()';
        $this->item->EBaySiteID = 0;
        //$this->item->Note = 'txtNote->Text->Trim()';
        $this->item->OrderSalesRecordNumber = 0;
        $this->item->PaymentDate = $data->list_date;
        //$this->item->PayPalEmail = 'txtPayPalEmail@tomt.com';
        //$this->item->PayPalMessage = 'yPalMessage->Text->Trim()';
        $this->item->PostedQTY = $amounts[0];
        $this->item->ReceivedAmount = $data->net / 2;
        $this->item->SalesRecordNumber = 0;
        $this->item->SKU = $this->sku;
        $this->item->SoldDate = $data->list_date;
        $this->item->SoldPrice = $price;
        $this->item->SoldQTY = $amounts[0];

        $ItemList[] = $this->item;
        // ship to address
        $this->load->library('epacket/ShipToAddress');
        $this->shipToAddress->City = $data->town_city;
        //$this->shipToAddress->Company = 'company test';
        $this->shipToAddress->Contact = $data->name;
        $this->shipToAddress->Country = $data->country;
        $this->shipToAddress->CountryCode = 'US';
        //$this->shipToAddress->District = 'tesasdt';
        $this->shipToAddress->Email = $data->from_email;
        $this->shipToAddress->Phone = $data->contact_phone_number ? $data->contact_phone_number : ' ';
        $this->shipToAddress->Postcode = $data->zip_code ? $data->zip_code : ' ';
        $this->shipToAddress->Province = $data->state_province ? $data->state_province : ' ';
        $street = $data->address_line_2;
        $street = empty($street) ? $data->address_line_1 : $street . ', ' . $data->address_line_1;
        $this->shipToAddress->Street = $street;

        // order detail
        $this->load->library('epacket/OrderDetail');
        $this->orderDetail->PickUpAddress = $this->pickUpAddress;
        $this->orderDetail->ShipFromAddress = $this->shipFromAddress;
        $this->orderDetail->ShipToAddress = $this->shipToAddress;
        $this->orderDetail->PickUpType = 1;
        $this->orderDetail->ItemList = $ItemList;

        // add order
        $this->load->library('epacket/AddOrder');
        $this->addOrder->AppKey = $this->config->item('app_key');
        $this->addOrder->Token = $this->token;
        $this->addOrder->MessageID = $this->config->item('message_id');
        $this->addOrder->Version = $this->config->item('version');
        $this->addOrder->OrderDetail = $this->orderDetail;

        $track_code = $this->_call_add_order($this->addOrder, $transaction_id);
        if ($track_code) {
            $this->epacket_model->save_track_number($transaction_id, $track_code);
            $this->_process_print_label($track_code, $transaction_id, $order_id);
            echo 'done!';
        } else {
            echo 'not done!';
        }
    }

    private function _call_add_order($add_order, $transaction_id) {
        try {
            $wsdl_url = $this->config->item('wsdl_url');
            $client = new SoapClient($wsdl_url);

            $response = $client->AddOrder(array('AddOrderRequest' => $add_order));

            $result = $response->AddOrderResult;


            if ($result->Ack == 'Success') {
                return $result->TrackCode;
            } else {
                if (isset($result->Message)) {
                    $this->epacket_model->save_failure_message($transaction_id, $result->Message);
                }

                return false;
            }
        } catch (SOAPFault $exception) {
            ob_start();
            print($exception);
            $error_message = ob_get_contents();
            ob_end_clean();
            $this->epacket_model->save_failure_message($transaction_id, $error_message);
        }
    }

    private function _process_print_label($track_code, $transaction_id, $order_id) {
        if ($this->_check_label_exists($transaction_id)) {
            return true;
        }
        $this->load->library('epacket/PrintLabel');
        $this->printLabel->AppKey = $this->config->item('app_key');
        $this->printLabel->Token = $this->token;
        $this->printLabel->MessageID = $this->config->item('message_id');
        $this->printLabel->Version = $this->config->item('version');
        $this->printLabel->TrackCode = $track_code;
        $this->printLabel->PageSize = 1;

        $this->_call_print_label($this->printLabel, $transaction_id, $order_id, $track_code);
    }

    private function _check_label_exists($transaction_id) {
        $status = $this->epacket_model->get_print_label_status($transaction_id);

        $pdf_file = $this->_pdf_path() . '/' . $transaction_id . '.pdf';

        if ($status && file_exists($pdf_file)) {
            return true;
        }

        return false;
    }

    private function _call_print_label($print_label, $transaction_id, $order_id, $track_code) {
        try {
            $wsdl_url = $this->config->item('wsdl_url');
            $client = new SoapClient($wsdl_url);
            $response = $client->PrintEPacketLabel(array('PrintLabelRequest' => $print_label));

            $result = $response->PrintEPacketLabelResult;
            if ($result->Ack == 'Success') {
                $label = $result->Label;
                $pdf_path = $this->_pdf_path();
                file_put_contents($pdf_path . '/' . $transaction_id . '.pdf', $label);
                $this->epacket_model->update_print_label($transaction_id);

                // everything is ok now! update the order status
                $order = $this->order_model->get_order($order_id);
                if (isset($order->ship_confirm_user)) {
                    $user_name = $order->ship_confirm_user;
                } else {
                    $user_name = 'script';
                }
                unset($order);

                $remark = $this->order_model->get_sys_remark($order_id);
                $remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);
                $data = array(
                    'track_number' => $track_code,
                    'ship_confirm_date' => date('Y-m-d H:i:s'),
                    'order_status' => $this->order_statuses['wait_for_feedback'],
                    'sys_remark' => $remark,
                );
                $this->order_model->update_order_information($order_id, $data);

                /*
                  $type_extra = $user_name . '/' . date('Y-m-d H:i:s');
                  $this->product_model->update_product_stock_count_by_order_id($order_id, 'order_outstock', $type_extra);
                 */

                // notify customer with email in another process
                $this->events->trigger(
                        'shipping_confirmation_after', array(
                    'order_id' => $order_id,
                        )
                );
            } else {
                var_dump($result);
                $this->epacket_model->save_failure_message($transaction_id, $result->Message);

                return false;
            }
        } catch (SOAPFault $exception) {
            print $exception;
        }
    }

    private function _pdf_path() {
        $pdf_path = $this->config->item('pdf_path');
        $sub_path = date('Y-m-d');
        $full_path = $pdf_path . $sub_path;
        if (!file_exists($full_path)) {
            mkdir($full_path);
        }

        return $full_path;
    }

}

?>
