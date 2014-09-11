<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
        
class Welcome extends Mallerp_no_key {
    
    /**
     * Magento API.
     * **/
    private $server_url;
    private $CI = NULL;
    private $authentication = array();
    /**
     * End
     * **/
    
    
    
	public function __construct()
	{
		parent::__construct();
        	$this->load->model('order_model');
			$this->CI = & get_instance();
    }
	public function update_oray_ip()
    {
		$this->CI->load->config('config_oray');
        $oray_username = $this->CI->config->item('oray_username');
        $oray_password = $this->CI->config->item('oray_password');
		$oray_hostname = $this->CI->config->item('oray_hostname');
		$html=file_get_contents('http://city.ip138.com/city.asp');
		preg_match('/\[(.*)\]/', $html, $ip);
		//var_dump($html);
		//echo $ip[1]."|".$oray_username."|".$oray_password."|".$oray_hostname;
		//die();
		$oray_url="http://".$oray_username.":".$oray_password."@ddns.oray.com/ph/update?hostname=".$oray_hostname."&myip=".$ip[1]."";
		
		 $user_agent = 'Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5';
		 //var_dump($user_agent);
		// ��ʼ��һ�� cURL ����
		$curl = curl_init();
		// ��������Ҫץȡ��URL
		curl_setopt($curl, CURLOPT_URL, $oray_url);
		// ����header
		//curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
		// ����cURL ������Ҫ�������浽�ַ����л����������Ļ�ϡ�
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// ����cURL��������ҳ
		$data = curl_exec($curl);
		// �ر�URL����
		curl_close($curl);
		// ��ʾ��õ�����
		var_dump($data);
		echo $oray_url;
    }

}
