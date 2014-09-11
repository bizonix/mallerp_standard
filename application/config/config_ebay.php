<?php
    //show all errors - useful whilst developing

    // these keys can be obtained by registering at http://developer.ebay.com
    
    $config['production']         = true;   // toggle to true if going against production
    $config['debug']              = false;   // toggle to provide debugging info
    $config['compatabilityLevel'] = 719;    // eBay API version
    
    //SiteID must also be set in the request
    //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
    //SiteID Indicates the eBay site to associate the call with
    //$siteID = 0;
    
    if ($config['production']) {
		$config['devID'] = '';   // these prod keys are different from sandbox keys
                $config['appID'] = '';
                $config['certID'] = '';
                //set the Server to use (Sandbox or Production)
                $config['serverUrl']   = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
                $config['shoppingURL'] = 'http://open.api.ebay.com/shopping';
        
                // This is used in the Auth and Auth flow
        
                // This is an initial token, not to be confused with the token that is fetched by the FetchToken call
                $config['appToken'] = array(
				'ebay'=> 'ebaytoken',
		);
		$config['ebay_id'] = array(
                    'ebay'=> 'ebay',
                );
		$config['paypalAcount'] = array(
			'ebay'=> 'paypal@gmail.com',
			);
    } else {  
        // sandbox (test) environment
        $config['devID']  = '';   // insert your devID for sandbox
        $config['appID']  = '';   // different from prod keys
        $config['certID'] = '';   // need three keys and one token
        //set the Server to use (Sandbox or Production)
        $config['serverUrl'] = 'https://api.sandbox.ebay.com/ws/api.dll';
        $config['shoppingURL'] = 'http://open.api.sandbox.ebay.com/shopping';
        
        $config['loginURL'] = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll'; // This is the URL to start the Auth & Auth process
        $config['feedbackURL'] = 'http://feedback.sandbox.ebay.com/ws/eBayISAPI.dll'; // This is used to for link to feedback
        
        $config['runame'] = 'testuser_mallerp';  // sandbox runame
        
        // This is the sandbox application token, not to be confused with the sandbox user token that is fetched.
        // This token is a long string - do not insert new lines. 
        $config['appToken'] = 'AgAAAA**AQAAAA**aAAAAA**hCJuTA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4CoAJWDpA2dj6x9nY+seQ**kl8BAA**AAMAAA**F9Ls8RnAde/c2HeY4ijj9WKkapgGC98XG6WCMCuhnQWjyeMDXB9ealIaY2HlLlvM+MT2NPxw1QfTr17iCG2HK6vXJuA3HFNJMTew6tqzNTMOfgZIFNmI4FP+VWb6VJZuX+PifRxyU18OrRjJkxKL8t194HBEze7k2u7cwIdc4duCYHYTgafi2zw2+eCIiUba2+KmI/0KnILJ9cwphljlm43KYmV5q2fEFuOY4j8fQrrzrM0RdlkZSv0S/xGYtk9Exso/KVsknd29h6Bbssu/LUpHQPurOcHxaygD9EBoDX/8BAxgJXTfvW2ci339NjaSbbPaUkCXTatwdFalm040sebVJGCacihe/c/2+tAYfVCAqrVzN8Rhd7UQ55JbpQN0iHJaNV7vCuyTss61nboB/1lG7BiC/n/5+o4lQom0lF00a4WgdWC5BAJmaTMR97tud2iCKzGCklkU0q3swB4UEt08giMxvziqiKDFFyD5SqDsYer67olKlyoz97ahUv4v+2TMXVfFA9rXYx4JwUQj0kkFIz/prOMddgmcx7nisbzFtY3I7oKR9y05M7ywXwjjc9U1EM4kHsXtw5BJdvzYbnfXY2QiCIU165akFVoWjQUEG5G0vk0gq5sFs+KlV2TtNK9UkZw2bSid18iXJ0SE4M5NrIXnrEv+8bpdgn2LWnS9fa3GJz4249X16eHH+ZsA+JP1KyCPCa8bqwFBE5v2xgu69CSL8YNg5KJz+adBYfj5cRFTC/H+eBLMbyzKBLLO';

        $config['paypalAcount'] = 'john@mallerp.com';
    }
    
    
?>
