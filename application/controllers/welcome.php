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
		// 初始化一个 cURL 对象
		$curl = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_URL, $oray_url);
		// 设置header
		//curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 运行cURL，请求网页
		$data = curl_exec($curl);
		// 关闭URL请求
		curl_close($curl);
		// 显示获得的数据
		var_dump($data);
		echo $oray_url;
    }

}
