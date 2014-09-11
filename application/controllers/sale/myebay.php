<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Myebay extends Mallerp_no_key
{
    protected $ebay_config;
    protected $appToken;

	public function  __construct()
    {
		parent::__construct();

        $this->config->load('config_ebay');
        $this->load->helper('xml_body');
        $this->load->helper('ebay');
        $this->load->helper('order');
        $this->load->model('ebay_model');
        $this->load->model('sale_model');
        $this->load->model('order_model');
        $this->load->model('epacket_model');
        $this->load->library('script');
        
        $this->ebay_config = array(
            'developerID'       => $this->config->item('devID'),
            'applicationID'     => $this->config->item('appID'),
            'certificateID'     => $this->config->item('certID'),
            'serverUrl'         => $this->config->item('serverUrl'),
            'compatabilityLevel'=> $this->config->item('compatabilityLevel')
        );
        $this->appToken = $this->config->item('appToken');
	}

    public function get_all_ebay_list()
    {
        echo 'Lion';
        $ebay_ids = array_keys($this->appToken);
        
        foreach ($ebay_ids as $ebay_id)
        {
            $this->get_ebay_list($ebay_id);
        }
    }
    
    public function get_ebay_list($ebay_id, $listing_type)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_ebay_list.php') === FALSE)
        {
            exit;
        }
        if ( ! in_array($listing_type, array('buy_now', 'auction')))
        {
            exit;
        }

        // keep Chinese Auction.
        if ($listing_type == 'buy_now')
        {
            $this->ebay_model->remove_outofday_ebay_list($ebay_id, $listing_type);
        }
        else
        {
            $this->ebay_model->update_outofday_ebay_list($ebay_id, $listing_type);
        }
        
        $itemsPerPage = 200;
        $pageIndex = 1;

        do
        {
            list($total_pages, $items) = $this->_proccess_get_ebay_list($ebay_id, $itemsPerPage, $pageIndex, $listing_type);

            foreach ($items as $item)
            {
                $qty = $item['qty'];

                /*
                if ($qty <= 10 && $listing_type == 'buy_now')
                {
                    $item_id = $item['item_id'];
                    $click_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=' . $item_id;
                   
                     $this->events->trigger(
                         'update_ebay_list_qty_low_after',
                         array(
                             'type'          => 'ebay_item_qty_low_notify',
                             'click_url'     => $click_url,
                             'content'       => sprintf(lang('ebay_item_qty_low_notify_params'), $item_id, $qty),
                             'owner_id'      => 0,
                         )
                     );
                }
                 */
                $this->ebay_model->save_ebay_list($item);           
            }
            $pageIndex++;
        }
        while($pageIndex <= $total_pages);
    }

    private function _proccess_get_ebay_list($ebay_id, $itemsPerPage, $pageIndex, $listing_type)
    {
        $site = 'US';
        if ($listing_type == 'auction')
        {
            $itemType = 'ActiveAuctionList';
        }
        else
        {
            $itemType = 'ActiveFixedPriceList';
        }


        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
        $itemInfo = array(
            'site' => $site,
            'itemType' => $itemType,
            'itemsPerPage' => $itemsPerPage,
            'pageIndex' => $pageIndex
        );

        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetMyeBaySelling';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = get_myebay_list($this->appToken, $itemInfo);
        $response = $this->xmlRequest($this->ebaysession, $xml);

        return $this->_parse_myebay_list($ebay_id, $itemType, $response);
    }

    private function _parse_myebay_list($ebay_id, $itemType, $response)
    {
        $total_pages = 0;
        $items = array();

        if ($response->Ack == 'Success')
        {
            switch ($itemType)
            {
                case 'ActiveAuctionList':
                case 'ActiveFixedPriceList':
                    $type = $response->ActiveList;
                    break;
                case 'BidList':
                    $type = $response->BidList;
                    break;
                case 'ScheduledList':
                    $type = $response->ScheduledList;
                    break;
                case 'SoldList':
                    $type = $response->SoldList;
                    break;
                case 'UnsoldList':
                    $type = $response->UnsoldList;
                    break;
            }
            
            if (isset($type) && isset($type->ItemArray))
            {
                foreach ($type->ItemArray->Item as $item)
                {
                    $price = $item->SellingStatus->CurrentPrice;
                    $price_attr = $price->attributes();
                    $currency_code = '';
                    foreach ($price_attr as $key => $value)
                    {
                        $currency_code = $value;
                        break;
                    }

                    //var_dump($item);


                    if (isset($item->Variations->Variation)) {//多属性
                        foreach ($item->Variations->Variation as $variation) {
                            echo $item->ItemID, " ", $item->ListingType, " ", $variation->SKU, "\n";
                            $qty1 = (int) $variation->Quantity;
                            $qty2 = (int) $variation->SellingStatus->QuantitySold;
                            $qty = $qty1-$qty2;//在线数量
                            $items[] = array(
                                'title' => (string) $variation->VariationTitle,
                                'listing_type' => (string) $item->ListingType == 'Chinese' ? 'auction' : 'buy_now',
                                'currency' => (string) $currency_code,
                                'price' => (string) $variation->StartPrice,
                                'shipping_price' => (string) ($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost),
                                'start_time' => (string) $item->ListingDetails->StartTime,
                                'listing_duration' => (string) $item->ListingDuration,
                                'time_left' => (string) $item->TimeLeft,
                                'qty' => $qty,//在线数量
//                                'sold_qty' => (int) $variation->SellingStatus->QuantitySold,//售出数量
                                'item_id' => (string) $item->ItemID,
                                'ebay_id' => $ebay_id,
                                'image_url' => (string) $item->PictureDetails->GalleryURL,
                                'sku' => (string) $variation->SKU,
                            );
                        }
                    } else {//单属性
                        echo $item->ItemID, " ", $item->ListingType, "\n";
                        $qty1 = (string) $item->Quantity;
                        $qty2 = (string) $item->QuantityAvailable;
                        $sold_qty = $qty1-$qty2;//售出数量
                        $items[] = array(
                            'title' => (string) $item->Title,
                            'listing_type' => (string) $item->ListingType == 'Chinese' ? 'auction' : 'buy_now',
                            'currency' => (string) $currency_code,
                            'price' => (string) $price,
                            'shipping_price' => (string) ($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost),
                            'start_time' => (string) $item->ListingDetails->StartTime,
                            'listing_duration' => (string) $item->ListingDuration,
                            'time_left' => (string) $item->TimeLeft,
                            'qty' => (string) $item->QuantityAvailable,
//                            'sold_qty' => $sold_qty,
                            'item_id' => (string) $item->ItemID,
                            'ebay_id' => $ebay_id,
                            'image_url' => (string) $item->PictureDetails->GalleryURL,
                            'sku' => (string) $item->SKU,
                        );
                    }
                }
                $total_pages = $type->PaginationResult->TotalNumberOfPages;
            }
        }

        return array($total_pages, $items);
    }

    public function get_feedback($ebay_id = NULL)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_ebay_feedback.php') === FALSE)
        {
            exit;
        }

        $configs = $this->config->item('ebay_id');
        if ($ebay_id)
        {
            $ebay_ids = array($ebay_id);
        }
        else
        {
            $ebay_ids = array_values($configs);
        }

        foreach ($ebay_ids as $ebay_id)
        {
            $itemsPerPage = 200;
            $pageIndex = 1;
            
            echo "starting $ebay_id\n";
            do
            {
                list($total_pages, $feedbacks) = $this->_proccess_get_feedback($ebay_id, $itemsPerPage, $pageIndex);

                foreach ($feedbacks as $feedback)
                {
                    echo 'starting:' . date('H:i:s') . "\n";
                    $this->ebay_model->save_ebay_feedback($feedback);  
                    echo 'ending:' . date('H:i:s') . "\n";         
                }
                echo "total page: $total_pages, page index: $pageIndex\n";
                $pageIndex++;
            }
            while($pageIndex <= $total_pages);

            echo "finish $ebay_id\n";
        }
    }

    private function _proccess_get_feedback($ebay_id, $itemsPerPage, $pageIndex)
    {
        $site = 'US';

        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
        $itemInfo = array(
            'site' => $site,
            'itemsPerPage' => $itemsPerPage,
            'pageIndex' => $pageIndex
        );

        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetFeedback';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = get_ebay_feedback($this->appToken, $itemInfo);
        $response = $this->xmlRequest($this->ebaysession, $xml);

        $total_pages = 0;
        $feedbacks = array();
        var_dump($response);
        if (isset($response->Ack) && $response->Ack != 'Success')
        {
            echo "Error\n";
            var_dump($response);
            return array($total_pages, $feedbacks);
        } 

        if (isset($response->FeedbackDetailArray->FeedbackDetail)) {
            $counter = $itemsPerPage;
            foreach ($response->FeedbackDetailArray->FeedbackDetail as $detail)
            {
                $buyer_id = (string)$detail->CommentingUser;
                $item_id = (string)$detail->ItemID;
                $data = array(
                    'feedback_id'       => (string)$detail->FeedbackID,
                    'buyer_id'          => $buyer_id,
                    'feedback_type'     => (string)$detail->CommentType,
                    'feedback_content'  => (string)$detail->CommentText,
                    'feedback_time'     => (string)$detail->CommentTime,
                    'feedback_response' => (string)(isset($detail->FeedbackResponse) ? $detail->FeedbackResponse : ''),
                    'item_id'           => $item_id,
                    'item_title'        => (string)$detail->ItemTitle,
                    'transaction_id'    => (string)$detail->TransactionID,
                    'ebay_id'           => (string)$ebay_id,
                );

                $feedbacks[] = $data;
            }
            $total_pages = $response->PaginationResult->TotalNumberOfPages;
        }

        return array($total_pages, $feedbacks);
    }

    public function load_shipping() {
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-type: text/html; charset=utf-8");
        
        $site = $this->input->post('Site');

        switch ($site) {
            case 'US':
                $this->load->view('html/shipping_us');
                break;
            case 'UK':
                $this->load->view('html/shipping_uk');
                break;
            case 'Australia':
                $this->load->view('html/shipping_au');
                break;
            case 'France':
                $this->load->view('html/shipping_fr');
                break;
        }
    }
    public function load_currency() {
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-type: text/html; charset=utf-8");

        $site = $this->input->post('Site');

        switch ($site) {
            case 'US':
                $currencyOption = <<<OPT
                <option value="USD" selected="selected">USD</option>
OPT;
                break;
            case 'UK':
                $currencyOption = <<<OPT
                <option value="GBP" selected="selected">GBP</option>
OPT;
                break;
            case 'Australia':
                $currencyOption = <<<OPT
                <option value="AUD" selected="selected">AUD</option>
OPT;
                break;
            case 'France':
                $currencyOption = <<<OPT
                <option value="EUR" selected="selected">EUR</option>
OPT;
                break;
        }
        echo $currencyOption;
    }
    public function category_search() {
        $text = $this->input->post('Text');
        $site = $this->input->post('Site');
        
        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetSuggestedCategories';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = search_categories($this->appToken, htmlentities($text));
        $resp = $this->xmlRequest($this->ebaysession, $xml);
        $resp = make_categories_id_name($resp);


        $selected = true;
        $html = <<<HTML
    <select name="CategoryId" id="PrimaryCatagoryID" onchange="get_item_specifics();">
HTML;
        foreach ($resp as $key => $value) {
            if ($selected) {
            $html .= <<<HTML
<option selected="selected" value="$key">$value</option>
HTML;
            $selected = false;
            } else {
            $html .= <<<HTML
<option value="$key">$value</option>
HTML;
            }
        }

        $html .= <<<HTML
</select>
HTML;

        echo $html;
    }

    public function load_store_category() {
        $site = $this->input->post('siteID');
        $ebay_id = $this->input->post('EbayID');
        $refresh = $this->input->post('refresh');

        $store_category_cache_name = $ebay_id . '_store_category_cache';
        $doc_root = getenv('DOCUMENT_ROOT');
        $store_category_cache_path = $doc_root . '/static/cache/' . $store_category_cache_name;
        $store_category_html = @file_get_contents($store_category_cache_path);

        if (!empty($store_category_html) && !$refresh) {
            echo $store_category_html;
            return;
        }


        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetStore';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = store_category($this->appToken);
        $resp = $this->xmlRequest($this->ebaysession, $xml);
        $resp = make_store_category_id_name($resp);
        

        $selected = true;
        $html = <<<HTML
    <select name="StoreCategoryID" id="StoreCategoryID">
        <option value="" selected="selected">-- none --</option>
HTML;
        foreach ($resp as $key => $value) {
            $html .= <<<HTML
<option value="$key">$value</option>
HTML;
        }
        $html .= <<<HTML
</select>
HTML;

        // save as cache!
        $num = file_put_contents($store_category_cache_path, $html);
        
        echo $html;
    }

    public function get_item() {
        $item_id = $this->input->post('item_id');
        $site = $this->input->post('site');
        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetItem';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);
        
        $xml = get_item($this->appToken, $item_id);
        $resp = $this->xmlRequest($this->ebaysession, $xml);

        $data = item_to_array($resp);

        echo json_encode($data);
    }    

    public function add_ebay_item() {
        $requestType = $this->input->post('requestType');
        $Currency = $this->input->post('Currency');
        $ListingType = $this->input->post('ListingType');
        $eBayID = $this->input->post('eBayID');
        $Site = $this->input->post('Site');
        $Title = $this->input->post('Title');
        $description = $this->input->post('itemDescription');
        $description = wrap_description($description);
        $CategoryId = $this->input->post('CategoryId');
        $CustomerLabel = $this->input->post('CustomerLabel');
        $Quantity = $this->input->post('Quantity');
        $StartPrice = $this->input->post('StartPrice');
        $ExternalGalleryImageFile = $this->input->post('ExternalGalleryImageFile');
        $ListingDuration = $this->input->post('ListingDuration');
        $Country = $this->input->post('Country');
        $Location = $this->input->post('Location');
        $PayPalEmailAddress = $this->input->post('PayPalEmailAddress');
        $ShippingType = $this->input->post('ShippingType');
        $ShippingService0 = $this->input->post('ShippingService0');
        $free0 = $this->input->post('free0');
        $ShippingServiceCost0 = $this->input->post('ShippingServiceCost0');
        $ShippingServiceAdditionalCost0 = $this->input->post('ShippingServiceAdditionalCost0');
        $ShippingSurcharge0 = $this->input->post('ShippingSurcharge0');
        $DispatchTimeMax = $this->input->post('DispatchTimeMax');
        $GetItFast = $this->input->post('GetItFast');

        $iShippingService0 = $this->input->post('iShippingService0');
        $ifree0 = $this->input->post('ifree0');
        $iShippingServiceCost0 = $this->input->post('iShippingServiceCost0');
        $iShippingServiceAdditionalCost0 = $this->input->post('iShippingServiceAdditionalCost0');

        $ShippingService1 = $this->input->post('ShippingService1');
        $ShippingServiceCost1 = $this->input->post('ShippingServiceCost1');
        $ShippingServiceAdditionalCost1 = $this->input->post('ShippingServiceAdditionalCost1');
        $ShippingSurcharge1 = $this->input->post('ShippingSurcharge1');

        $ShippingService2 = $this->input->post('ShippingService2');
        $ShippingServiceCost2 = $this->input->post('ShippingServiceCost2');
        $ShippingServiceAdditionalCost2 = $this->input->post('ShippingServiceAdditionalCost2');
        $ShippingSurcharge2 = $this->input->post('ShippingSurcharge2');

        $iShippingService1 = $this->input->post('iShippingService1');
        $iShippingServiceCost1 = $this->input->post('iShippingServiceCost1');
        $iShippingServiceAdditionalCost1 = $this->input->post('iShippingServiceAdditionalCost1');
        $iShippingService2 = $this->input->post('iShippingService2');
        $iShippingServiceCost2 = $this->input->post('iShippingServiceCost2');
        $iShippingServiceAdditionalCost2 = $this->input->post('iShippingServiceAdditionalCost2');

        $ReturnPolicy = $this->input->post('ReturnPolicy');

        $BestOfferEnabled = $this->input->post('BestOfferEnabled');

        $iShipToLocation0 = $this->input->post('iShipToLocation0');
        if (isset($iShipToLocation0[0])) {
            $iShipToLocation0 = $iShipToLocation0[0];
        }

        $storeCategoryID = $this->input->post('StoreCategoryID');

        $itemInfo = array(
            'site' => $Site,
            'currency' => $Currency,
            'description' => $description,
            'title' => $Title,
            'startPrice' => $StartPrice,
            'quantity' => $Quantity,
            'pictureUrl' => $ExternalGalleryImageFile,
            'categoryID' => $CategoryId,
            'customerLabel' => $CustomerLabel,
            'listType' => $ListingType,
            'bestOfferEnabled' => false,
            'listingDuration' => $ListingDuration,
            'country' => $Country,
            'location' => $Location,
            'payPalEmailAddress' => $PayPalEmailAddress,
            'shippingType' => $ShippingType,
            'shippingService0' => $ShippingService0,
            'shippingServiceCost0' => $ShippingServiceCost0,
            'ishippingService0' => $iShippingService0,
            'ishippingServiceCost0' => $iShippingServiceCost0,
            'iShipToLocation0' => $iShipToLocation0,
            'dispatchTimeMax' => $DispatchTimeMax,
            'returnPolicy' => $ReturnPolicy
        );

        $this->ebay_config['siteToUseID'] = get_site_id($Site);

        if (!empty($storeCategoryID)) {
            $itemInfo['storeCategoryID'] = $storeCategoryID;
        }

        if ($free0 == 1) {
            $itemInfo['free0'] = true;
        }
        if (isset($ShippingServiceAdditionalCost0)) {
            $itemInfo['shippingServiceAdditionalCost0'] = $ShippingServiceAdditionalCost0;
        }
        if (isset($ShippingSurcharge0) && $ShippingSurcharge0 > 0) {
            $itemInfo['shippingSurcharge0'] = $ShippingSurcharge0;
        }
        if ($GetItFast == 1) {
            $itemInfo['getItFast'] = $GetItFast;
        }
        if ($ifree0 == 1) {
            $itemInfo['ifree0'] = true;
        }
        if (isset($iShippingServiceAdditionalCost0)) {
            $itemInfo['ishippingServiceAdditionalCost0'] = $iShippingServiceAdditionalCost0;
        }

        if ($ShippingService1 != 'NotSelected') {
            $itemInfo['shippingService1'] = $ShippingService1;
            $itemInfo['shippingServiceCost1'] = $ShippingServiceCost1;
            if (isset($ShippingServiceAdditionalCost1)) {
                $itemInfo['shippingServiceAdditionalCost1'] = $ShippingServiceAdditionalCost1;
            }
            if (isset($ShippingSurcharge1) && $ShippingSurcharge1 > 0) {
                $itemInfo['shippingSurcharge1'] = $ShippingSurcharge1;
            }
        }
        if ($ShippingService2 != 'NotSelected') {
            $itemInfo['shippingService2'] = $ShippingService2;
            $itemInfo['shippingServiceCost2'] = $ShippingServiceCost2;
            if (isset($ShippingServiceAdditionalCost2)) {
                $itemInfo['shippingServiceAdditionalCost2'] = $ShippingServiceAdditionalCost2;
            }
            if (isset($ShippingSurcharge2) && $ShippingSurcharge2 > 0) {
                $itemInfo['shippingSurcharge2'] = $ShippingSurcharge2;
            }
        }
        if ($iShippingService1 != 'InternationalNotSelected') {
            $itemInfo['ishippingService1'] = $iShippingService1;
            $itemInfo['ishippingServiceCost1'] = $this->input->post('iShippingServiceCost1');
            if (isset($iShippingServiceAdditionalCost1)) {
                $itemInfo['ishippingServiceAdditionalCost1'] = $iShippingServiceAdditionalCost1;
            }
            $iShipToLocation1 = $this->input->post('iShipToLocation1');
            if (isset($iShipToLocation1[0])) {
                $itemInfo['ishipToLocation1'] = $iShipToLocation1[0];
            }
        }
        if ($iShippingService2 != 'InternationalNotSelected') {
            $itemInfo['ishippingService2'] = $iShippingService2;
            $itemInfo['ishippingServiceCost2'] = $iShippingServiceCost2;
            if (isset($iShippingServiceAdditionalCost2)) {
                $itemInfo['ishippingServiceAdditionalCost2'] = $iShippingServiceAdditionalCost2;
            }
            $iShipToLocation2 = $this->input->post('iShipToLocation2');
            if (isset($iShipToLocation2[0])) {
                $itemInfo['ishipToLocation2'] = $iShipToLocation2[0];
            }
        }

        if ($ReturnPolicy == 1) {
            $return_within = $this->input->post('return_within');
            $return_refund_as = $this->input->post('return_refund_as');
            $return_actor = $this->input->post('return_actor');
            $return_details = $this->input->post('return_details');
            $itemInfo['return_within'] = $return_within;
            $itemInfo['return_refund_as'] = $return_refund_as;
            $itemInfo['return_actor'] = $return_actor;
            if (!empty($return_details)) {
                $itemInfo['return_details'] = $return_details;
            }
        }

        $usescheduler = $this->input->post('usescheduler');
        if ($usescheduler == 1) {
            $schedule_hour = $this->input->post('schedule_hour');
            $schedule_minute = $this->input->post('schedule_minute');
            $auctionDate = $this->input->post('auctionDate');

            $schedule_date = date('d', strtotime($auctionDate));
            $schedule_month = date('m', strtotime($auctionDate));
            $schedule_year = date('Y', strtotime($auctionDate));

            $itemInfo['schedule_time'] = get_gmt(array(
                'year'      => $schedule_year,
                'month'     => $schedule_month,
                'date'      => $schedule_date,
                'hour'      => $schedule_hour,
                'minute'    => $schedule_minute,
            ));
        }

        $itemSpecificsJSON = $this->input->post('itemSpecifics');
        $itemSpecifics = json_decode($itemSpecificsJSON, true);        
        if (count($itemSpecifics)) {
            $itemInfo['itemSpecifics'] = $itemSpecifics;
        }
        if ($ListingType == 'FixedPriceItem') {
            if ($BestOfferEnabled) {
                $itemInfo['bestOfferEnabled'] = true;
                $MinimumBestOfferPrice = $this->input->post('MinimumBestOfferPrice');
                if (isset($MinimumBestOfferPrice) && $MinimumBestOfferPrice > 0) {
                    $itemInfo['minimumBestOfferPrice'] = $MinimumBestOfferPrice;
                }
                $BestOfferAutoAcceptPrice = $this->input->post('BestOfferAutoAcceptPrice');
                if (isset($BestOfferAutoAcceptPrice) && $BestOfferAutoAcceptPrice > 0) {
                    $itemInfo['bestOfferAutoAcceptPrice'] = $BestOfferAutoAcceptPrice;
                }
            } 

            $this->ebay_config['callName'] = $requestType;
            $this->load->library('ebayapi/EbaySession', $this->ebay_config);
            
            if ($requestType == 'AddItem') {
                $requestType = $requestType . 'Request';
                $xml = add_item($this->appToken, $itemInfo, $requestType);

                $resp = $this->xmlRequest($this->ebaysession, $xml);

                if ($resp->Ack != 'Failure') {
                    echo 'Success';
                } else {
                    $err_num = count($resp->Errors);
                    echo $resp->Errors[$err_num - 1]->LongMessage;
                }
            } else if ($requestType == 'VerifyAddItem') {
                $requestType = $requestType . 'Request';
                $xml = add_item($this->appToken, $itemInfo, $requestType);

                $resp = $this->xmlRequest($this->ebaysession, $xml);
                
                if ($resp->Ack == 'Success' || $resp->Ack == 'Warning') {
                    $toReturn['Ack'] = 'Success';
                    $toReturn['Fees'] = countFees($resp);
                } else {
                    $toReturn['Ack'] = 'Failure';
                    $err_num = count($resp->Errors);
                    $toReturn['Message'] = sprintf('%s', $resp->Errors[$err_num - 1]->LongMessage);
                }
                echo json_encode($toReturn);
            }
        } else if ($ListingType == 'Chinese') {
            $ReservePrice = $this->input->post('ReservePrice');
            if (isset($ReservePrice) && $ReservePrice > 0) {
                $itemInfo['reservePrice'] = $ReservePrice;
            }
            $BuyItNowPrice = $this->input->post('BuyItNowPrice');
            if (isset($BuyItNowPrice) && $BuyItNowPrice > 0) {
                $itemInfo['buyItNowPrice'] = $BuyItNowPrice;
            }

            $this->ebay_config['callName'] = $requestType;
            $this->load->library('ebayapi/EbaySession', $this->ebay_config);
            if ($requestType == 'AddItem') {
                $requestType = $requestType . 'Request';
                $xml = add_item($this->appToken, $itemInfo, $requestType);
                $resp = $this->xmlRequest($this->ebaysession, $xml);

                if ($resp->Ack != 'Failure') {
                    echo 'Success';
                } else {
                    $err_num = count($resp->Errors);
                    echo $resp->Errors[$err_num - 1]->LongMessage;
                }
            } else if ($requestType == 'VerifyAddItem') {
                $requestType = $requestType . 'Request';
                $xml = add_item($this->appToken, $itemInfo, $requestType);
                $resp = $this->xmlRequest($this->ebaysession, $xml);

                if ($resp->Ack == 'Success' || $resp->Ack == 'Warning') {
                    $toReturn['Ack'] = 'Success';
                    $toReturn['Fees'] = countFees($resp);
                } else {
                    $toReturn['Ack'] = 'Failure';
                    $err_num = count($resp->Errors);
                    $toReturn['Message'] = sprintf('%s', $resp->Errors[$err_num - 1]->LongMessage);
                }
                echo json_encode($toReturn);
            }
        } else if ($ListingType == 'MultiSKU') {
            $variations = get_variations($_POST);
            if (count($variations) > 0) {
                $itemInfo['variations'] = $variations;
            }

            if ($requestType == 'AddItem') {
                $this->ebay_config['callName'] = 'AddFixedPriceItem';
                $this->load->library('ebayapi/EbaySession', $this->ebay_config);
                $requestType = 'AddFixedPriceItemRequest';
                $xml = add_multi_sku_item($this->appToken, $itemInfo, $requestType);
                $resp = $this->xmlRequest($this->ebaysession, $xml);

                if ($resp->Ack != 'Failure') {
                    echo 'Success';
                } else {
                    $err_num = count($resp->Errors);
                    echo $resp->Errors[$err_num - 1]->LongMessage;
                }
            } else if ($requestType == 'VerifyAddItem') {
                $this->ebay_config['callName'] = 'VerifyAddFixedPriceItem';
                $this->load->library('ebayapi/EbaySession', $this->ebay_config);
                $requestType = 'VerifyAddFixedPriceItemRequest';
                $xml = add_multi_sku_item($this->appToken, $itemInfo, $requestType);
                $resp = $this->xmlRequest($this->ebaysession, $xml);
                if ($resp->Ack == 'Success' || $resp->Ack == 'Warning') {
                    $toReturn['Ack'] = 'Success';
                    $toReturn['Fees'] = countFees($resp);
                } else {
                    $toReturn['Ack'] = 'Failure';
                    $err_num = count($resp->Errors);
                    $toReturn['Message'] = sprintf('%s', $resp->Errors[$err_num - 1]->LongMessage);
                }
                echo json_encode($toReturn);
            }
        }        
    }

    protected function xmlRequest($session, $xml) {
        $respondXML = $session->sendHttpRequest($xml);

        $resp = simplexml_load_string($respondXML, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        return $resp;
    }

    public function preview_description() {
        $ebayId = $this->input->post('EbayID');
        $itemDescription = $this->input->post('itemDescription');
        $itemDescription = wrap_description($itemDescription);
        
        if (isset($ebayId)) {
            $doc_root = getenv('DOCUMENT_ROOT');
            $num = file_put_contents($doc_root . '/static/html/' . $ebayId . '.html', $itemDescription);
            if ($num == 0) {
                echo '';
            }
            echo 'Done';
        }
    }
    
    public function get_item_specifics() {
        $category_id = $this->input->post('category_id');
        $site = $this->input->post('siteID');

        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetCategorySpecifics';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);
        
        $xml = get_item_specifics($this->appToken, $category_id);
        $resp = $this->xmlRequest($this->ebaysession, $xml);

        $item_specifics = make_item_specifics_name_value($resp);
        if (count($item_specifics) == 0) {
            return ;
        }
        $html = <<<HTML
    <table cellspacing="0" cellpadding="0" border="0" class="labelData" style="">
		<col class="col1">
		<col class="col2">
		<col class="col3">
		<tbody><tr>
		  <th valign="top">Item Specifics:</th>
			<td>
                <table width="100%" border="0">
                    <tbody>
HTML;
        $i = 1;
        foreach ($item_specifics as $key => $values) {
            $html .= <<<HTML
                        <tr>
                            <td>
                                <input type='hidden' value='$key' id="ItemSpecificKey$i"/>
                                <b>$key:</b><br>
                                <select name="ItemSpecificValue$i" onchange='set_item_specific($("ItemSpecificKey$i").value, this.value)'>
                                <option value="" selected="selected">--</option>
HTML;
            foreach ($values as $value) {
                $html .= <<<HTML
                                    <option value="$value">$value</option>
HTML;
            }
            $html .= <<<HTML
                                </select>
                            </td>
                        </tr>
HTML;
            $i++;
        }
        $html .= <<<HTML
                    </tbody>
                </table>
            </td>
		  <td class="instructions">Recommended to use for better search results</td>
		</tr>
	</tbody></table>
HTML;
        echo $html;
    }
    
    public function get_paypal_account() {
        $ebay_id = $this->input->post('EbayID');
        $paypalAcount = $this->config->item('paypalAcount');

        echo $paypalAcount[$ebay_id];
    }

    public function get_item_transactions()
    {
        set_time_limit(0);
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_item_transactions.php') === FALSE)
        {
            exit;
        }
        
        $all_orders = $this->epacket_model->get_all_orders_for_epacket();

        $count = count($all_orders);
        foreach ($all_orders as $order) {
            echo $order->id, " ", $count--, ' left', "\n";
            
            $this->_process_get_item_transaction($order->id);
        }

        echo 'Done!';
    }
    
    public function get_item_transaction($order_id)
    {
        set_time_limit(60*5);
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_item_transaction.php') === FALSE)
        {
            exit;
        }        
        $this->_process_get_item_transaction($order_id);        
    }

    private function _process_get_item_transaction($order_id)
    {
        $ebay_ids = $this->config->item('ebay_id');
        $order = $this->order_model->get_order($order_id);
        if (empty($order))
        {
            return;
        }
        $paypal_transaction_id = $order->transaction_id;
        $ebay_id = $ebay_ids[$order->to_email];
        $buy_id = $order->buyer_id;
        $name = $order->name;
        $item_titles = explode_item_title($order->item_title_str);
        $item_ids = explode(',',  trim($order->item_id_str, ','));
        $item_id = $item_ids[0];
        $paid_time = $order->paid_time;

        $i = 0;
        foreach ($item_titles as $item_title)
        {
            if (strrpos($item_title, ']') == (strlen($item_title) - 1))
            {
                $item_title = rtrim(rtrim($item_title, ']')) . ']';
            }
            if (empty($item_ids[$i]))
            {
                continue;
            }
            $item_id = $item_ids[$i];
            if (! $this->epacket_model->ebay_transaction_id_exists($item_id, $item_title, $paypal_transaction_id)) {
                $ebay_transaction_id = $this->epacket_model->fetch_transaction_id_from_poll(
                    $item_id, 
                    $item_title,
                    $buy_id, 
                    $name, 
                    $paid_time,
                    $paypal_transaction_id
                );
                if ( ! empty($ebay_transaction_id))
                {
                    $this->epacket_model->save_paypal_ebay_transacstion_id($item_id, $item_title, $paypal_transaction_id, $ebay_transaction_id, $ebay_id);
                    $this->epacket_model->set_transaction_poll_used($item_id, $ebay_transaction_id, $paypal_transaction_id);
                }
                else
                {
                    $this->_process_get_item_transactions($item_id, $item_title, $ebay_id, $paypal_transaction_id, $buy_id, $name);
                    $ebay_transaction_id = $this->epacket_model->fetch_transaction_id_from_poll(
                        $item_id,
                        $item_title, 
                        $buy_id, 
                        $name, 
                        $paid_time,
                        $paypal_transaction_id
                    );
                    if ( ! empty($ebay_transaction_id))
                    {
                        $this->epacket_model->save_paypal_ebay_transacstion_id($item_id, $item_title, $paypal_transaction_id, $ebay_transaction_id, $ebay_id);
                        $this->epacket_model->set_transaction_poll_used($item_id, $ebay_transaction_id, $paypal_transaction_id);
                    }
                }
            }
            $i++;
        }

        // get trade fees at the same time;
        echo "starting calling item trade fees\n";
        //$this->get_item_trade_fees($order_id);
    }

    private function _process_get_item_transactions($item_id, $item_title, $ebay_id, $paypal_transaction_id, $buy_id, $name) {
        $app_tokens = $this->config->item('appToken');
        $app_token = $app_tokens[$ebay_id];
        $itemsPerPage = 200;
        $pageIndex = 1;

        $this->ebay_config['siteToUseID'] = 0;
        $this->ebay_config['callName'] = 'GetItemTransactions';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $has_more = true;
        while ($has_more) {
            $itemInfo = array(
                'itemsPerPage' => $itemsPerPage,
                'pageIndex'    => $pageIndex,
                'itemId'       => $item_id
            );
            echo "$item_id: starting calling ebay api\n";
            $xml = get_item_transactions($app_token, $itemInfo);
            $resp = $this->xmlRequest($this->ebaysession, $xml);
            
            if (! isset($resp->HasMoreTransactions) || $resp->HasMoreTransactions == 'false') {
                $has_more = false;
            }

            if ($resp->Ack == 'Failure') {
                return false;
            }

            // Chinese auction
            if ($resp->Item->ListingType == 'Chinese') {
                $this->epacket_model->save_paypal_ebay_transacstion_id($item_id, $item_title, $paypal_transaction_id, 0, $ebay_id);
                return true;
            }
            if (!isset($resp->TransactionArray)) {
                break;
            }
            $transactionArray = $resp->TransactionArray;
            $tried_amounts = array();
            $trade_fee = 0;
            foreach ($transactionArray->Transaction as $transaction) {
                $transaction_id = $transaction->TransactionID;
                $user_id = $transaction->Buyer->UserID;
                $ship_to_name = $transaction->Buyer->BuyerInfo->ShippingAddress->Name;
                $gross = $transaction->AmountPaid;
                $paid_time = $transaction->PaidTime;

                if ($user_id == $buy_id)
                {
                    $data = array(
                        'item_id'               => $item_id,
                        'variation_title'       => "$item_title",
                        'buyer_id'              => "$user_id",
                        'name'                  => "$ship_to_name",
                        'ebay_transaction_id'   => "$transaction_id",
                        'gross'                 => "$gross",
                        'paid_time'             => "$paid_time",
                    );

                    if (isset($transaction->Variation->VariationTitle))
                    {
                        $variation_title = (string)$transaction->Variation->VariationTitle;
                        $data['variation_title'] = "$variation_title";
                    }

                    // save to poll
                    $this->epacket_model->save_transaction_to_poll($data);
                    unset($data);
                }
                
                echo $transaction_id, "\n";

                $this->__update_item_trade_fee($tried_amounts, $trade_fee, $transaction, $item_id, $buy_id);
            }
            $pageIndex++;
        }
    }

    public function get_item_trade_fees($order_id = NULL)
    {
        $ebay_ids = $this->config->item('ebay_id');
        $emails = array_keys($ebay_ids);
        if ($order_id)
        {
            $all_orders = array($order_id);
        }
        else
        {
            $all_orders = $this->sale_model->get_all_ebay_order_ids_for_profit($emails);
        }
        
        foreach ($all_orders as $row) {
            $order_id = isset($row->id) ? $row->id : $row; 

            $order = $this->sale_model->get_ebay_order_for_profit($order_id);
            $auction = FALSE;

            
            echo $order_id, "\n";
            
            if (empty($order))
            {
                continue;
            }
            $paypal_transaction_id = $order->transaction_id;
            $ebay_id = $ebay_ids[$order->to_email];
            $buyer_id = $order->buyer_id;
            $gross = $order->gross;
            $item_ids = explode(',',  $order->item_id_str);
            $item_id_count = count($item_ids);

            // for order with single item id
            if ($item_id_count == 1)
            {
                /* check if the order has been calculated or not */
                if ($order->trade_fee > 0)
                {
                    if ($auction)
                    {
                        echo 'auction', "\n";
                        $this->script->get_auction_listing_fee(array('order_id' => $order_id));
                    }
                    calculate_order_profit_rate($order_id, FALSE);
                    
                    continue;
                }
                $item_id = $order->item_id_str;
                if (empty($item_id))
                {
                    continue;
                }
                /* check if the order of the same buyer has been calculated or not */
                $trade_fee = $this->sale_model->get_item_trade_fee($item_id);
                
                list($trade_fee, $listing_type) = $this->_process_get_item_trade_fees($item_id, $ebay_id, $buyer_id, $gross);
                if ($listing_type == 'Chinese')
                {
                    $auction = TRUE;
                }
            }
            else
            {
                $all_trade_fee = 0;
                foreach ($item_ids as $item_id)
                {
                    if (empty($item_id))
                    {
                        continue;
                    }

                    echo 'more: ' . $item_id, "\n";
                    
                    /* check if the order of the same buyer has been calculated or not */
                    $trade_fee = $this->sale_model->get_buyer_trade_fee($buyer_id, $item_id);
                    
                    if (empty($trade_fee))
                    {
                        list($trade_fee, $listing_type) = $this->_process_get_item_trade_fees($item_id, $ebay_id, $buyer_id, $gross);

                        if ($listing_type == 'Chinese')
                        {
                            $auction = TRUE;
                        }
                    }
                    $all_trade_fee += $trade_fee;
                }
                
                $this->sale_model->update_order_trade_fee_by_id($order_id, $all_trade_fee);
            }
            if ($auction)
            {
                echo 'auction', "\n";
                $this->script->get_auction_listing_fee(array('order_id' => $order_id));
            }

            echo "starting calculate_order_profit_rate\n";
            calculate_order_profit_rate($order_id, FALSE);
        }

        echo 'Done!';
    }

    private function _process_get_item_trade_fees($item_id, $ebay_id, $buyer_id, $gross) {
        $app_tokens = $this->config->item('appToken');
        $app_token = $app_tokens[$ebay_id];
        $itemsPerPage = 200;
        $pageIndex = 1;

        $this->ebay_config['siteToUseID'] = 0;
        $this->ebay_config['callName'] = 'GetItemTransactions';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $has_more = true;
        $trade_fee = 0;
        $tried_amounts = array();
        $listing_type = '';
        
        while ($has_more) {
            $itemInfo = array(
                'itemsPerPage' => $itemsPerPage,
                'pageIndex'    => $pageIndex,
                'itemId'       => $item_id
            );

            $xml = get_item_transactions($app_token, $itemInfo);
            $resp = $this->xmlRequest($this->ebaysession, $xml);
            
            if (! isset($resp->HasMoreTransactions) || $resp->HasMoreTransactions == 'false') {
                $has_more = false;
            }

            if ($resp->Ack == 'Failure') {
                return false;
            }
            if ($resp->Item->ListingType == 'Chinese') {
                $listing_type = 'Chinese';
            }

            if (!isset($resp->TransactionArray)) {
                break;
            }
            $transactionArray = $resp->TransactionArray;

            foreach ($transactionArray->Transaction as $transaction) {
                $this->__update_item_trade_fee($tried_amounts, $trade_fee, $transaction, $item_id, $buyer_id);
            }
            $pageIndex++;
        }

        return array($trade_fee, $listing_type);
    }

    private function __update_item_trade_fee(&$tried_amounts, &$trade_fee, $transaction, $item_id, $buyer_id)
    {
        $user_id = (string)$transaction->Buyer->UserID;
        $amount_paid = price((string)$transaction->AmountPaid);
        $final_vale_fee = (string)$transaction->FinalValueFee;
        $phone = (string)$transaction->Buyer->BuyerInfo->ShippingAddress->Phone;

        if ($buyer_id == $user_id)
        {
            echo $final_vale_fee . "\n";
            $trade_fee = $final_vale_fee;
         
            // update phone info
            if ( ! empty($phone) && $phone != 'Invalid Request')
            {
                echo $phone . "\n";
                $this->sale_model->update_order_phone($user_id, $item_id, $amount_paid, $phone);
            }
            // skipping paid amount that has been processed.
            if (in_array($amount_paid, $tried_amounts))
            {
                return;
            }

            echo "starting update order trade fee\n";
            $this->sale_model->update_order_trade_fee($item_id, $amount_paid, $final_vale_fee);
            echo "ending....\n";
            $tried_amounts[] = $amount_paid;
        }

        if (empty($final_vale_fee))
        {
            return;
        }
    }

    public function get_auction_listing_fee($order_id)
    {
        $ebay_ids = $this->config->item('ebay_id');
        $emails = array_keys($ebay_ids);

        $order = $this->sale_model->get_ebay_order_for_profit($order_id);
        if (empty($order))
        {
            continue;
        }
        $ebay_id = $ebay_ids[$order->to_email];
        $buyer_id = $order->buyer_id;
        $item_ids = explode(',',  $order->item_id_str);
        $listing_fee = $order->listing_fee;
        if ($order->listing_fee > 0)
        {
            return;
        }

        $item_id = $order->item_id_str;

        $total_listing_fee = 0;
        foreach ($item_ids as $item_id)
        {
            /* check if the order of the item id has been calculated or not */
            $listing_fee = $this->sale_model->get_existing_listing_fee($item_id);
            if (empty($listing_fee))
            {
                $listing_fee = $this->_process_get_auction_listing_fee($item_id, $ebay_id);
            }
            $total_listing_fee += $listing_fee;
        }

        echo 'listing fee: ' . $total_listing_fee, "\n";
        $this->sale_model->update_order_listing_fee_by_id($order_id, $total_listing_fee);
        
        calculate_order_profit_rate($order_id, FALSE);

        echo 'Done!';
    }

    private function _process_get_auction_listing_fee($item_id, $ebay_id) {
        $app_tokens = $this->config->item('appToken');
        $app_token = $app_tokens[$ebay_id];
        $itemsPerPage = 200;
        $pageIndex = 1;

        $this->ebay_config['siteToUseID'] = 0;
        $this->ebay_config['callName'] = 'GetAccount';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $has_more = true;

        while ($has_more) {
            $itemInfo = array(
                'itemsPerPage' => $itemsPerPage,
                'pageIndex'    => $pageIndex,
                'itemId'       => $item_id
            );

            $xml = get_account($app_token, $itemInfo);
            $resp = $this->xmlRequest($this->ebaysession, $xml);

            if (! isset($resp->HasMoreTransactions) || $resp->HasMoreTransactions == 'false') {
                $has_more = false;
            }

            if ($resp->Ack == 'Failure') {
                return false;
            }

            if ( ! isset($resp->AccountEntries))
            {
                break;
            }
            $accounts = $resp->AccountEntries->AccountEntry;
            foreach ($accounts as $account)
            {
                // check the listing fee.
                if ($account->AccountDetailsEntryType == 'FeeInsertion')
                {
                    $listing_fee = (string)$account->GrossDetailAmount;
                    return $listing_fee;
                }
            }

            $pageIndex++;
        }

        break;
    }

	//add  by mansea
	public function get_ebay_message($ebay_id = NULL)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'get_ebay_message.php') === FALSE)
        {
            exit;
        }

        $configs = $this->config->item('ebay_id');
        if ($ebay_id)
        {
            $ebay_ids = array($ebay_id);
        }
        else
        {
            $ebay_ids = array_values($configs);
        }

        foreach ($ebay_ids as $ebay_id)
        {
            $itemsPerPage = 200;
            $pageIndex = 1;
			$begin_time = $this->ebay_model->get_message_begin_time($ebay_id);
			//$end_time = get_current_utc_time();
			$end_time=date('Y-m-d\TH:i:s\Z',mktime(substr($begin_time,11,2)+2,substr($begin_time,14,2),substr($begin_time,17,2),substr($begin_time,5,2),substr($begin_time,8,2),substr($begin_time,0,4)));
			
			$begin_time=date('Y-m-d\TH:i:s\Z',mktime(substr($end_time,11,2)-24,substr($end_time,14,2),substr($end_time,17,2),substr($end_time,5,2),substr($end_time,8,2),substr($end_time,0,4)));
			
			$startdate=strtotime($end_time);
			$enddate=strtotime(get_current_utc_time());
			if($enddate-$startdate<=0){
				$end_time = get_current_utc_time();
			}
			if (empty($begin_time))
			{
				return;
			}
            
            echo "starting $ebay_id\n";
			echo 'start:' . $begin_time . "\n";
			echo 'end:' . $end_time . "\n";
            do
            {
                list($total_pages, $messages) = $this->_proccess_get_message($ebay_id, $itemsPerPage, $pageIndex, $begin_time, $end_time);

                foreach ($messages as $message)
                {
                    //echo 'starting:' . date('H:i:s') . "\n";
                    //$this->ebay_model->save_ebay_message($message);
                    //echo 'ending:' . date('H:i:s') . "\n";
                }
                echo "total page: $total_pages, page index: $pageIndex\n";
                $pageIndex++;
            }
            while($pageIndex <= $total_pages);
			$this->ebay_model->update_message_begin_time($ebay_id,$end_time); 

            echo "finish $ebay_id\n";
        }
    }
	private function _proccess_get_message($ebay_id, $itemsPerPage, $pageIndex, $begin_time, $end_time)
    {
		$site = 'US';
        
        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
        $itemInfo = array(
            'itemsPerPage' => $itemsPerPage,
            'pageIndex' => $pageIndex,
			'begin_time'    => $begin_time,
            'end_time'      => $end_time,
			
        );

        $this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'GetMemberMessages';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);

        $xml = get_ebay_message($this->appToken, $itemInfo);
        $response = $this->xmlRequest($this->ebaysession, $xml);

        $total_pages = 0;
        $messages = array();
        var_dump($response);
        if (isset($response->Ack) && $response->Ack != 'Success' && $response->Ack != 'Warning')
        {
            echo "Error\n";
            var_dump($response);
            return array($total_pages, $messages);
        } 
        if (isset($response->MemberMessage->MemberMessageExchange)) {
            $counter = $itemsPerPage;
            foreach ($response->MemberMessage->MemberMessageExchange as $detail)
            {
                $buyer_id = (string)$detail->Question->SenderID;
                $item_id = (string)$detail->Item->ItemID;
				$body=str_replace("'","\'",$detail->Question->Body);
				$body=str_replace('"','\"',$body);
                $data = array(
					'message_id'       =>$detail->Question->MessageID,
					'message_type'       =>(string)$detail->Question->MessageType,
					'question_type'       =>(string)$detail->Question->QuestionType,
					'recipientid'       =>(string)$detail->Question->RecipientID,
					'sendmail'       =>(string)$detail->Question->SenderEmail,
					'sendid'       =>(string)$detail->Question->SenderID,
					'subject'       =>(string)$detail->Question->Subject,
					'body'       =>$body,
					'itemid'       =>$item_id,
					'itemurl'       =>(string)$detail->Item->ListingDetails->ViewItemURL,
					'starttime'       =>(string)$detail->Item->ListingDetails->StartTime,
					'endtime'       =>(string)$detail->Item->ListingDetails->EndTime,
					'currentprice'       =>(string)$detail->Item->SellingStatus->CurrentPrice,
					'title'       =>(string)$detail->Item->Title,
					'status'       =>0,
					'createtime'       =>(string)$detail->CreationDate,
					'ebay_user'       =>(string)$ebay_id,
					'classid'       =>'',
					'add_time'       =>date('Y-m-d H:i:s'),
					'replaycontent'       =>'',
					'replyuser'       =>'',
					'ebay_id'           => (string)$ebay_id,
                    
                );

                $messages[] = $data;
				$this->ebay_model->save_ebay_message($data);
            }
            $total_pages = $response->PaginationResult->TotalNumberOfPages;
        }

        return array($total_pages, $messages);
    }
	
	public function add_ebay_message($ebay_id = NULL)
	{
		if(strpos($_SERVER['SCRIPT_FILENAME'], 'add_ebay_message.php') === FALSE)
        {
            exit;
        }
		$waitting_add_ebay_messages=$this->ebay_model->fetch_waitting_add_ebay_message($ebay_id);
		foreach($waitting_add_ebay_messages as $waitting_add_ebay_message)
		{
			/*if($waitting_add_ebay_message->message_id!='362312015019')
			{
				continue;
			}*/
			$itemInfo = array(
							  'itemid' => $waitting_add_ebay_message->itemid,
							  'content' => $waitting_add_ebay_message->replaycontent,
							  'message_id'    => $waitting_add_ebay_message->message_id,
							  'sendid'      => $waitting_add_ebay_message->sendid,
							  );
			if($waitting_add_ebay_message->replaycontent!=''){
				$this->_proccess_add_message($ebay_id, $itemInfo,$waitting_add_ebay_message->id);
			}
			//die('stop');
			
		}
			
	}
	private function _proccess_add_message($ebay_id, $itemInfo,$message_id){
		$site = 'US';
        
        if ($this->config->item('production'))
        {
            $this->appToken = $this->config->item('appToken');
            $this->appToken = $this->appToken[$ebay_id];
        }
		$this->ebay_config['siteToUseID'] = get_site_id($site);
        $this->ebay_config['callName'] = 'AddMemberMessageRTQ';
        $this->load->library('ebayapi/EbaySession', $this->ebay_config);
		$xml = add_ebay_message($this->appToken, $itemInfo);
		var_dump($xml);
        $response = $this->xmlRequest($this->ebaysession, $xml);
		var_dump($response);
		if (isset($response->Ack) && $response->Ack=='Success')
        {
			//var_dump($response);
			$staus=array('status'=>5,'update_ebay'=>1);
            $this->ebay_model->update_ebay_message_by_id($message_id, $staus);
        }
	}
}

/* End of file ebay.php */
/* Location: ./system/application/controllers/ebay.php */
