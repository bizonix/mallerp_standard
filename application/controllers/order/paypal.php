<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Paypal extends Mallerp_no_key
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

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('mallerp', DEFAULT_LANGUAGE);

        $this->load->model('paypal_model');
        $this->load->model('order_model');
        $this->load->model('order_role_model');
        $this->load->model('product_model');
        $this->load->model('ebay_model');
        $this->load->model('fee_price_model');
        $this->load->model('shipping_company_model');
        $this->load->model('shipping_function_model');
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_subarea_group_model');
        $this->load->model('shipping_type_model');
        $this->load->model('shipping_code_model');
        $this->load->helper('paypal');
        $this->load->helper('order');
        $this->load->helper('shipping');
        $this->load->helper('chukouyi');



        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }

        if (!session_id()) {
            session_start();
        }

        $this->report = array(
                            'wait_for_handle'       => 0,
                            'auto_comfirmed'        => 0,
                            'wait_for_comfirmed'    => 0,
                            'merged_order'          => 0,
                            'send_money'            => 0,
                            'ingored'               => 0,
                            'order_exists'          => 0,
                            'not_assigned'          => 0,
                            'uncleared'             => 0,
                            'incomplete'            => 0,
                            'unauthorized'          => 0
                        );
        set_time_limit(0);
    }

    public function auto_assign_orders()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_assign_order.php') === FALSE)
        {
            exit;
        }
        $orders = $this->paypal_model->fectch_all_unassigned_orders();

        $paypal_emails = array();

        $pattern = '/<span class="mbg-nw">(.*?)<\/span>/';
        foreach ($orders as $order)
        {
            $order_id = $order->id;
            $new_order = $this->order_model->get_order($order_id);
            $not_updated = TRUE; // mark if order is updated or not
            $remark = $new_order->sys_remark;
            $remark .= sprintf(lang('auto_assign_order_remark'), date('Y-m-d H:i:s'), lang('program'));


            if ($new_order->order_status != $this->order_statuses['wait_for_assignment'])
            {
                continue;
            }

            $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
            $item_ids = explode(',', $order->item_id_str);
            if ( ! isset($item_ids[0]))
            {
                continue;
            }
            $item_id = $item_ids[0];
            $ebay_url .= $item_id;
            $ebay_html = @ file_get_contents($ebay_url);
            if ($ebay_html !== FALSE)
            {
                preg_match($pattern, $ebay_html, $matches);
                if (isset($matches[1]))
                {
                    $ebay_id = $matches[1];

                    if (isset($paypal_emails[$ebay_id]))
                    {
                        $paypal_email = $paypal_emails[$ebay_id];
                    }
                    else
                    {
                        $paypal_email = $this->paypal_model->fetch_paypal_email_by_ebay_id($ebay_id);
                        $paypal_emails[$ebay_id] = $paypal_email;
                    }

                    if ( ! empty($paypal_email))
                    {

                        echo $order_id . "\n";
                        $user_obj = $this->paypal_model->fetch_user_by_paypal_email($paypal_email);

                        $item_no = FALSE;
                        if ( ! empty($user_obj))
                        {
                            $item = $this->order_model->get_order_item($order_id);
                            if (empty($item))
                            {
                                continue;
                            }
                            $item_no = $item->item_no;

                            $accounts = array('005', '012','017','010');
                            foreach ($accounts as $account)
                            {
                                if (strpos($item_no, $account))
                                {
                                    $item_ids = explode(',', $order->item_id_str);
                                    $item_id = isset($item_ids[0]) ? $item_ids[0] : '';
                                    $item_no = $this->order_model->create_item_no($user_obj->login_name, date("ymd"), substr($item_id, -5), $order->transaction_id, $order->is_register);
                                    break;
                                }
                            }
                        }
                        $data = array(
                                    'to_email'      => $paypal_email,
                                    'order_status'  => $this->order_statuses['not_handled'],
                                    'sys_remark'    => $remark,
                                );
                        if ($item_no)
                        {
                            $data['item_no'] = $item_no;
                        }
                        if ( ! empty($user_obj->login_name))
                        {
                            $data['input_user'] = $user_obj->login_name;
                        }
                        var_dump($data);
                        $not_updated = FALSE;
                        $this->order_model->update_order_information($order_id, $data);
                    }
                }
            }
            // not updated? just assign it to uk or ac.
            if ($not_updated)
            {
                $secends = time() - strtotime($new_order->input_date);
                if ($secends > 60 * 60 * 24)
                {
                    echo "Not updated\n";
                    $data = array(
                                'order_status'  => $this->order_statuses['not_handled'],
                                'sys_remark'    => $remark,
                            );
                    $this->order_model->update_order_information($order_id, $data);
                }
            }
        }
    }

    public function import_ack_failed_orders()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'import_ack_failed_order.php') === FALSE)
        {
            exit;
        }
        $all_orders = $this->paypal_model->fectch_all_ack_failed_orders();

        foreach ($all_orders as $order) {
            $this->set_paypal_account($order->input_user);

            $transaction_id = $order->transaction_id;
            $transaction_details = $this->get_transaction_details($transaction_id);

            if ($transaction_details) {
                $this->_save_transaction_details($transaction_details);
            }

            if ($this->paypal_model->is_transaction_exists($transaction_id)
                || $this->paypal_model->is_transaction_merged($transaction_id)) {
                $this->paypal_model->remove_ack_failed_order($transaction_id);
            }
        }
        $this->log_report('ack failed');
        echo 'Done!';
    }

    public function import_unauthorized_orders()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'import_authorized_order.php') === FALSE)
        {
            exit;
        }
        $all_orders = $this->paypal_model->fectch_all_unauthorized_orders();
        foreach ($all_orders as $order) {
            $this->set_paypal_account($order->input_user);

            $transaction_id = $order->transaction_id;
            $transaction_details = $this->get_transaction_details($transaction_id);

            if ($transaction_details) {
                $this->_save_transaction_details($transaction_details);
            }

            if ($this->paypal_model->is_transaction_exists($transaction_id)
                    || $this->paypal_model->is_transaction_merged($transaction_id))
            {
                $this->paypal_model->remove_unauthorized_order($transaction_id);
            }
        }
        echo 'Done!';
    }
	
	public function auto_import_refund($user_name)
	{
		if(strpos($_SERVER['SCRIPT_FILENAME'], 'import_refund.php') === FALSE)
        {
            //exit;
        }
		$all_transactions = array();
        $this->user = $user_name;
        $this->uid = $this->order_model->get_user_id_by_name($user_name);
        $this->paypal = $this->paypal_model->get_paypal_account($user_name);

        $this->start_time = $this->paypal_model->get_paypal_import_refund_beginning_time($this->user);
        $this->end_time = get_current_utc_time(); 
        echo "End time: ", $this->end_time, "\n";

        $start_time = $this->start_time;
        $end_time = $this->end_time;

        $save_end_time = $end_time;
        while ($transactions = $this->process_transaction_refund_search($start_time, $end_time))
        {
            $transaction_count = count($transactions);
            if ($transaction_count > 0)
            {
                $end_time = $transactions[$transaction_count - 1]['timeStamp'];
                $all_transactions = array_merge($all_transactions, $transactions);
                if (strcmp($end_time, $start_time) <= 0 || strcasecmp($save_end_time, $end_time) == 0)
                {
                    break;
                }
                $save_end_time = $end_time;
            }
            else
            {
                break;
            }
        }

        $all_transactions = array_reverse($all_transactions);
        $this->_proccess_import_refund_transactions($all_transactions);

        $this->paypal_model->update_paypal_import_refund_beginning_time(array('value' => $this->end_time), $this->user);
        $this->log_report();
        echo 'Done!';
	}
	private function _proccess_import_refund_transactions($all_transactions)
    {
        $total_count = count($all_transactions);
        foreach ($all_transactions as $transaction)
        {

            $transaction_id = $transaction['transactionID'];
            /*
             * 订单已存在，不读取订单信息
             */
            /*
             * ********************在导入订单时必须重新判断*****************************
             */
            if ($this->paypal_model->is_transaction_refund_exists($transaction_id))
            {
                $this->report['order_exists']++;

                continue;
            }
            $transaction_details = $this->get_transaction_details($transaction_id);

            if ($transaction_details)
            {
				$data=array(
					'transactionid'=>isset($transaction_details['TRANSACTIONID']) ? $transaction_details['TRANSACTIONID'] : '',
					'timestamp'=>isset($transaction_details['ORDERTIME']) ? $transaction_details['ORDERTIME'] : '',
					'payername'=>isset($transaction_details['SHIPTONAME']) ? $transaction_details['SHIPTONAME'] : '',
					'amount'=>isset($transaction_details['AMT']) ? $transaction_details['AMT'] : '',
					'status'=>isset($transaction_details['PAYMENTSTATUS']) ? $transaction_details['PAYMENTSTATUS'] : '',
					'note'=>isset($transaction_details['NOTE']) ? $transaction_details['NOTE'] : '',
					'input_user'=>$this->user,
					);
                $this->paypal_model->save_paypal_refund_list($data);
            }
        }
    }



	private function process_transaction_refund_search($start, $end)
    {
        /* Make the API call to PayPal, using API signature.
           The API response is stored in an associative array called $resArray */

        $nvpStr = $this->format_nvp_search_str($start, $end);
		$nvpStr = $nvpStr."&TRANSACTIONCLASS=Refund";

        $resArray = hash_call("TransactionSearch", $nvpStr);

        if (! $resArray)
        {
            var_dump($resArray);
            $this->_damage_error($start, $end);
            return false;
        }

        /* Next, collect the API request in the associative array $reqArray
           as well to display back to the browser.
           Normally you wouldnt not need to do this, but its shown for testing */

        $reqArray = $_SESSION['nvpReqArray'];

        /* Display the API response back to the browser.
           If the response from PayPal was a success, display the response parameters'
           If the response was an error, display the errors received using APIError.php.
           */
        $ack = strtoupper($resArray["ACK"]);

        if($ack!="SUCCESS" && $ack!="SUCCESSWITHWARNING")
        {
            var_dump($resArray);
            $this->_damage_error($start, $end);
            return false;

        }
        $resArray = NVPToArray($resArray);

        return $resArray['transactions'];
    }

    public function import_completed_orders()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'import_completed_order.php') === FALSE)
        {
            exit;
        }
        $all_orders = $this->paypal_model->fectch_all_uncompleted_orders();
        foreach ($all_orders as $order) {
            $this->set_paypal_account($order->input_user);

            $transaction_id = $order->transaction_id;
            $transaction_details = $this->get_transaction_details($transaction_id);

            if ($transaction_details) {
                $this->_save_transaction_details($transaction_details);
            }

            if ($this->paypal_model->is_transaction_exists($transaction_id)
                || $this->paypal_model->is_transaction_merged($transaction_id)) {
                $this->paypal_model->remove_completed_order($transaction_id);
            }
        }
        echo 'Done!';
    }

    public function transaction_details($transactionID)
    {
        $user_name = '100';
        $this->user = $user_name;
        $this->uid = $this->order_model->get_user_id_by_name($user_name);
        $this->paypal = $this->paypal_model->get_paypal_account($user_name);

        $resArray = $this->get_transaction_details($transactionID);

        var_dump($resArray);die('');
        $data['transaction_details'] = $resArray;

        $this->load->view('paypal/transaction_details', $data);
    }

    public function get_paypal_account()
    {
        return $this->paypal;
    }

    private function format_nvp_search_str($startDateStr, $endDateStr)
    {
        $API_UserName = $this->paypal->apiuser;
        $API_Password = $this->paypal->apipass;
        $API_Signature = $this->paypal->apisign;
        $API_Endpoint = API_ENDPOINT;
        $subject = SUBJECT;

        /* Construct the request string that will be sent to PayPal.
           The variable $nvpstr contains all the variables and is a
           name value pair string with & as a delimiter */
        $nvpStr;
        $transactionID = '';
        if (isset($_REQUEST['transactionID']))
        {
            $transactionID=urlencode();
        }
        $nvpStr="&STARTDATE=$startDateStr";
        $nvpStr.="&ENDDATE=$endDateStr";

        if($transactionID!='')
            $nvpStr=$nvpStr."&TRANSACTIONID=$transactionID";

        $getAuthModeFromConstantFile = true;
        //$getAuthModeFromConstantFile = false;
        $nvpHeader = "";

        if(!$getAuthModeFromConstantFile)
        {
            $AuthMode = "THIRDPARTY"; //Partner's API Credential and Merchant Email as Subject are required.
        }
        else
        {
            if(!empty($API_UserName) && !empty($API_Password) && !empty($API_Signature) && !empty($subject))
            {
                $AuthMode = "THIRDPARTY";
            }
            else if (!empty($API_UserName) && !empty($API_Password) && !empty($API_Signature))
            {
                $AuthMode = "3TOKEN";
            }
            else if (!empty($subject))
            {
                $AuthMode = "FIRSTPARTY";
            }
        }

        switch($AuthMode)
        {

        case "3TOKEN" :
            $nvpHeader = "&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature);
            break;
        case "FIRSTPARTY" :
            $nvpHeader = "&SUBJECT=".urlencode($subject);
            break;
        case "THIRDPARTY" :
            $nvpHeader = "&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature)."&SUBJECT=".urlencode($subject);
            break;

        }

        $nvpStr = $nvpHeader.$nvpStr;

        return $nvpStr;
    }

    private function format_nvp_details_str($transactionID)
    {
        $API_UserName = $this->paypal->apiuser;
        $API_Password = $this->paypal->apipass;
        $API_Signature = $this->paypal->apisign;
        $API_Endpoint = API_ENDPOINT;
        $subject = SUBJECT;

        $transactionID=urlencode($transactionID);

        /* Construct the request string that will be sent to PayPal.
           The variable $nvpstr contains all the variables and is a
           name value pair string with & as a delimiter */
        $nvpStr="&TRANSACTIONID=$transactionID";

        $getAuthModeFromConstantFile = true;
        //$getAuthModeFromConstantFile = false;
        $nvpHeader = "";

        if(!$getAuthModeFromConstantFile)
        {
            //$AuthMode = "3TOKEN"; //Merchant's API 3-TOKEN Credential is required to make API Call.
            //$AuthMode = "FIRSTPARTY"; //Only merchant Email is required to make EC Calls.
            $AuthMode = "THIRDPARTY"; //Partner's API Credential and Merchant Email as Subject are required.
        }
        else
        {
            if(!empty($API_UserName) && !empty($API_Password) && !empty($API_Signature) && !empty($subject))
            {
                $AuthMode = "THIRDPARTY";
            }
            else if(!empty($API_UserName) && !empty($API_Password) && !empty($API_Signature))
            {
                $AuthMode = "3TOKEN";
            }
            else if(!empty($subject))
            {
                $AuthMode = "FIRSTPARTY";
            }
        }

        switch($AuthMode) {

        case "3TOKEN" :
            $nvpHeader = "&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature);
            break;
        case "FIRSTPARTY" :
            $nvpHeader = "&SUBJECT=".urlencode($subject);
            break;
        case "THIRDPARTY" :
            $nvpHeader = "&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature)."&SUBJECT=".urlencode($subject);
            break;

        }

        $nvpStr = $nvpHeader.$nvpStr;

        return $nvpStr;
    }

    private function process_transaction_search($start, $end)
    {
        /* Make the API call to PayPal, using API signature.
           The API response is stored in an associative array called $resArray */

        $nvpStr = $this->format_nvp_search_str($start, $end);

        $resArray = hash_call("TransactionSearch", $nvpStr);

        if (! $resArray) {
            var_dump($resArray);
            $this->_damage_error($start, $end);
            return false;
        }

        /* Next, collect the API request in the associative array $reqArray
           as well to display back to the browser.
           Normally you wouldnt not need to do this, but its shown for testing */

        $reqArray = $_SESSION['nvpReqArray'];

        /* Display the API response back to the browser.
           If the response from PayPal was a success, display the response parameters'
           If the response was an error, display the errors received using APIError.php.
           */
        $ack = strtoupper($resArray["ACK"]);

        if($ack!="SUCCESS" && $ack!="SUCCESSWITHWARNING") {
            var_dump($resArray);
            $this->_damage_error($start, $end);
            return false;

        }
        $resArray = NVPToArray($resArray);

        return $resArray['transactions'];
    }

    private function get_transaction_details($transactionID)
    {
        $nvpStr = $this->format_nvp_details_str($transactionID);
        $resArray = hash_call("gettransactionDetails",$nvpStr);

        if (! $resArray)
        {
            $this->_log_transaction_detail_error($transactionID);
            $this->_ignore_order($transactionID);

            return false;
        }

        /* Next, collect the API request in the associative array $reqArray
           as well to display back to the browser.
           Normally you wouldnt not need to do this, but its shown for testing */

        $reqArray = $_SESSION['nvpReqArray'];

        /* Display the API response back to the browser.
           If the response from PayPal was a success, display the response parameters'
           If the response was an error, display the errors received using APIError.php.
           */
        $ack = strtoupper($resArray["ACK"]);

        if($ack != "SUCCESS" && $ack!="SUCCESSWITHWARNING") {
            $this->_ignore_order($transactionID);

            return false;
        }

        return $resArray;
    }


    // import transactions
    public function import_transactions($user_name)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'paypal_order.php') === FALSE)
        {
            exit;
        }
        $all_transactions = array();
        $this->user = $user_name;
        $this->uid = $this->order_model->get_user_id_by_name($user_name);
		$this->paypal = $this->paypal_model->get_paypal_account($user_name);
		
		//把上次时间前推5分钟，覆盖漏单情况
		//$datetimes = explode(":",$this->paypal_model->get_paypal_import_beginning_time($this->user));
		$datetimes=str_replace("Z","",str_replace("T"," ",$this->paypal_model->get_paypal_import_beginning_time($this->user)));
		$datetime=date('Y-m-d\TH:i:s\Z',mktime(substr($datetimes,11,2),substr($datetimes,14,2)-2,substr($datetimes,17,2),substr($datetimes,5,2),substr($datetimes,8,2),substr($datetimes,0,4)));
		$this->start_time = $datetime;

        //$this->start_time = $this->paypal_model->get_paypal_import_beginning_time($this->user);
        //$this->end_time = get_utc_time('-5 minutes');  // 5 minutes ago
		$this->end_time=date('Y-m-d\TH:i:s\Z',mktime(substr($datetimes,11,2)+40,substr($datetimes,14,2),substr($datetimes,17,2),substr($datetimes,5,2),substr($datetimes,8,2),substr($datetimes,0,4)));
		$startdate=strtotime($this->end_time);
		$enddate=strtotime(get_utc_time('-5 minutes'));
		if($enddate-$startdate<=0){
			$this->end_time = get_utc_time('-5 minutes');
		}
        echo "End time: ", $this->end_time, "\n";

        $start_time = $this->start_time;
        $end_time = $this->end_time;
		//$start_time = '2012-10-19T19:45:59Z';
		//$end_time = '2012-10-19T19:45:59Z';
        
        $save_end_time = $end_time;
        while ($transactions = $this->process_transaction_search($start_time, $end_time))
        {
            $transaction_count = count($transactions);
            if ($transaction_count > 0)
            {
                $end_time = $transactions[$transaction_count - 1]['timeStamp'];
                $all_transactions = array_merge($all_transactions, $transactions);
                if (strcmp($end_time, $start_time) <= 0 || strcasecmp($save_end_time, $end_time) == 0)
                {
                    break;
                }
                $save_end_time = $end_time;
            } else {
                break;
            }
        }

        $all_transactions = array_reverse($all_transactions);
        $this->_proccess_import_transactions($all_transactions);

        $this->paypal_model->update_paypal_import_beginning_time(array('value' => $this->end_time), $this->user);
        $this->log_report();
        echo 'Done!';
    }

    private function _proccess_import_transactions($all_transactions)
    {
        $total_count = count($all_transactions);
        foreach ($all_transactions as $transaction)
        {
            echo "=================================== ", $total_count--, " orders left ============================\n";

            $transaction_id = $transaction['transactionID'];
            /*
             * 订单已存在，不读取订单信息
             */
            /*
             * ********************在导入订单时必须重新判断*****************************
             */
            if ($this->paypal_model->is_transaction_exists($transaction_id)
                    || $this->paypal_model->is_transaction_merged($transaction_id))
            {
                $this->report['order_exists']++;

                continue;
            }
            echo "===================================Starting geting Transaction===========================\n";
            $transaction_details = $this->get_transaction_details($transaction_id);

            
            echo "===================================Ending geting Transaction===========================\n";
            if ($transaction_details) {
                $this->_save_transaction_details($transaction_details);
            }
            echo "=================================== Finish one transaction ============================\n";
        }
    }

    private function _save_transaction_details($transaction_details)
    {
        $step = $this->_check_save_step($transaction_details);

        /*
         * $step = -1 : 数据不全
         * $step = 0 : 直接交易的订单
         * $step = 1 : 未结清的订单
         * $step = 2 : 未分配的订单
         * $step = 3 : 待确认订单
         * $step = 4 : 直接通过确认
         * $step = 5 : 订单重复
         * $step = 6 : 合并订单
         * $step = 7 : 收入为负数
         * $step = 8 : 未授权订单
         */

        echo $step, "===============================\n";

        switch ($step)
        {
        case -1:
            $this->_incomplete_order($transaction_details);
            break;
        case 0:
            $this->_direct_transaction($transaction_details);
            break;
        case 1:
            $this->_uncleared_order($transaction_details);
            break;
        case 2:
            $this->_waiting_comfirmed_order($transaction_details);
            break;
        case 3:
            $this->_not_assigned_order($transaction_details);
            break;
        case 4:
            $this->_auto_comfirmed_order($transaction_details);
            break;
        case 5:
            $this->_order_exists($transaction_details);
            break;
        case 6:
            if ($this->to_merged_id)
            {
                $this->_merge_order($this->to_merged_id, $transaction_details);
                $this->to_merged_id = false;
            }
            break;
        case 7:
            break;
        case 8:
            $this->_import_unauthorized_order($transaction_details);
            break;
        case 100:
            $this->_waiting_for_handle_order($transaction_details);
            break;
        }

        unset ($item_info);
    }

    private function _check_save_step($transaction_details)
    {
        /*
         * 收入为负数
         */
        if ((!is_numeric($transaction_details["AMT"])) || ($transaction_details["AMT"] < 0))
        {
            return 7;
        }

        /*
         * 数据不全
         */
        $order_name0 = '';
        if (isset($transaction_details['L_NAME0']))
        {
            $order_name0 = $transaction_details['L_NAME0'];
        }
        $order_qty0 = '';
        if (isset($transaction_details['L_QTY0'])) {
            $order_qty0 = $transaction_details['L_QTY0'];
        }
        if ($order_name0 == '' || $order_qty0 == '') {
            /*
             * 直接付款
             */
            $transaction_type = $transaction_details['TRANSACTIONTYPE'];
            if ($transaction_type == 'sendmoney') {
                return 0;
            }

            return -1;
        }

        /*
         * 订单重复
         */
        $transaction_id = $transaction_details['TRANSACTIONID'];
        if ($this->paypal_model->is_transaction_exists($transaction_id)
                || $this->paypal_model->is_transaction_merged($transaction_id))
        {

            return 5;
        }

        /*
         * 未结清的订单
         */
        $uncleard_status = array(
                               'uncleared',
                               'pending'
                           );

        if (in_array(strtolower($transaction_details['PAYMENTSTATUS']), $uncleard_status))
        {
            return 1;
        }

        /*
         * 未授权订单
         */
        if ($transaction_details['REASONCODE'] != 'None')
        {
            return 8;
        }

        /*
         * 判断是否合法订单
         */
        if (strtolower($transaction_details["PAYMENTSTATUS"]) == "completed"
                || strtolower($transaction_details["PAYMENTSTATUS"]) == "cleared"
                || strtolower($transaction_details["PAYMENTSTATUS"]) == "on hold - ship now")
        {
            $this->item_info = array();
            $this->_parse_item_info($transaction_details);

            /*
             * 合并订单
             */
			 
            if (isset($transaction_details['L_NUMBER0']))
            {
				$ebay_id = $this->paypal_model->get_ebay_id_by_item_id(isset($transaction_details['L_NUMBER0']) ? $transaction_details['L_NUMBER0'] : '');
                $this->to_merged_id = $this->paypal_model->can_merge_order($transaction_details, $this->item_info['user'],$ebay_id);
                if ($this->to_merged_id && $this->_is_items_good(false))
                {

                    return 6;
                }
            }

            /*
             * b2c magento order, wait for confirmation.
             */
            if ($this->user == 'b2c')
            {
                return 2;
            }

            /*
             * 未分配的订单
             */
            if (( ! isset($transaction_details['RECEIVERBUSINESS'])
                || $transaction_details['RECEIVERBUSINESS'] != $transaction_details['RECEIVEREMAIL'])
                //&& $transaction_details['TRANSACTIONTYPE'] == 'cart'
				)
            {

                return 3;
            }

            //$net_usd = $this->order_model->to_usd($transaction_details['CURRENCYCODE'], $transaction_details["AMT"] - $transaction_details["FEEAMT"]);
            //$auto_comfirmed_contries = auto_comfirmed_contries();

            //直接通过确认
            //if ($this->is_profit_good($transaction_details)
            //    && $net_usd <= 30
            //    && isset($transaction_details["SHIPTONAME"])
            //    && isset($transaction_details["SHIPTOCOUNTRYNAME"])
            //    && in_array($transaction_details["SHIPTOCOUNTRYNAME"], $auto_comfirmed_contries)
            //    && ! isset($transaction_details["NOTE"])
            //    && $this->_is_items_good())
            //{
            //
            //    return 4;
            //}

            //待确认

            // 待处理
            return 3;
        }
        $this->_ignore_order($transaction_details['TRANSACTIONID']);
    }

    private function _import_unauthorized_order($transaction_details)
    {
        $data = array(
                    'transaction_id' => $transaction_details['TRANSACTIONID'],
                    'input_user'     => $this->user
                );
        $this->paypal_model->save_unauthorized_order($data);
        $this->report['unauthorized']++;
    }

    private function _is_items_good($strict = true)
    {
        if (count($this->item_info['item_titles']) == count($this->item_info['item_ids'])
                && count($this->item_info['item_ids']) == count($this->item_info['item_qties'])
                && count($this->item_info['item_qties']) == count($this->item_info['item_codes']))
        {
            if ($strict)
            {
                if (in_array('', $this->item_info['item_codes']))
                {
                    return false;
                }
                // check if the all the items are shipped from the same base.
                if (count($this->item_info['item_titles']) > 1)
                {
                    $shipping_code = FALSE;
                    foreach ($this->item_info['item_titles'] as $item_title)
                    {
                        $tmp_code = $this->order_model->get_product_shipping_code($item_title, $this->item_info['user']);
                        if ($shipping_code === FALSE)
                        {
                            $shipping_code = $tmp_code;
                        }
                        else
                        {
                            if ($tmp_code != $shipping_code)
                            {
                                return FALSE;
                            }
                        }
                    }

                }
            }
            else
            {
                return true;
            }
            return true;
        }
        return false;
    }

    private function is_profit_good($transaction_details)
    {
        $gross = $transaction_details['AMT'];
        $fee = isset($transaction_details['FEEAMT']) ? $transaction_details['FEEAMT'] : 0;
        $currency = $transaction_details['CURRENCYCODE'];
        $gross_rmb = price(calc_currency($currency, $gross));
        $fee_rmb = price(calc_currency($currency, $fee));

        $skus = $this->item_info['item_codes'];
        $qties = $this->item_info['item_qties'];
        $item_ids = $this->item_info['item_ids'];
        $product_cost = 0;
        $shipping_weight = 0;
        $other_cost = 0.65;
		$paypal_transaction_id = array($transaction_details['TRANSACTIONID']);
        $shipping_type = get_register($this->item_info, $this->user,$currency,$paypal_transaction_id);
        $country_name_en = $transaction_details['SHIPTOCOUNTRYNAME'];
        if (isset($this->country_names[$country_name_en]))
        {
            $country_name_cn = $this->country_names[$country_name_en];
        }
        else
        {
            $country_name_cn = get_country_name_cn($country_name_en);
            $this->country_names[$country_name_en] = $country_name_cn;
        }

        $sale_mode = 'buy_now';
        // check sale mode
        foreach ($item_ids as $item_id)
        {
            $item = $this->ebay_model->fetch_ebay_item_by_item_id($item_id);
            if (isset($item->listing_type))
            {
                $sale_mode = $item->listing_type;
                break;
            }
        }
        if ($sale_mode == 'buy_now')
        {
            $eshop_list_fee_multiply = 1;
            $eshop_list_count = 100;
        }
        else
        {
            $bid_rate = 30;
            $eshop_list_fee_multiply = 1 + (100 - $bid_rate) / $bid_rate;
            $eshop_list_count = 1;
        }

        $i = 0;
        foreach ($skus as $sku)
        {
            $qty = $qties[$i];
            $product = $this->product_model->fetch_product_by_sku($sku);
            $product_cost += $product->price * $qty;
            $shipping_weight += $product->total_weight * $qty;

            $i++;
        }

        $eshop_codes = array(
                           'USD'   => 'ebay-USA',
                           'AUD'   => 'ebay-AU',
                           'GBP'   => 'ebay-UK',
                           'EUR'   => 'ebay-FR',
                       );

        $input = array(
                     'eshop_code'                => $eshop_codes[$currency],
                     'buyer_shipping_cost'       => 0,
                     'shipping_type'             => $shipping_type,
                     'shipping_country'          => $country_name_cn,
                     'total_weight'              => $shipping_weight,
                     'sale_mode'                 => $sale_mode,
                     'eshop_category'            => 0,      // default catalog
                     'suggest_price'             => $gross,
                     'key'                       => TRUE,   // calculate profit rate
                     'total_price'               => $product_cost,
                     'balance_profit'            => 0.3,
                     'eshop_list_count'          => $eshop_list_count,
                     'eshop_list_fee_multiply'   => $eshop_list_fee_multiply,
                     'pay_option'                => 0,                       // no need for paypal cost
                     'pay_discount'              => 0,
                     'other_cost'                => $other_cost,
                     'paypal_cost'               => $fee,
                 );

        $data = price_profit_rate($input);

        if ( ! is_array($data))
        {
            $this->user_note = lang('not_profit_rate_note');

            return FALSE;
        }
        $profit_rate = price($data['total_profit_rate']);
        $this->profit_rate = $profit_rate;
        $this->sale_mode = $sale_mode;
        $this->user_note = '';
        if ($profit_rate < 0)
        {
            if ($sale_mode == 'buy_now' OR $profit_rate < -0.3)
            {
                $product_cost = $data['product_cost'];
                $shipping_cost = $data['shipping_cost'];
                $trade_fee_rmb = price($data['trade_fee']);
                $list_fee_rmb = price($data['list_fee']);

                $this->user_note = sprintf(
                                       lang('wait_for_confirmation_user_note'),
                                       $profit_rate, $gross_rmb, $fee_rmb, $trade_fee_rmb, $list_fee_rmb,
                                       $product_cost, $shipping_cost, $other_cost
                                   );

                return FALSE;
            }
        }

        return TRUE;
    }

    /*
     * 收入为负数
     */
    private function _ignore_order($transactionId)
    {
        $this->save_ack_failed_order($transactionId);
        $this->report['ingored']++;
    }

    /*
     * 直接交易的订单
     */
    private function _direct_transaction($transaction_details)
    {
        $data = array(
                    'transaction_id' => $transaction_details['TRANSACTIONID'],
                    'input_user'     => $this->user,
                    'tomail'         => $transaction_details['RECEIVEREMAIL'],
                    'from_email'     => $transaction_details['EMAIL'],
                    'input_date'     => date("Y-m-d H:i:s")
                );

        $result = $this->paypal_model->save_direct_transaction($data);


        $this->report['send_money']++;
    }

    /*
     * 数据不全
     */
    private function _incomplete_order($transaction_details)
    {
        $this->report['incomplete']++;
    }

    /*
     * 未结清的订单
     */
    private function _uncleared_order($transaction_details)
    {
        $data = array(
                    'transaction_id' => $transaction_details['TRANSACTIONID'],
                    'input_user'     => $this->user,
                    'input_date'     => date('Y-m-d H:i:s')
                );
        $this->paypal_model->save_pending_order($data);

        $this->report['uncleared']++;
    }

    /*
     * 未分配
     */
    private function _not_assigned_order($transaction_details)
    {
        $this->_waiting_comfirmed_order($transaction_details, $this->order_statuses['wait_for_assignment']);

        $this->report['not_assigned']++;
    }

    /*
     * 待确认
     */
    private function _waiting_comfirmed_order($transaction_details, $order_status = NULL)
    {
        if ($order_status === NULL)
        {
            $order_status = $this->order_statuses['wait_for_confirmation'];
        }
        $data = $this->_make_common_order_list_data($transaction_details);
        $item_info = $this->_merge_duplicated_order($this->item_info);
        $this->item_info['item_titles'] = $item_info['item_titles'];
        $this->item_info['item_ids'] = $item_info['item_ids'];
        $this->item_info['item_qties'] = $item_info['item_qties'];
        $this->item_info['item_codes'] = $item_info['item_codes'];
		$this->item_info['item_price'] = $item_info['item_price'];
		$paypal_transaction_id = array($transaction_details['TRANSACTIONID']);

        $is_register = get_register($this->item_info, $this->user,$transaction_details['CURRENCYCODE'],$paypal_transaction_id);

        $data['item_title_str'] = implode(ITEM_TITLE_SEP, $this->item_info['item_titles']);
        $data['item_id_str'] = implode(',', $this->item_info['item_ids']);
		$data['item_price_str'] = implode(',', $this->item_info['item_price']);
        $data['qty_str'] = implode(',', $this->item_info['item_qties']);
        $data['input_user'] = $this->item_info['user'];
        $data['sku_str'] = implode(',', $this->item_info['item_codes']);
        $data['order_status'] = $order_status;

        // note for customer service.
        $data['note'] = '';
        if ($this->item_info['gross_usd'] >= 30)
        {
            //$data['note'] = lang('mouse_pad_gift');
        }
        $data['note'] .= $this->user_note . (isset($transaction_details['NOTE']) ? $transaction_details['NOTE'] : '');
        $this->user_note = '';
        $data['profit_rate'] = $this->profit_rate;
        $this->profit_rate = '';

        if ($is_register)
        {
            $data['is_register'] = $is_register;
        }
/*
        if ($this->user == 'b2c' && ! empty($transaction_details['INVNUM']))
        {
            $data['item_id_str'] = $transaction_details['INVNUM'];
        }*/
        $this->item_info['item_no'] = $this->order_model->create_item_no($data['input_user'], date("ymd"), substr($data['item_id_str'], -5), $transaction_details['TRANSACTIONID'], $is_register);

        $data['item_no'] = $this->item_info['item_no'];

        $data['sys_remark'] = $this->_create_sys_remark('wait_for_comfirm', $transaction_details['TRANSACTIONID']);

        $add_order_role = TRUE;

        // will do adding order role in the process of auto assinment step
        if ($order_status == $this->order_statuses['wait_for_assignment'])
        {
            $add_order_role = FALSE;
        }
        $order_id = $this->paypal_model->save_order_list($data, $add_order_role);
        $new_is_register = get_register_by_order_id($order_id);
        if ($new_is_register != $is_register)
        {
            $new_item_no = change_item_register($data['item_no'], $is_register, $new_is_register);
            $sys_remark = $data['sys_remark'] . sprintf(lang('change_item_no_to'), $new_is_register);
            $this->order_model->update_order_information($order_id, array(
                        'is_register'   => $new_is_register,
                        'item_no'       => $new_item_no,
                        'sys_remark'    => $sys_remark,
                    ));
        }

        // b2c order
        if ($this->user == 'b2c' && isset($order_id) && ! empty($data['invoice_number']))
        {
            //$this->customer->update_order($order_id, $data['invoice_number']);
        }

        if ($order_status == $this->order_statuses['wait_for_confirmation'])
        {
            $this->report['wait_for_comfirmed']++;
        }
    }

    /*
     * 待处理
     */
    private function _waiting_for_handle_order($transaction_details)
    {
        $order_status = 0;
        $data = $this->_make_common_order_list_data($transaction_details);
        //$item_info = $this->_merge_duplicated_order($this->item_info);
        //$this->item_info['item_titles'] = $item_info['item_titles'];
        //$this->item_info['item_ids'] = $item_info['item_ids'];
        //$this->item_info['item_qties'] = $item_info['item_qties'];
        //$this->item_info['item_codes'] = $item_info['item_codes'];
		$paypal_transaction_id = array($transaction_details['TRANSACTIONID']);

        $is_register = get_register($this->item_info, $this->user,$transaction_details['CURRENCYCODE'],$paypal_transaction_id);
        $data['is_register'] = $is_register;

        $data['item_title_str'] = implode(ITEM_TITLE_SEP, $this->item_info['item_titles']);
        $data['item_id_str'] = implode(',', $this->item_info['item_ids']);
		$data['item_price_str'] = implode(',', $this->item_info['item_price']);
        $data['qty_str'] = implode(',', $this->item_info['item_qties']);
        $data['input_user'] = $this->item_info['user'];
        $data['sku_str'] = implode(',', $this->item_info['item_codes']);
        $data['order_status'] = $order_status;

        // note for customer service.
        $data['note'] = '';
        if ($this->item_info['gross_usd'] >= 30)
        {
            //$data['note'] = lang('mouse_pad_gift');
        }
        $data['note'] .= isset($transaction_details['NOTE']) ? $transaction_details['NOTE'] : '';


        $this->item_info['item_no'] = $this->order_model->create_item_no($data['input_user'], date("ymd"), substr($data['item_id_str'], -5), $transaction_details['TRANSACTIONID'], $is_register);

        $data['item_no'] = $this->item_info['item_no'];

        $data['sys_remark'] = $this->_create_sys_remark('wait_for_handle', $transaction_details['TRANSACTIONID']);

        var_dump($data);
        $order_id = $this->paypal_model->save_order_list($data, FALSE);

        if ($order_status == 0)
        {
            $this->report['wait_for_handle']++;
        }
    }

    /*
     * 直接通过确认
     */
    private function _auto_comfirmed_order($transaction_details)
    {
        $data = $this->_make_common_order_list_data($transaction_details);
        $item_info = $this->_merge_duplicated_order($this->item_info);
        $this->item_info['item_titles'] = $item_info['item_titles'];
        $this->item_info['item_ids'] = $item_info['item_ids'];
        $this->item_info['item_qties'] = $item_info['item_qties'];
        $this->item_info['item_codes'] = $item_info['item_codes'];
		$this->item_info['item_price'] = $item_info['item_price'];

        $data['item_title_str'] = implode(ITEM_TITLE_SEP, $this->item_info['item_titles']);
        $data['item_id_str'] = implode(',', $this->item_info['item_ids']);
		$data['item_price_str'] = implode(',', $this->item_info['item_price']);
        $data['qty_str'] = implode(',', $this->item_info['item_qties']);
        $data['input_user'] = $this->item_info['user'];
        $data['sku_str'] = implode(',', $this->item_info['item_codes']);
        $data['order_status'] = $this->order_statuses['wait_for_purchase'];
        $data['bursary_check_user'] = $this->user;
        $data['bursary_check_date'] = date("Y-m-d H:i:s");
        $data['check_user'] = $this->item_info['user'];
        $data['check_date'] = date("Y-m-d H:i:s");

        // description for shipping service
        if ($this->item_info['gross_usd'] >= 30)
        {
            //$data['descript'] = lang('mouse_pad_gift');
        }
		$paypal_transaction_id = array($transaction_details['TRANSACTIONID']);

        $is_register = get_register($this->item_info, $this->user,$transaction_details['CURRENCYCODE'],$paypal_transaction_id);
        if ($is_register)
        {
            $data['is_register'] = $is_register;
        }
        $this->item_info['item_no'] = $this->order_model->create_item_no($data['input_user'], date("ymd"), substr($data['item_id_str'], -5), $transaction_details['TRANSACTIONID'], $is_register);
        $data['item_no'] = $this->item_info['item_no'];

        $data['sys_remark'] = $this->_create_sys_remark('auto_confirmed', $transaction_details['TRANSACTIONID']);

        $order_id = $this->paypal_model->save_order_list($data);
        $new_is_register = get_register_by_order_id($order_id);
        if ($new_is_register != $is_register)
        {
            $new_item_no = change_item_register($data['item_no'], $is_register, $new_is_register);
            $sys_remark = $data['sys_remark'] . sprintf(lang('change_item_no_to'), $new_is_register);
            $this->order_model->update_order_information($order_id, array(
                        'is_register'   => $new_is_register,
                        'item_no'       => $new_item_no,
                        'sys_remark'    => $sys_remark,
                    ));
        }

        $this->report['auto_comfirmed']++;
    }

    /*
     * 订单重复
     */
    private function _order_exists($transaction_details)
    {
        $this->report['order_exists']++;
    }

    /*
     * 合并订单
     */
    private function _merge_order($to_merged_id , $transaction_details)
    {
        $order_obj = $this->paypal_model->get_order_info_for_merge($to_merged_id);
        if ( ! $order_obj)
        {
            return $this->_waiting_comfirmed_order($transaction_details);
        }

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
                         'item_titles' => array_merge(explode(ITEM_TITLE_SEP, $item_title), $this->item_info['item_titles']),
                         'item_ids' => array_merge(explode(',', $item_id), $this->item_info['item_ids']),
                         'item_codes' => array_merge(explode(',', $sm_products_code), $this->item_info['item_codes']),
                         'item_qties' => array_merge(explode(',', $amount), $this->item_info['item_qties'])
                     );

        $item_info = $this->_merge_duplicated_order($item_info);

        if ($currency == $transaction_details['CURRENCYCODE'])
        {
            $new_gross = $gross + $transaction_details['AMT'];
            $new_fee = $fee + $transaction_details['FEEAMT'];
	    	$new_shippingamt=$shippingamt+$transaction_details['SHIPPINGAMT'];
        }
        else
        {
            $tmp_rmb = calc_currency($transaction_details['CURRENCYCODE'], $transaction_details['AMT']);
            $new_gross = $gross + price(to_foreigh_currency($currency, $tmp_rmb));
            $tmp_rmb = calc_currency($transaction_details['CURRENCYCODE'], $transaction_details['FEEAMT']);
            $new_fee = $fee + price(to_foreigh_currency($currency, $tmp_rmb));
	    	$tmp_rmb = calc_currency($transaction_details['CURRENCYCODE'], $transaction_details['SHIPPINGAMT']);
	    	$new_shippingamt=$shippingamt+price(to_foreigh_currency($currency, $tmp_rmb));
        }
        echo "$new_gross, $new_fee, $new_shippingamt\n";
        echo "currency: $currency\n";
        echo "transaction_details: {$transaction_details['CURRENCYCODE']}\n";
        echo "gross: $gross, {$transaction_details['AMT']}\n";
        echo "fee: $fee, {$transaction_details['FEEAMT']}\n";
		echo "shippingamt: $shippingamt, {$transaction_details['SHIPPINGAMT']}\n";
		$paypal_transaction_id = array($transaction_details['TRANSACTIONID'],$order_obj->transaction_id);

        $new_net = $new_gross - $new_fee;
        $new_title = implode(ITEM_TITLE_SEP, $item_info['item_titles']);
        $new_id = implode(',', $item_info['item_ids']);
        $new_sm_products_code = implode(',', $item_info['item_codes']);
        $new_amount = implode(',', $item_info['item_qties']);
		$new_gross_usd=$this->order_model->to_usd($currency,$new_gross);
		$item_info['gross_usd']=$new_gross_usd;
		$item_info['gross']=$new_gross;
		$new_is_register = get_register($item_info, $this->user,$currency,$paypal_transaction_id);
		$new_item_no = change_item_register($item_no,$is_register,$new_is_register);
		$order_status = $this->order_statuses['wait_for_confirmation'];

        $data = array(
                    'transaction_id'     => $transaction_details['TRANSACTIONID'],
                    'gross'              => $new_gross,
                    'fee'                => $new_fee,
                    'net'                => $new_net,
					'shippingamt'        => $new_shippingamt,
                    'item_title_str'     => $new_title,
                    'item_id_str'        => $new_id,
                    'sku_str'            => $new_sm_products_code,
                    'qty_str'            => $new_amount,
					'is_register'		 => $new_is_register,
					'item_no'			 => $new_item_no,
					'order_status'		 => $order_status,
                );
        $sys_remark = $this->_create_sys_remark('merged_order', $transaction_details['TRANSACTIONID']);
        $this->paypal_model->merge_order($to_merged_id, $data, $sys_remark);
        $this->report['merged_order']++;
    }
	public function ack_inport_one($transaction_id, $input_user) {
        $this->set_paypal_account($input_user);

        $transaction_id = $transaction_id;
        $transaction_details = $this->get_transaction_details($transaction_id);

        if ($transaction_details) {
            $this->_save_transaction_details($transaction_details);
        }

        if ($this->paypal_model->is_transaction_exists($transaction_id) || $this->paypal_model->is_transaction_merged($transaction_id)) {
            $this->paypal_model->remove_completed_order($transaction_id);
        }
        echo 'Done!';
    }

    private function _make_common_order_list_data($transaction_details)
    {
        $ship_to_name = isset($transaction_details["SHIPTONAME"]) ? $transaction_details["SHIPTONAME"] : '';
        $ship_to_name = html_entity_decode($ship_to_name);
        $ship_to_street = isset($transaction_details['SHIPTOSTREET']) ? $transaction_details['SHIPTOSTREET'] : '';
        $ship_to_street2 = isset($transaction_details["SHIPTOSTREET2"]) ? $transaction_details["SHIPTOSTREET2"] : '';
        $ship_to_city = isset($transaction_details['SHIPTOCITY']) ? $transaction_details['SHIPTOCITY'] : '';
        $ship_to_country = isset($transaction_details['SHIPTOCOUNTRYNAME']) ? $transaction_details['SHIPTOCOUNTRYNAME'] : '';
        $ship_to_state = isset($transaction_details['SHIPTOSTATE']) ? $transaction_details['SHIPTOSTATE'] : '';
        $note = isset($transaction_details['NOTE']) ? $transaction_details['NOTE'] : '';
        $subject = isset($transaction_details['SUBJECT']) ? $transaction_details['SUBJECT'] : '';
        $ship_handle_amount = isset($transaction_details['SHIPHANDLEAMOUNT']) ? $transaction_details['SHIPHANDLEAMOUNT'] : '';
        $from_email = isset($transaction_details['RECEIVERBUSINESS']) ? $transaction_details['RECEIVERBUSINESS'] : $transaction_details['RECEIVEREMAIL'];
        $ship_to_zip = isset($transaction_details['SHIPTOZIP']) ? $transaction_details['SHIPTOZIP'] : '';
        $shipping_address = $transaction_details["SHIPTONAME"] ." ".$transaction_details["SHIPTOSTREET"]." ".$ship_to_street2." ".$transaction_details["SHIPTOCITY"]." ".$ship_to_state." ".$transaction_details["SHIPTOCOUNTRYNAME"];
        $import_date=date("Y-m-d H:i:s");
        $fee = isset($transaction_details['FEEAMT']) ? $transaction_details['FEEAMT'] : 0;
        $insuranceamount = isset($transaction_details['INSURANCEAMOUNT']) ? $transaction_details['INSURANCEAMOUNT'] : '';
        $buyer_id = isset($transaction_details['BUYERID']) ? $transaction_details['BUYERID'] : '';
        $closing_date = isset($transaction_details['CLOSINGDATE']) ? $transaction_details['CLOSINGDATE'] : '';
        $invoice_number = isset($transaction_details['INVNUM']) ? $transaction_details['INVNUM'] : '';
        $paid_time = isset($transaction_details['ORDERTIME']) ? $transaction_details['ORDERTIME'] : '';
		$ebay_id = $this->paypal_model->get_ebay_id_by_item_id(isset($transaction_details['L_NUMBER0']) ? $transaction_details['L_NUMBER0'] : '');
		

        /*
         * erase shipping information, we need them from b2c.
         */
		 /*
        if ($this->user == '053')
        {
            $ship_to_name = '';
            $shipping_address = '';
            $ship_to_street = '';
            $ship_to_street2 = '';
            $ship_to_city = '';
            $ship_to_state = '';
            $ship_to_zip = '';
            $ship_to_country = '';
        }*/
        
        $data = array(
                    'list_date'                 => gmt_to_pdt($transaction_details['ORDERTIME'], 'D'),
                    'list_time'                 => gmt_to_pdt($transaction_details['ORDERTIME'], 'T'),
                    'time_zone'                 => 'PDT',
                    'name'                      => $ship_to_name,
                    'list_type'                 => $transaction_details['TRANSACTIONTYPE'],
                    'payment_status'            => $transaction_details['PAYMENTSTATUS'],
                    'subject'                   => $subject,
                    'currency'                  => $transaction_details['CURRENCYCODE'],
                    'gross'                     => $transaction_details['AMT'],
                    'fee'                       => $fee,
                    'net'                       => $transaction_details['AMT'] - $fee,
					'shippingamt'               => $transaction_details['SHIPPINGAMT'],
                    'note'                      => $note,
                    'from_email'                => $transaction_details['EMAIL'],
                    'to_email'                  => $from_email,
                    'transaction_id'            => $transaction_details['TRANSACTIONID'],
                    'payment_type'              => $transaction_details['PAYMENTTYPE'],
                    'counterparty_status'       => $transaction_details['PAYMENTTYPE'],
                    'shipping_address'          => $shipping_address,
                    'address_status'            => $transaction_details['ADDRESSSTATUS'],
                    'shipping_handling_amount'  => $ship_handle_amount,
                    'insurance_amount'          => $insuranceamount,
                    'sales_tax'                 => $transaction_details['TAXAMT'],
                    'auction_site'              => 'EA',
					'auction_site_type'			=> 'Ebay',
                    'buyer_id'                  => $buyer_id,
                    'item_url'                  => '',
                    'closing_date'              => $closing_date,
                    'reference_txn_id'          => '',
                    'invoice_number'            => $invoice_number,
                    'subscription_number'       => '',
                    'custom_number'             => '',
                    'receipt_id'                => $transaction_details['RECEIVERID'],
                    'balance'                   => '',
                    'address_line_1'            => $ship_to_street,
                    'address_line_2'            => $ship_to_street2,
                    'town_city'                 => $ship_to_city,
                    'state_province'            => $ship_to_state,
                    'zip_code'                  => $ship_to_zip,
                    'country'                   => $ship_to_country,
                    'contact_phone_number'      => '',
                    'balance_impact'            => 'CREDIT',
                    'income_type'               => 'Paypal',
                    'input_date'                => $import_date,
                    'input_from_row'            => 0,
                    'paid_time'                 => $paid_time,
					'ebay_id'					=> $ebay_id,
					'domain'					=> $ebay_id,
                );

        return $data;
    }
	public function hours12_missing_inport_one($transaction_id, $ebay_id) {
        $paypalAcount = $this->config->item('paypalAcount');
        $input_user = $this->paypal_model->get_paypal_user_by_paypal($paypalAcount[$ebay_id]);
		//var_dump($paypalAcount[$ebay_id]);
		//var_dump($input_user);die();
        $this->set_paypal_account($input_user->user);
        $transaction_id = $transaction_id;
        $transaction_details = $this->get_transaction_details($transaction_id);

        if ($transaction_details) {
            $this->_save_transaction_details($transaction_details);
        }

        if ($this->paypal_model->is_transaction_exists($transaction_id) || $this->paypal_model->is_transaction_merged($transaction_id)) {
            $this->paypal_model->remove_completed_order($transaction_id);
        }
        echo 'Done!';
    }

    private function _parse_item_info($transaction_details)
    {
        $transactionID = $transaction_details['TRANSACTIONID'];

        $user = $this->user;
        $user_id = $this->uid;

        $paypal_email = isset($transaction_details['RECEIVERBUSINESS']) ? $transaction_details['RECEIVERBUSINESS'] : $transaction_details['RECEIVEREMAIL'];

        if (isset($this->paypal_email_login_name[$paypal_email]))
        {
            $user_arr = $this->paypal_email_login_name[$paypal_email];
            $user = $user_arr['name'];
            $user_id = $user_arr['id'];
        }
        else
        {
            $user_obj = $this->paypal_model->fetch_user_by_paypal_email($paypal_email);
            if ( ! empty($user_obj))
            {
                $user = $user_obj->login_name;
                $user_id = $user_obj->id;
            }
            $this->paypal_email_login_name[$paypal_email] = array('name' => $user, 'id' => $user_id);
        }

        $this->item_info['user'] = $user;
        $this->item_info['uid'] = $user_id;

        $this->_set_order_products($transaction_details);
		/*
        if (isset($transaction_details['INVNUM']))
        {
            $item_no_id = $transaction_details['INVNUM'];
        }
        else
        {
            $item_no_id = isset($this->item_info['item_ids'][0]) ? $this->item_info['item_ids'][0] : '';
        }*/
		$item_no_id = isset($this->item_info['item_ids'][0]) ? $this->item_info['item_ids'][0] : '';
        $item_no = $this->order_model->create_item_no($user, date("ymd"), substr($item_no_id, -5), $transactionID, "");

        $this->_set_product_codes();

        $this->item_info['item_no'] = $item_no;
        $this->item_info['ship_to_country'] = $transaction_details['SHIPTOCOUNTRYNAME'];
        $this->item_info['gross_usd'] = $this->order_model->to_usd($transaction_details['CURRENCYCODE'], $transaction_details["AMT"]);
        $this->item_info['our_paypal_email'] = isset($transaction_details['RECEIVERBUSINESS']) ? $transaction_details['RECEIVERBUSINESS'] : $transaction_details['RECEIVEREMAIL'];
		$this->item_info['gross'] =$transaction_details['AMT'];
    }

    private function _set_order_products($transaction_details)
    {
        $start_index = 0;
        $item_titles = array();
        $item_ids = array();
        $item_qties = array();
		$item_price = array();

        $filters = array(
                       'ems express' => 'E',
                       'track number' => 'PT',
                   );

        while(isset($transaction_details['L_NAME' . $start_index]) && isset($transaction_details['L_NUMBER' . $start_index]))
        {
            $is_shipping = FALSE;
            $l_name = $transaction_details['L_NAME' . $start_index];
			if(substr($l_name,0,18)=="xxxBASE64_STARTxxx"){
				$l_name = str_replace('xxxBASE64_STARTxxx','',$l_name);
				$l_name = str_replace('xxxBASE64_ENDxxx','',$l_name);
				$l_name=base64_decode($l_name);
			}
            /*
             * b2c
             */
            if ($this->user == 'b2c')
            {
                foreach ($filters as $key => $value)
                {
                    if (strpos(strtolower($l_name), $key) !== FALSE)
                    {
                        $this->is_register = $value;
                        $is_shipping = TRUE;
                        break;
                    }
                }
            }
            if ($is_shipping)
            {
                $start_index++;
                continue;
            }
            $item_titles[] = $l_name;
            $item_ids[] = $transaction_details['L_NUMBER' . $start_index];
            $item_qties[] = $transaction_details['L_QTY' . $start_index];
			$item_price[] = $transaction_details['L_AMT' . $start_index];
            $start_index++;
        }

        $this->item_info['item_titles']= $item_titles;
        $this->item_info['item_ids'] = $item_ids;
		$this->item_info['item_price'] = $item_price;
        $this->item_info['item_qties'] = $item_qties;
    }

    private function _set_product_codes()
    {
        $uid = $this->item_info['uid'];
        $item_titles = $this->item_info['item_titles'];
        $item_ids = $this->item_info['item_ids'];
        $amounts = $this->item_info['item_qties'];
	$item_price = $this->item_info['item_price'];

        $new_item_titles = array();
        $new_item_ids = array();
        $new_amounts = array();
        $item_codes = array();
		$new_item_price = array();
        foreach ($item_titles as $i => $item_title) {
            $product_codes= $this->order_model->get_product_by_netname(trim($item_title), $uid);
            if (empty($product_codes))
            {
                $product_codes = '';
            }
            if (substr_count($product_codes, ','))
            {
                $product_codes = explode(',', $product_codes);
                foreach ($product_codes as $product_code)
                {
                    $new_item_titles[] = $item_title;
                    $new_item_ids[] = $item_ids[$i];
                    $new_amounts[] = $amounts[$i];
                    $item_codes[] = $product_code;
					$new_item_price[] = $item_price[$i];
                }
            }
            else
            {
                $new_item_titles[] = $item_title;
                $new_item_ids[] = $item_ids[$i];
                $new_amounts[] = $amounts[$i];
                $item_codes[] = $product_codes;
				$new_item_price[] = $item_price[$i];
            }
        }
        $this->item_info['item_titles'] = $new_item_titles;
        $this->item_info['item_ids'] = $new_item_ids;
        $this->item_info['item_qties'] = $new_amounts;
        $this->item_info['item_codes'] = $item_codes;
		$this->item_info['item_price'] = $new_item_price;
    }
	public function auto_import_12hours_missing_orders()
	{
		if (strpos($_SERVER['SCRIPT_FILENAME'], 'auto_import_12hours_missing_orders.php') === FALSE)
		{
			exit;
		}
		$orders=$this->order_model->fetch_all_import_12hours_missing_orders();
		foreach ($orders as $order)
		{
			echo $order->transaction_id."---".$order->ebay_id."---\n";
			$this->hours12_missing_inport_one($order->transaction_id, $order->ebay_id);
		}
	   
   }

    /*
     * 把重复的订单合并在一起
     */
    private function _merge_duplicated_order($item_info)
    {
        $item_titles = $item_info['item_titles'];
        $item_ids = $item_info['item_ids'];
        $item_qties = $item_info['item_qties'];
        $item_codes = $item_info['item_codes'];

        $new_item_titles = array();
        $new_item_ids = array();
        $new_amounts = array();
        $new_item_codes = array();

        $item_count = count($item_ids);        
        if ($item_count <= 1) {
            return $item_info;
        }
        $new_item_codes[] = $item_codes[0];
        $new_item_titles[] = $item_titles[0];
        $new_item_ids[] = $item_ids[0];
        $new_amounts[] = $item_qties[0];

        for ($i = 1; $i < $item_count; $i++)
        {
            if (empty ($item_codes[$i]))
            {
                $new_item_titles[] = $item_titles[$i];
                $new_item_ids[] = $item_ids[$i];
                $new_amounts[] = $item_qties[$i];
                $new_item_codes[] = $item_codes[$i];
                continue;
            }
            if (!in_array($item_codes[$i], $new_item_codes) || !in_array($item_ids[$i], $new_item_ids))
            {
                $new_item_titles[] = $item_titles[$i];
                $new_item_ids[] = $item_ids[$i];
                $new_amounts[] = $item_qties[$i];
                $new_item_codes[] = $item_codes[$i];
            }
            else
            {
                $index = array_search($item_codes[$i], $new_item_codes);
                $ode = $item_qties[$i];
                $new_amounts[$index] += $ode;
            }
        }

        return array(
                   'item_titles'   => $new_item_titles,
                   'item_ids'      => $new_item_ids,
                   'item_qties'    => $new_amounts,
                   'item_codes'    => $new_item_codes
               );
    }

    private function _create_sys_remark($type, $transaction_id)
    {
        $import_date = date("Y-m-d H:i:s");
        $code = implode(',',$this->item_info['item_codes']);
        $title = implode(ITEM_TITLE_SEP, $this->item_info['item_titles']);
        $qty = implode(',',$this->item_info['item_qties']);

        $wait_for_comfirm = <<<DATA
                            $import_date 由 $this->user 导入本订单(程序自动导单)，编号为 {$this->item_info['item_no']} \n;
DATA;
        $wait_for_handle = <<<DATA
                           $import_date 由 $this->user 导入本订单(程序自动导单)，编号为 {$this->item_info['item_no']}, 进入待处理状态 \n;
DATA;
        $auto_confirmed = <<<DATA
                          $import_date 由 $this->user  导入本订单(程序自动导单)，编号为 {$this->item_info['item_no']} \n此订单符合自动确认的条件，自动确认并审核。\n
DATA;
        $merged_order = <<<DATA
$import_date  由  $this->user  系统判断 {$this->item_info['item_no']} 是重复订单，自动合并。增加商品 {$code};
数量分别为 {$qty}; transaction_id为 $transaction_id; item_title加上了 {$title};\n
订单为合并订单(程序自动导单)\n'
DATA;

        $remarks = array(
        'wait_for_comfirm' =>$wait_for_comfirm,
        'auto_confirmed'   => $auto_confirmed,
        'merged_order'     => $merged_order,
        'wait_for_handle'  => $wait_for_handle,
        );

        return $remarks[$type];
    }

        private function log_report($head = '')
        {
        $message = <<<MSG
        $head&nbsp;
        自动导入信息(UTC {$this->start_time} - {$this->end_time})：\n
        待处理: {$this->report['wait_for_handle']}, 直接通过确认: {$this->report['auto_comfirmed']}条，待确认: {$this->report['wait_for_comfirmed']}条, 自动合并{$this->report['merged_order']}条\n
        直接付款：{$this->report['send_money']}条, 订单已存在: {$this->report['order_exists']}条, 未分配：{$this->report['not_assigned']}条\n
        未结清： {$this->report['uncleared']}条， 信息不全：{$this->report['incomplete']}条, 被忽略：{$this->report['ingored']}条, 未授权：{$this->report['unauthorized']}条
MSG;

        $data = array(
        'import_date' => date("Y-m-d H:i:s"),
        'user_name'   => $this->user,
        'descript'    => $message,
        'user_login'  => $this->user
        );
        $this->paypal_model->import_log($data);
    }

        private function _damage_error()
        {
        $message = <<<MSG
        自动导入信息(UTC {$this->start_time} - {$this->end_time})：\n
        不能获取订单，自动终止，下次自动导单开始时间为{$this->start_time}（UTC)
