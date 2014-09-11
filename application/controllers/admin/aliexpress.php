<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Aliexpress extends Admin
{
	private $CI = NULL;
    public function  __construct()
    {
        parent::__construct();
		$this->CI = & get_instance();
		$this->load->library('form_validation');
        $this->load->model('config_model');
    }
    
    public function get_aliexpress_token()
    {
		$this->CI->load->config('config_aliexpress');
        $appKey = $this->CI->config->item('appKey');
        $appSecret = $this->CI->config->item('appSecret');
		$redirectUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		//����ǩ��
    	$code_arr = array(
        	'client_id' => $appKey,
        	'redirect_uri' => $redirectUrl,
        	'site' => 'aliexpress'
    	);
    	ksort($code_arr);
		$sign_str='';
    	foreach ($code_arr as $key=>$val)
		{
        	$sign_str .= $key . $val;
		}
    	$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
          
    	$get_code_url = "http://gw.api.alibaba.com/auth/authorize.htm?client_id={$appKey}&site=aliexpress&redirect_uri={$redirectUrl}&_aop_signature={$code_sign}";
		if(!isset($_REQUEST['code'])){
			echo 'You are being redirected to the alibaba website with get token';
			sleep(5);
			redirect($get_code_url);
		}else{
			//echo 'get_code_url:      '.$get_code_url.'<br /> <br />';
			$code=isset($_REQUEST['code'])?$_REQUEST['code']:'';
			//echo $code;
			$get_refresh_token_url ="https://gw.api.alibaba.com/openapi/http/1/system.oauth2/getToken/{$appKey}";
			//echo $get_refresh_token_url;
			$curlPost = 'grant_type=authorization_code&need_refresh_token=true&client_secret='.urlencode($appSecret).'&redirect_uri='.urlencode($redirectUrl).'&code='.urlencode($code).'&client_id='.urlencode($appKey).'';
			$ch = curl_init();//��ʼ��curl
			curl_setopt($ch,CURLOPT_URL,$get_refresh_token_url);//ץȡָ����ҳ
			curl_setopt($ch, CURLOPT_HEADER, 0);//����header
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Ҫ����Ϊ�ַ������������Ļ��
			curl_setopt($ch, CURLOPT_POST, 1);//post�ύ��ʽ
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$data = curl_exec($ch);//����curl
			curl_close($ch);
			$data=json_decode($data);
			//echo "<pre>";
			//var_dump($data);//������
			//echo "</pre>";
		}
		$data = array(
            'aliexpress_token'     => $data,
        );

        $this->template->write_view('content', 'admin/get_aliexpress_token', $data);
        $this->template->render();
    	
    }
	public function save_aliexpress_token()
	{
		$rules = array(
            array(
                'field' => 'aliid',
                'label' => lang('aliid'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'resource_owner',
                'label' => lang('resource_owner'),
                'rules' => 'trim|required',
            ),
			array(
                'field' => 'refresh_token',
                'label' => lang('refresh_token'),
                'rules' => 'trim|required',
            ),
			array(
                'field' => 'access_token',
                'label' => lang('access_token'),
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);
            return;
        }
        $aliid = trim($this->input->post('aliid'));
		$resource_owner = trim($this->input->post('resource_owner'));
		$refresh_token = trim($this->input->post('refresh_token'));
		$access_token = trim($this->input->post('access_token'));
		$data = array(
					  'aliid'=> $aliid,
					  'resource_owner'=> $resource_owner,
					  'refresh_token'=> $refresh_token,
					  'access_token'=> $access_token,
        );
		try
        {
			if ($this->system_model->check_exists('aliexpress_token', array('aliid' => $aliid)))
			{
				echo $this->create_json(0, lang('aliexpress_token_exists'));
				return;
			}else{
				$insert_id = $this->system_model->insert_aliexpress_token($data);
			}
			echo $this->create_json(1, lang('ok'));
		}catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
	}
	
	private function api_code_sign($apiInfo, $code_arr)
	{
		$this->CI->load->config('config_aliexpress');
        $appKey = $this->CI->config->item('appKey');
        $appSecret = $this->CI->config->item('appSecret');
		$url = 'http://gw.api.alibaba.com/openapi';
		$apiInfo = 'json/1/aliexpress.open/api.getChildrenPostCategoryById/' . $appKey;
		//urlencode('http://hanshisky.taobao.com');����˵ǩ����ʱ��Ҫ���߰����encode,����ת����ȥ
		//����ǩ��
		ksort($code_arr);
		$sign_str='';
		foreach ($code_arr as $key=>$val)
		{
        	$sign_str .= $key . $val;
		}
		$sign_str = $apiInfo . $sign_str;//ǩ������
		echo $sign_str."<br>";
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		return $code_sign;
	}
	
	public function test()
	{
		$this->CI->load->config('config_aliexpress');
        $appKey = $this->CI->config->item('appKey');
        $appSecret = $this->CI->config->item('appSecret');
		
		$url = 'http://gw.api.alibaba.com/openapi/';
		$apiInfo = 'json/1/aliexpress.open/api.getChildrenPostCategoryById/' . $appKey;
		$access_token='fb8feed8-62ec-466a-abb9-6ddd91b29767';
		$code_arr = array(
        'access_token' => urlencode($access_token),
        'cateId' => 1,
        //'site' => 'aliexpress'
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
echo $get_refresh_token_url;
echo "<br>";
$ch = curl_init();//��ʼ��curl
curl_setopt($ch,CURLOPT_URL,$get_refresh_token_url);//ץȡָ����ҳ
curl_setopt($ch, CURLOPT_HEADER, 0);//����header
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Ҫ����Ϊ�ַ������������Ļ��
//curl_setopt($ch, CURLOPT_POST, 0);//post�ύ��ʽ
//curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
$data = curl_exec($ch);//����curl
curl_close($ch);
//var_dump($data);//������
//echo "<br>";
$data=json_decode($data);
echo "<pre>";
var_dump($data);//������
echo "</pre>";

	}

   


}

?>
