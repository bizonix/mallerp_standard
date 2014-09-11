<?php
require_once APPPATH.'controllers/sale/sale'.EXT;

class Taobaoapi extends Sale
{
    protected $top_client;
    public function __construct()
    {
        parent::__construct();
        require_once APPPATH . 'libraries/taobao/TopSdk.php';
        
        $product_mode = FALSE; // TRUE or FALSE to toggle for product mode or sandbox mode.
        
        $this->config->load('config_taobao', TRUE);
        if ($product_mode)
        {
            $config = $this->config->item('product', 'config_taobao');
        }
        else
        {
            $config = $this->config->item('sandbox', 'config_taobao');
        }

        var_dump($config);
        
        $this->top_client = new TopClient($product_mode); 
        $this->top_client->appkey = $config['app_key']; 
        $this->top_client->secretKey = $config['app_secret'];  
    }    
}

?>
