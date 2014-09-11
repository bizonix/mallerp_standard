<?php
    $config['APPLICATION_NAME']           = 'mallerp';
    $config['APPLICATION_VERSION']             = '2011-01-01';
    $config['production']         = true;
    $config['debug']              = false;
	/************************************************************************
	* Instantiate Implementation of MarketplaceWebServiceOrders
	* 
	* AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
	* are defined in the .config.inc.php located in the same 
	* directory as this sample
	***********************************************************************/
	// United States:
	//$serviceUrl = "https://mws.amazonservices.com/Orders/2011-01-01";
	// Europe
	//$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2011-01-01";
	// Japan
	//$serviceUrl = "https://mws.amazonservices.jp/Orders/2011-01-01";
	// China
	//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2011-01-01";
	// Canada
	//$serviceUrl = "https://mws.amazonservices.ca/Orders/2011-01-01";
	if ($config['production']) {
		$config["amazon_app"]=array(
			array(
				"serviceUrl"=>"https://mws-eu.amazonservices.com/Orders/2011-01-01",
				"AWS_ACCESS_KEY_ID"=>"keyid",
				"AWS_SECRET_ACCESS_KEY"=>"access key",
				"MERCHANT_ID"=>"",
				"MARKETPLACE_ID"=>"",
				),
			);
	}else
	{
	}
?>