<?php
function get_top_client()
{
    $CI = & get_instance();
    
    include_once APPPATH . 'libraries/taobao/TopSdk.php';

    $product_mode = TRUE; // TRUE or FALSE to toggle for product mode or sandbox mode.

    $CI->config->load('config_taobao', TRUE);
    if ($product_mode)
    {
        $config = $CI->config->item('product', 'config_taobao');
    }
    else
    {
        $config = $CI->config->item('sandbox', 'config_taobao');
    }

    $top_client = new TopClient($product_mode); 
    $top_client->appkey = $config['app_key']; 
    $top_client->secretKey = $config['app_secret'];  
    
    return $top_client;
}
?>
