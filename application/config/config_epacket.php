<?php

$config['production'] = true;   // toggle to true if going against production
$config['debug'] = FALSE;   // toggle to provide debugging info
$config['version'] = '3.0.0';    // epacket API version
$config['message_id'] = 'MALLERP';
$config['pdf_path'] = '/var/www/html/mallerp/static/pdf/';

if ($config['production']) {
    $config['secret'] = array(
        'ebay' => array(
            'user_id'   => 'ebay',
            'dev_id'    => 'ebay',
            'api_key'   => 'eubkey',
        ),
    );
    $config['wsdl_url'] = 'http://shippingapi.ebay.cn/production/v3/orderservice.asmx?WSDL';
} else {
    // sandbox (test) environment
    $config['token'] = array(
            'user_id'   => 'mallerp',
            'dev_id'    => 'mallerp',
            'api_key'   => '6093110PHRS23S1Z213CYAY6SYZF4R540PA50PSZ96WZO4YO3WMUY52012141338',
        );
    $config['wsdl_url'] = 'http://epacketws.pushauction.net/v3/orderservice.asmx?WSDL';
}

?>