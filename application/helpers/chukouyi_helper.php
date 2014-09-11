<?php

function cky_get_shipping_code($product_net_name, $input_user)
{
    $CI = & get_instance();
    if (! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    
    return $CI->order_model->get_product_shipping_code($product_net_name, $input_user);
}

function cky_check_shipping_support($shipping_code, $country_en)
{
    if (empty($shipping_code) || empty($country_en))
    {
        return FALSE;
    }
    $CI = & get_instance();
    if (! isset($CI->shipping_code_model))
    {
        $CI->load->model('shipping_code_model');
    }
    
    return $CI->shipping_code_model->cky_check_shipping_support($shipping_code, $country_en);
}

function cky_check_stock_support($sku, $stock_code)
{
    
}

?>