MSG;

        $data = array(
        'import_date' => date("Y-m-d H:i:s"),
        'user_name'   => $this->user,
        'descript'    => $message,
        'user_login'  => $this->user
        );
        $this->paypal_model->import_log($data);
        die('error!');
    }

        private function _log_transaction_detail_error($transaction_id)
        {
        $message = <<<MSG
        自动导入信息(UTC {$this->start_time} - {$this->end_time})：\n
        不能获取订单({$transaction_id}),忽略该订单,需要手工检查
MSG;

        $data = array(
        'import_date' => date("Y-m-d H:i:s"),
        'user_name'   => $this->user,
        'descript'    => $message,
        'user_login'  => $this->user
        );
        $this->paypal_model->import_log($data);
    }

        private function save_ack_failed_order($transaction_id)
        {
        $data = array(
        'transaction_id' => $transaction_id,
        'input_user'     => $this->user,
        'email'          => '',
        'status'         => ''
        );

        $this->paypal_model->save_ack_failed_order($data);
    }

    private function set_paypal_account($user)
    {
        $this->user = $user;

        // no need to ask the database everytime.
        if (isset($this->accounts[$this->user]))
        {
        $this->uid = $this->accounts[$this->user]['uid'];
        $this->paypal = $this->accounts[$this->user]['paypal'];
        }
        else
        {
        $this->uid = $this->order_model->get_user_id_by_name($user);
        $this->paypal = $this->paypal_model->get_paypal_account($user);
        $this->accounts[$this->user] = array(
        'uid'       => $this->uid,
        'paypal'    => $this->paypal,
        );
        }
    }
}
