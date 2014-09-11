<?php
require_once APPPATH.'controllers/mallerp'.EXT;
class Paypalapi extends Mallerp
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
            //$this->_ignore_order($transactionID);
            
            return false;
        }

        return $resArray;
    }
    private function refundtransaction_response($transactionID,$refundType,$amount,$currency,$memo)
    {
		
        $nvpStr = $this->format_nvp_details_str($transactionID);
		$nvpStr.="&REFUNDTYPE=$refundType&CURRENCYCODE=$currency&NOTE=$memo";
		if(strtoupper($refundType)=="PARTIAL") $nvpStr=$nvpStr."&AMT=$amount";
		//die($nvpStr);
        $resArray = hash_call("RefundTransaction",$nvpStr);

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
            //$this->_ignore_order($transactionID);
			var_dump($resArray);
            
            return false;
        }

        return $resArray;
    }	

	

	public function search_paypal_api()
	{
		$data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'order/paypal/search_paypal_api', $data);
        $this->template->render();
	}
	
	public function refundtransaction()
	{
		$data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'order/paypal/refundtransaction', $data);
        $this->template->render();
	}
	
    public function transaction_details() {
		if ($this->input->is_post())
        {
            $transactionID = $this->input->post('transaction_id');
            $user_name = $this->input->post('intut_user');
            
        }
        $this->user = $user_name;
        $this->uid = $this->order_model->get_user_id_by_name($user_name);
        $this->paypal = $this->paypal_model->get_paypal_account($user_name);
		
       $resArray = $this->get_transaction_details($transactionID);
	   
		//var_dump($resArray);
        $data['transaction_details'] = $resArray;
        
		$this->template->write_view('content', 'order/paypal/transaction_details', $data);
        $this->template->render();
    }
	 public function RefundReceipt() {
		 if ($this->input->is_post())
        {
            $user_name = $this->input->post('intut_user');
			$transactionID=urlencode($this->input->post('transactionID'));
			$refundType=urlencode($this->input->post('refundType'));
			$amount=urlencode($this->input->post('amount'));
			$currency=urlencode($this->input->post('currency'));
			$memo=urlencode($this->input->post('memo'));
            
        }
        
        $this->user = $user_name;
        $this->uid = $this->order_model->get_user_id_by_name($user_name);
        $this->paypal = $this->paypal_model->get_paypal_account($user_name);
		
       $resArray = $this->refundtransaction_response($transactionID,$refundType,$amount,$currency,$memo);
	   
		//var_dump($resArray);
        $data['refundtransaction_details'] = $resArray;
		 
		 
		 
		 
		$this->template->write_view('content', 'order/paypal/refundtransaction_details', $data);
        $this->template->render();
	 }
	
}

?>
