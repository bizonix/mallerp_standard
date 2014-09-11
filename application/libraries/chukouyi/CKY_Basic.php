<?php

class CKY_Basic
{
    protected $key;
    protected $product_gateway_url;
    protected $order_gateway_url;
    protected $CI;
    
    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->config->load('config_chukouyi', TRUE);
        $product_mode = TRUE;
        
        if ($product_mode)
        {
            $config = $this->CI->config->item('product', 'config_chukouyi');
        }
        else
        {
            $config = $this->CI->config->item('sandbox', 'config_chukouyi');
        }
        $this->key = $config['app_key'];
        $this->order_gateway_url = $config['order_gateway_url'];
        $this->product_gateway_url = $config['product_gateway_url'];
        
    }
}

?>