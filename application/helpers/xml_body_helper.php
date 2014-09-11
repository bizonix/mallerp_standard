<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function get_item($appToken, $itemID) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
<RequesterCredentials>
<eBayAuthToken>$appToken</eBayAuthToken>
</RequesterCredentials>
<ItemID>$itemID</ItemID>
<DetailLevel>ReturnAll</DetailLevel>
<IncludeItemSpecifics>true</IncludeItemSpecifics>
</GetItemRequest>
XML;

        return $requestXMLBody;
    }

    function search_categories($appToken, $keyword) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
<RequesterCredentials>
<eBayAuthToken>$appToken</eBayAuthToken>
</RequesterCredentials>
<Query>$keyword</Query>
</GetSuggestedCategoriesRequest>
XML;
        return $requestXMLBody;
    }

    function get_myebay_list($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];
        $itemType = $itemInfo['itemType'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <Version>683</Version>
XML;
        switch ($itemType) {
            case 'ActiveAuctionList':
                $requestXMLBody .= <<<XML
  <ActiveList>
    <ListingType>Auction</ListingType>
    <Sort>StartTimeDescending</Sort>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </ActiveList>
XML;
                break;
            case 'ActiveFixedPriceList':
                $requestXMLBody .= <<<XML
  <ActiveList>
    <ListingType>FixedPriceItem</ListingType>
    <Sort>StartTimeDescending</Sort>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </ActiveList>
XML;
                break;
            case 'BidList':
                $requestXMLBody .= <<<XML
  <BidList>
    <Sort>ItemIDDescending</Sort>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </BidList>
XML;
                break;
            case 'ScheduledList':
                $requestXMLBody .= <<<XML
  <ScheduledList>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </ScheduledList>
XML;
                break;
            case 'SoldList':
                $requestXMLBody .= <<<XML
  <SoldList>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </SoldList>
XML;
                break;
            case 'UnsoldList':
                $requestXMLBody .= <<<XML
  <UnsoldList>
    <Pagination>
      <EntriesPerPage>$itemsPerPage</EntriesPerPage>
      <PageNumber>$pageIndex</PageNumber>
    </Pagination>
  </UnsoldList>
XML;
                break;
        }
        $requestXMLBody .= <<<XML
</GetMyeBaySellingRequest>
XML;

        return $requestXMLBody;
    }

    function get_ebay_feedback($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetFeedback xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
  <Version>683</Version>
  <FeedbackType>FeedbackReceivedAsSeller</FeedbackType>
  <Pagination>
    <EntriesPerPage>$itemsPerPage</EntriesPerPage>
    <PageNumber>$pageIndex</PageNumber>
  </Pagination>
</GetFeedback>
XML;

        return $requestXMLBody;
    }

    function get_ebay_orders($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];
        $begin_time = $itemInfo['begin_time'];
        $end_time = $itemInfo['end_time'];
        $create_time_from = get_utc_time('-30 days');

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetOrders xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
  <Version>719</Version>
  <IncludeFinalValueFee>true</IncludeFinalValueFee>
  <OrderRole>Seller</OrderRole>
  <ModTimeFrom>$begin_time</ModTimeFrom>
  <ModTimeTo>$end_time</ModTimeTo>
  <Pagination>
    <EntriesPerPage>$itemsPerPage</EntriesPerPage>
    <PageNumber>$pageIndex</PageNumber>
  </Pagination>
</GetOrders>
XML;

        return $requestXMLBody;
    }

    function get_seller_transactions($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];
        $begin_time = $itemInfo['begin_time'];
        $end_time = $itemInfo['end_time'];
        $create_time_from = get_utc_time('-30 days');

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetSellerTransactions xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
  <Version>741</Version>
  <IncludeFinalValueFee>true</IncludeFinalValueFee>
  <IncludeContainingOrder>true</IncludeContainingOrder>
  <ModTimeFrom>$begin_time</ModTimeFrom>
  <ModTimeTo>$end_time</ModTimeTo>
  <Pagination>
    <EntriesPerPage>$itemsPerPage</EntriesPerPage>
    <PageNumber>$pageIndex</PageNumber>
  </Pagination>
</GetSellerTransactions>
XML;

        return $requestXMLBody;
    }

    function store_category($appToken) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <CategoryStructureOnly>true</CategoryStructureOnly>
</GetStoreRequest>
XML;

        return $requestXMLBody;
    }


    function add_item($appToken, $itemInfo, $requestType) {
        $currency = $itemInfo['currency'];
        $site = $itemInfo['site'];
        $title = $itemInfo['title'];
        $description = htmlspecialchars($itemInfo['description'], ENT_QUOTES, 'UTF-8');
        $startPrice = $itemInfo['startPrice'];
        $categoryID = $itemInfo['categoryID'];
        $customerLabel = $itemInfo['customerLabel'];
        if (isset($itemInfo['storeCategoryID'])) {
            $storeCategoryID = $itemInfo['storeCategoryID'];
        }
        $payPalEmailAddress = $itemInfo['payPalEmailAddress'];
        $shippingType = $itemInfo['shippingType'];
        $shippingService0 = $itemInfo['shippingService0'];
        if (isset($itemInfo['free0'])) {
            $free0 = $itemInfo['free0'];
        }
        $shippingServiceCost0 = $itemInfo['shippingServiceCost0'];
        $shippingServiceAdditionalCost0 = $itemInfo['shippingServiceAdditionalCost0'];
        if (isset($itemInfo['shippingSurcharge0'])) {
            $shippingSurcharge0 = $itemInfo['shippingSurcharge0'];
        }
        $dispatchTimeMax = $itemInfo['dispatchTimeMax'];
        if (isset($itemInfo['getItFast'])) {
            $getItFast = $itemInfo['getItFast'];
        }
        
        if (isset($itemInfo['shippingService1'])) {
            $shippingService1 = $itemInfo['shippingService1'] ;
            $shippingServiceCost1 = $itemInfo['shippingServiceCost1'];
            $shippingServiceAdditionalCost1 = $itemInfo['shippingServiceAdditionalCost1'];
            if (isset($itemInfo['shippingSurcharge1'])) {
                $shippingSurcharge1 = $itemInfo['shippingSurcharge1'];
            }
        }
        
        if (isset($itemInfo['shippingService2'])) {
            $shippingService2 = $itemInfo['shippingService2'];
            $shippingServiceCost2 = $itemInfo['shippingServiceCost2'];
            $shippingServiceAdditionalCost2 = $itemInfo['shippingServiceAdditionalCost2'];
            if (isset($itemInfo['shippingSurcharge2'])) {
                $shippingSurcharge2 = $itemInfo['shippingSurcharge2'];
            }
        }

        $ishippingService0 = $itemInfo['ishippingService0'];
        if (isset($itemInfo['ifree0'])) {
            $ifree0 = $itemInfo['ifree0'];
        }
        $ishippingServiceCost0 = $itemInfo['ishippingServiceCost0'];
        $ishippingServiceAdditionalCost0 = $itemInfo['ishippingServiceAdditionalCost0'];
        $iShipToLocation0 = $itemInfo['iShipToLocation0'];

        
        if (isset($itemInfo['ishippingService1'])) {
            $ishippingService1 = $itemInfo['ishippingService1'];
            $ishippingServiceCost1 = $itemInfo['ishippingServiceCost1'];
            $ishippingServiceAdditionalCost1 = $itemInfo['ishippingServiceAdditionalCost1'];
            $ishipToLocation1 = $itemInfo['ishipToLocation1'];
        }
        
        if (isset($itemInfo['ishippingService2'])) {
            $ishippingService2 = $itemInfo['ishippingService2'];
            $ishippingServiceCost2 = $itemInfo['ishippingServiceCost2'];
            $ishippingServiceAdditionalCost2 = $itemInfo['ishippingServiceAdditionalCost2'];
            $ishipToLocation2 = $itemInfo['ishipToLocation2'];
        }

        $returnPolicy = $itemInfo['returnPolicy'];
        if (isset($itemInfo['schedule_time'])) {
            $schedule_time = $itemInfo['schedule_time'];
        }

        if ($returnPolicy == 1) {
            $return_within = $itemInfo['return_within'];
            $return_refund_as = $itemInfo['return_refund_as'];
            $return_actor = $itemInfo['return_actor'];
            if (isset($itemInfo['return_details'])) {
                $return_details = $itemInfo['return_details'];
            }
        }
        if (isset($itemInfo['reservePrice'])) {
            $reservePrice = $itemInfo['reservePrice'];
        }
        if (isset($itemInfo['buyItNowPrice'])) {
            $buyItNowPrice = $itemInfo['buyItNowPrice'];
        }

        $listType = $itemInfo['listType'];
        if (isset($itemInfo['bestOfferEnabled'])) {
            $bestOfferEnabled = $itemInfo['bestOfferEnabled'];
        }
        if (isset($itemInfo['minimumBestOfferPrice'])) {
            $minimumBestOfferPrice = $itemInfo['minimumBestOfferPrice'];
        }
        if (isset($itemInfo['bestOfferAutoAcceptPrice'])) {
            $bestOfferAutoAcceptPrice = $itemInfo['bestOfferAutoAcceptPrice'];
        }

        if (isset($itemInfo['quantity']) && is_numeric($itemInfo['quantity'])) {
            $quantity = $itemInfo['quantity'];
        }

        $pictureUrl = $itemInfo['pictureUrl'];

        if (isset($itemInfo['itemSpecifics'])) {
            $itemSpecifics = $itemInfo['itemSpecifics'];
        }

        $listingDuration = $itemInfo['listingDuration'];
        $country = strtoupper($itemInfo['country']);
        $location = $itemInfo['location'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<$requestType xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <ErrorLanguage>en_US</ErrorLanguage>
  <WarningLevel>High</WarningLevel>
  <Item>
XML;

    $requestXMLBody .= <<<XML
<Title>$title</Title>
    <Description>
        $description
    </Description>

XML;
    if ($bestOfferEnabled == true) {
        $requestXMLBody .= <<<XML
    <BestOfferDetails>
      <BestOfferEnabled>true</BestOfferEnabled>
    </BestOfferDetails>
XML;
    }
    if (isset($bestOfferAutoAcceptPrice) || isset($minimumBestOfferPrice)) {
        $requestXMLBody .= <<<XML
    <ListingDetails>
XML;
    }
    if (isset($bestOfferAutoAcceptPrice)) {
        $requestXMLBody .= <<<XML
      <BestOfferAutoAcceptPrice currencyID="$currency">$bestOfferAutoAcceptPrice</BestOfferAutoAcceptPrice>
XML;
    }
      if (isset($minimumBestOfferPrice)) {
        $requestXMLBody .= <<<XML
      <MinimumBestOfferPrice currencyID="$currency">$minimumBestOfferPrice</MinimumBestOfferPrice>
XML;
      }
    if (isset($bestOfferAutoAcceptPrice) || isset($minimumBestOfferPrice)) {
        $requestXMLBody .= <<<XML
    </ListingDetails>
XML;
    }

    $requestXMLBody .= <<<XML
    <PrimaryCategory>
      <CategoryID>$categoryID</CategoryID>
    </PrimaryCategory>
XML;

        $requestXMLBody .= <<<XML
<StartPrice currencyID="$currency">$startPrice</StartPrice>
XML;
    if (isset($buyItNowPrice)) {
        $requestXMLBody .= <<<XML
<BuyItNowPrice currencyID="$currency">$buyItNowPrice</BuyItNowPrice>
XML;
    }
    if (isset($reservePrice)) {
        $requestXMLBody .= <<<XML
<ReservePrice currencyID="$currency">$reservePrice</ReservePrice>
XML;
    }
$requestXMLBody .= <<<XML
    <CategoryMappingAllowed>true</CategoryMappingAllowed>
    <ConditionID>1000</ConditionID>
    <Country>$country</Country>
    <Location>$location</Location>
    <Currency>$currency</Currency>
    <ListingDuration>$listingDuration</ListingDuration>
    <ListingType>$listType</ListingType>
    <PaymentMethods>PayPal</PaymentMethods>
    <PayPalEmailAddress>$payPalEmailAddress</PayPalEmailAddress>
XML;
        $requestXMLBody .= <<<XML
<PictureDetails>
      <GalleryType>Gallery</GalleryType>
      <PhotoDisplay>None</PhotoDisplay>
XML;
        if (isset($pictureUrl) && is_string($pictureUrl)) {
            $requestXMLBody .= <<<XML
<GalleryURL>$pictureUrl</GalleryURL>
<PictureURL>$pictureUrl</PictureURL>
XML;
        } else if(isset($pictureUrl) && is_array($pictureUrl)) {
            $requestXMLBody .= <<<XML
<GalleryURL>$pictureUrl[0]</GalleryURL>
XML;
            foreach ($pictureUrl as $url) {
                $requestXMLBody .= <<<XML
<PictureURL>$url</PictureURL>
XML;
            }
        }

        $requestXMLBody .= <<<XML
</PictureDetails>
XML;

        $requestXMLBody .= <<<XML
    <Quantity>$quantity</Quantity>
XML;
        
        if (isset($itemSpecifics)) {

        $requestXMLBody .= <<<XML
    <ItemSpecifics>
XML;
        foreach ($itemSpecifics as $name => $value) {
            $requestXMLBody .= <<<XML
      <NameValueList>
        <Name>$name</Name>
        <Value>$value</Value>
      </NameValueList>
XML;
        }

        $requestXMLBody .= <<<XML
    </ItemSpecifics>
XML;
    }
        $requestXMLBody .= <<<XML
    <ReturnPolicy>
XML;
        if ($returnPolicy == 0) {
        $requestXMLBody .= <<<XML
      <ReturnsAcceptedOption>ReturnsNotAccepted</ReturnsAcceptedOption>
XML;
        } else {
        $requestXMLBody .= <<<XML
      <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
      <RefundOption>$return_refund_as</RefundOption>
      <ReturnsWithinOption>$return_within</ReturnsWithinOption>
      <ShippingCostPaidByOption>$return_actor</ShippingCostPaidByOption>
XML;
            if (isset($return_details)) {
    $requestXMLBody .= <<<XML
      <Description>$return_details</Description>
XML;
            }
        }
    $requestXMLBody .= <<<XML
    </ReturnPolicy>

    <ShippingDetails>
      <ShippingType>$shippingType</ShippingType>
      <ShippingServiceOptions>
        <ShippingServicePriority>1</ShippingServicePriority>
        <ShippingService>$shippingService0</ShippingService>
XML;
        if (isset($free0)) {
            $requestXMLBody .= <<<XML
            <FreeShipping>true</FreeShipping>
XML;
        } else {
            $requestXMLBody .= <<<XML
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost0</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost0</ShippingServiceAdditionalCost>
XML;
        }

        if (isset($shippingSurcharge0)) {
            $requestXMLBody .= <<<XML
        <ShippingSurcharge currencyID="$currency">$shippingSurcharge0</ShippingSurcharge>
XML;
        }
        $requestXMLBody .= <<<XML
      </ShippingServiceOptions>
XML;
        if (isset($shippingService1)) {
            $requestXMLBody .= <<<XML
        <ShippingServiceOptions>
            <ShippingServicePriority>2</ShippingServicePriority>
            <ShippingService>$shippingService1</ShippingService>
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost1</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost1</ShippingServiceAdditionalCost>
XML;
            if (isset($shippingSurcharge1)) {
            $requestXMLBody .= <<<XML
            <ShippingSurcharge currencyID="$currency">$shippingSurcharge1</ShippingSurcharge>
XML;
            }
            $requestXMLBody .= <<<XML
        </ShippingServiceOptions>
XML;
        }
        if (isset($shippingService2)) {
            $requestXMLBody .= <<<XML
        <ShippingServiceOptions>
            <ShippingServicePriority>3</ShippingServicePriority>
            <ShippingService>$shippingService2</ShippingService>
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost2</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost2</ShippingServiceAdditionalCost>
XML;
            if (isset($shippingSurcharge2)) {
            $requestXMLBody .= <<<XML
            <ShippingSurcharge currencyID="$currency">$shippingSurcharge2</ShippingSurcharge>
XML;
            }
            $requestXMLBody .= <<<XML
        </ShippingServiceOptions>
XML;
        }
        $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService0</ShippingService>
        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost0</ShippingServiceAdditionalCost>
XML;
        $requestXMLBody .= <<<XML
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost0</ShippingServiceCost>
        <ShippingServicePriority>1</ShippingServicePriority>
XML;
        if (isset($iShipToLocation0)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$iShipToLocation0</ShipToLocation>
XML;
        }
        $requestXMLBody .= <<<XML
      </InternationalShippingServiceOption>
XML;
        if (isset($ishippingService1)) {
            $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService1</ShippingService>
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost1</ShippingServiceCost>
        <ShippingServicePriority>2</ShippingServicePriority>
        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost1</ShippingServiceAdditionalCost>
XML;
            if (isset($ishipToLocation1)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$ishipToLocation1</ShipToLocation>
XML;
            }
            $requestXMLBody .= <<<XML
        </InternationalShippingServiceOption>
XML;
        }
        if (isset($ishippingService2)) {
            $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService2</ShippingService>
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost2</ShippingServiceCost>
        <ShippingServicePriority>3</ShippingServicePriority>

        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost2</ShippingServiceAdditionalCost>
XML;
            if (isset($ishipToLocation2)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$ishipToLocation2</ShipToLocation>
XML;
            }
            $requestXMLBody .= <<<XML
        </InternationalShippingServiceOption>
XML;
        }
        $requestXMLBody .= <<<XML
    </ShippingDetails>
XML;
    if (isset($getItFast)) {
        $requestXMLBody .= <<<XML
    <GetItFast>true</GetItFast>
XML;
    } else {
        $requestXMLBody .= <<<XML
    <GetItFast>false</GetItFast>
XML;
    }
        $requestXMLBody .= <<<XML
    <ShipToLocations>Worldwide</ShipToLocations>
    <DispatchTimeMax>$dispatchTimeMax</DispatchTimeMax>
    <SKU> $customerLabel </SKU>

    <Site>$site</Site>
XML;
        if (isset($schedule_time)) {
            $requestXMLBody .= <<<XML
    <ScheduleTime>$schedule_time</ScheduleTime>
XML;
        }
        if (isset($storeCategoryID)) {
            $requestXMLBody .= <<<XML
    <Storefront>
      <StoreCategoryID>$storeCategoryID</StoreCategoryID>
    </Storefront>
XML;
        }
        $requestXMLBody .= <<<XML
  </Item>
<$requestType>
XML;
        return $requestXMLBody;
    }


    function add_multi_sku_item($appToken, $itemInfo, $requestType) {
        $currency = $itemInfo['currency'];
        $site = $itemInfo['site'];
        $title = $itemInfo['title'];
        $description = htmlspecialchars($itemInfo['description'], ENT_QUOTES, 'UTF-8');
        $startPrice = $itemInfo['startPrice'];
        $categoryID = $itemInfo['categoryID'];
        if (isset($itemInfo['storeCategoryID'])) {
            $storeCategoryID = $itemInfo['storeCategoryID'];
        }
        $payPalEmailAddress = $itemInfo['payPalEmailAddress'];
        $shippingType = $itemInfo['shippingType'];
        if (isset($itemInfo['shippingService0'])) {
            $shippingService0 = $itemInfo['shippingService0'];
        }
        $free0 = $itemInfo['free0'];
        $shippingServiceCost0 = $itemInfo['shippingServiceCost0'];
        $shippingServiceAdditionalCost0 = $itemInfo['shippingServiceAdditionalCost0'];
        if (isset($itemInfo['shippingSurcharge0'])) {
            $shippingSurcharge0 = $itemInfo['shippingSurcharge0'];
        }
        $dispatchTimeMax = $itemInfo['dispatchTimeMax'];
        if (isset($itemInfo['getItFast'])) {
            $getItFast = $itemInfo['getItFast'];
        }
        if (isset($itemInfo['schedule_time'])) {
            $schedule_time = $itemInfo['schedule_time'];
        }
        $ishippingService0 = $itemInfo['ishippingService0'];
        if (isset($itemInfo['ifree0'])) {
            $ifree0 = $itemInfo['ifree0'];
        }
        $ishippingServiceCost0 = $itemInfo['ishippingServiceCost0'];
        $ishippingServiceAdditionalCost0 = $itemInfo['ishippingServiceAdditionalCost0'];
        $iShipToLocation0 = $itemInfo['iShipToLocation0'];

        
        if (isset($itemInfo['shippingService1'])) {
            $shippingService1 = $itemInfo['shippingService1'];
            $shippingServiceCost1 = $itemInfo['shippingServiceCost1'];
            $shippingServiceAdditionalCost1 = $itemInfo['shippingServiceAdditionalCost1'];
            if (isset($itemInfo['shippingSurcharge1'])) {
                $shippingSurcharge1 = $itemInfo['shippingSurcharge1'];
            }
        }
        
        if (isset($itemInfo['shippingService2'])) {
            $shippingService2 = $itemInfo['shippingService2'];
            $shippingServiceCost2 = $itemInfo['shippingServiceCost2'];
            $shippingServiceAdditionalCost2 = $itemInfo['shippingServiceAdditionalCost2'];
            if (isset($itemInfo['shippingSurcharge2'])) {
                $shippingSurcharge2 = $itemInfo['shippingSurcharge2'];
            }
        }

        
        if (isset($itemInfo['ishippingService1'])) {
            $ishippingService1 = $itemInfo['ishippingService1'];
            $ishippingServiceCost1 = $itemInfo['ishippingServiceCost1'];
            $ishippingServiceAdditionalCost1 = $itemInfo['ishippingServiceAdditionalCost1'];
            $ishipToLocation1 = $itemInfo['ishipToLocation1'];
        }
        
        if (isset($itemInfo['ishippingService2'])) {
            $ishippingService2 = $itemInfo['ishippingService2'];
            $ishippingServiceCost2 = $itemInfo['ishippingServiceCost2'];
            $ishippingServiceAdditionalCost2 = $itemInfo['ishippingServiceAdditionalCost2'];
            $ishipToLocation2 = $itemInfo['ishipToLocation2'];
        }

        $returnPolicy = $itemInfo['returnPolicy'];

        if ($returnPolicy == 1) {
            $return_within = $itemInfo['return_within'];
            $return_refund_as = $itemInfo['return_refund_as'];
            $return_actor = $itemInfo['return_actor'];
            if (isset($itemInfo['return_details'])) {
                $return_details = $itemInfo['return_details'];
            }
        }
        if (isset($itemInfo['reservePrice'])) {
            $reservePrice = $itemInfo['reservePrice'];
        }
        if (isset($itemInfo['buyItNowPrice'])) {
            $buyItNowPrice = $itemInfo['buyItNowPrice'];
        }

        $listType = $itemInfo['listType'];
        if (isset($itemInfo['bestOfferEnabled'])) {
            $bestOfferEnabled = $itemInfo['bestOfferEnabled'];
        }
        if (isset($itemInfo['minimumBestOfferPrice'])) {
            $minimumBestOfferPrice = $itemInfo['minimumBestOfferPrice'];
        }
        if (isset($itemInfo['bestOfferAutoAcceptPrice'])) {
            $bestOfferAutoAcceptPrice = $itemInfo['bestOfferAutoAcceptPrice'];
        }

        if (isset($itemInfo['quantity']) && is_numeric($itemInfo['quantity'])) {
            $quantity = $itemInfo['quantity'];
        }

        $pictureUrl = $itemInfo['pictureUrl'];

        if (isset($itemInfo['itemSpecifics'])) {
            $itemSpecifics = $itemInfo['itemSpecifics'];
        }

        $listingDuration = $itemInfo['listingDuration'];
        $country = strtoupper($itemInfo['country']);
        $location = $itemInfo['location'];

        if (isset($itemInfo['variations'])) {
            $variations = $itemInfo['variations'];
        }
        if (isset($itemInfo['customerLabel'])) {
            $customerLabel = $itemInfo['customerLabel'];
        }

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<$requestType xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <ErrorLanguage>en_US</ErrorLanguage>
  <WarningLevel>High</WarningLevel>
  <Item>
XML;

    $requestXMLBody .= <<<XML
<Title>$title</Title>
    <Description>
        $description
    </Description>

XML;

    $requestXMLBody .= <<<XML
    <PrimaryCategory>
      <CategoryID>$categoryID</CategoryID>
    </PrimaryCategory>
XML;

        $requestXMLBody .= <<<XML
<StartPrice currencyID="$currency">$startPrice</StartPrice>
XML;

$requestXMLBody .= <<<XML
    <CategoryMappingAllowed>true</CategoryMappingAllowed>
    <ConditionID>1000</ConditionID>
    <Country>$country</Country>
    <Location>$location</Location>
    <Currency>$currency</Currency>
    <ListingDuration>$listingDuration</ListingDuration>
    <ListingType>FixedPriceItem</ListingType>
    <PaymentMethods>PayPal</PaymentMethods>
    <PayPalEmailAddress>$payPalEmailAddress</PayPalEmailAddress>
XML;
        $requestXMLBody .= <<<XML
<PictureDetails>
      <GalleryType>Gallery</GalleryType>
      <PhotoDisplay>None</PhotoDisplay>
XML;
        if (isset($pictureUrl) && is_string($pictureUrl)) {
            $requestXMLBody .= <<<XML
<GalleryURL>$pictureUrl</GalleryURL>
<PictureURL>$pictureUrl</PictureURL>
XML;
        } else if(isset($pictureUrl) && is_array($pictureUrl)) {
            $requestXMLBody .= <<<XML
<GalleryURL>$pictureUrl[0]</GalleryURL>
XML;
            foreach ($pictureUrl as $url) {
                $requestXMLBody .= <<<XML
<PictureURL>$url</PictureURL>
XML;
            }
        }

        $requestXMLBody .= <<<XML
</PictureDetails>
XML;

        $requestXMLBody .= <<<XML
    <Quantity>$quantity</Quantity>
XML;
        if (isset($itemSpecifics)) {
            $requestXMLBody .= <<<XML
    <ItemSpecifics>
XML;
        foreach ($itemSpecifics as $name => $value) {
            $requestXMLBody .= <<<XML
      <NameValueList>
        <Name>$name</Name>
        <Value>$value</Value>
      </NameValueList>
XML;
        }

        $requestXMLBody .= <<<XML
    </ItemSpecifics>
XML;
        }
        $requestXMLBody .= <<<XML
    <ReturnPolicy>
XML;
        if ($returnPolicy == 0) {
        $requestXMLBody .= <<<XML
      <ReturnsAcceptedOption>ReturnsNotAccepted</ReturnsAcceptedOption>
XML;
        } else {
        $requestXMLBody .= <<<XML
      <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
      <RefundOption>$return_refund_as</RefundOption>
      <ReturnsWithinOption>$return_within</ReturnsWithinOption>
      <ShippingCostPaidByOption>$return_actor</ShippingCostPaidByOption>
XML;
            if (isset($return_details)) {
    $requestXMLBody .= <<<XML
      <Description>$return_details</Description>
XML;
            }
        }
    $requestXMLBody .= <<<XML
    </ReturnPolicy>

    <ShippingDetails>
      <ShippingType>$shippingType</ShippingType>
      <ShippingServiceOptions>
        <ShippingServicePriority>1</ShippingServicePriority>
        <ShippingService>$shippingService0</ShippingService>
XML;
        if ($free0 == true) {
            $requestXMLBody .= <<<XML
            <FreeShipping>true</FreeShipping>
XML;
        } else {
            $requestXMLBody .= <<<XML
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost0</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost0</ShippingServiceAdditionalCost>
XML;
        }
        if (isset($shippingSurcharge0)) {
            $requestXMLBody .= <<<XML
        <ShippingSurcharge currencyID="$currency">$shippingSurcharge0</ShippingSurcharge>
XML;
        }
        $requestXMLBody .= <<<XML
      </ShippingServiceOptions>
XML;
        if (isset($shippingService1)) {
            $requestXMLBody .= <<<XML
        <ShippingServiceOptions>
            <ShippingServicePriority>2</ShippingServicePriority>
            <ShippingService>$shippingService1</ShippingService>
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost1</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost1</ShippingServiceAdditionalCost>
XML;
            if (isset($shippingSurcharge1)) {
            $requestXMLBody .= <<<XML
            <ShippingSurcharge currencyID="$currency">$shippingSurcharge1</ShippingSurcharge>
XML;
            }
            $requestXMLBody .= <<<XML
        </ShippingServiceOptions>
XML;
        }
        if (isset($shippingService2)) {
            $requestXMLBody .= <<<XML
        <ShippingServiceOptions>
            <ShippingServicePriority>3</ShippingServicePriority>
            <ShippingService>$shippingService2</ShippingService>
            <ShippingServiceCost currencyID="$currency">$shippingServiceCost2</ShippingServiceCost>
            <ShippingServiceAdditionalCost currencyID="$currency">$shippingServiceAdditionalCost2</ShippingServiceAdditionalCost>
XML;
            if (isset($shippingSurcharge2)) {
            $requestXMLBody .= <<<XML
            <ShippingSurcharge currencyID="$currency">$shippingSurcharge2</ShippingSurcharge>
XML;
            }
            $requestXMLBody .= <<<XML
        </ShippingServiceOptions>
XML;
        }
        $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService0</ShippingService>
        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost0</ShippingServiceAdditionalCost>
XML;
        $requestXMLBody .= <<<XML
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost0</ShippingServiceCost>
        <ShippingServicePriority>1</ShippingServicePriority>
XML;
        if (isset($iShipToLocation0)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$iShipToLocation0</ShipToLocation>
XML;
        }
        $requestXMLBody .= <<<XML
      </InternationalShippingServiceOption>
XML;
        if (isset($ishippingService1)) {
            $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService1</ShippingService>
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost1</ShippingServiceCost>
        <ShippingServicePriority>2</ShippingServicePriority>
        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost1</ShippingServiceAdditionalCost>
XML;
            if (isset($ishipToLocation1)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$ishipToLocation1</ShipToLocation>
XML;
            }
            $requestXMLBody .= <<<XML
        </InternationalShippingServiceOption>
XML;
        }
        if (isset($ishippingService2)) {
            $requestXMLBody .= <<<XML
      <InternationalShippingServiceOption>
        <ShippingService>$ishippingService2</ShippingService>
        <ShippingServiceCost currencyID="$currency">$ishippingServiceCost2</ShippingServiceCost>
        <ShippingServicePriority>3</ShippingServicePriority>
        <ShippingServiceAdditionalCost currencyID="$currency">$ishippingServiceAdditionalCost2</ShippingServiceAdditionalCost>
XML;
            if (isset($ishipToLocation2)) {
            $requestXMLBody .= <<<XML
        <ShipToLocation>$ishipToLocation2</ShipToLocation>
XML;
            }
            $requestXMLBody .= <<<XML
        </InternationalShippingServiceOption>
XML;
        }
        $requestXMLBody .= <<<XML
    </ShippingDetails>
XML;
    if (isset($getItFast)) {
        $requestXMLBody .= <<<XML
    <GetItFast> true </GetItFast>
XML;
    }
        $requestXMLBody .= <<<XML
    <ShipToLocations>Worldwide</ShipToLocations>
    <DispatchTimeMax>$dispatchTimeMax</DispatchTimeMax>

    <SKU> $customerLabel </SKU>
XML;
        if (isset($variations)) {
        $requestXMLBody .= <<<XML
    <Variations>
XML;
        foreach ($variations['variationSpecifics'] as $variationSpecific) {
        $requestXMLBody .= <<<XML
      <Variation>
        <Quantity>{$variationSpecific['qnt']}</Quantity>
        <SKU>{$variationSpecific['sku']}</SKU>
        <StartPrice>{$variationSpecific['price']}</StartPrice>
        <VariationSpecifics>
XML;
        $variation_name_values = $variationSpecific['variation_name_value'];
        foreach ($variation_name_values as $name_value) {
            foreach ($name_value as $name => $value) {
            $requestXMLBody .= <<<XML
              <NameValueList>
                <Name>$name</Name>
                <Value>$value</Value>
              </NameValueList>
XML;
            }
        }
        $requestXMLBody .= <<<XML
        </VariationSpecifics>
      </Variation>
XML;
        }

        $requestXMLBody .= <<<XML
      <VariationSpecificsSet>
XML;
        $variationSpecificsSet = $variations['variationSpecificsSet'];
        foreach ($variationSpecificsSet as $name => $values) {
            $requestXMLBody .= <<<XML
        <NameValueList>
          <Name>$name</Name>
XML;
            foreach ($values as $value) {
            $requestXMLBody .= <<<XML
          <Value>$value</Value>
XML;
            }
            $requestXMLBody .= <<<XML
        </NameValueList>
XML;
        }
        $requestXMLBody .= <<<XML
      </VariationSpecificsSet>
    </Variations>
XML;
        }

        $requestXMLBody .= <<<XML
    <Site>$site</Site>
XML;
        if (isset($schedule_time)) {
            $requestXMLBody .= <<<XML
    <ScheduleTime>$schedule_time</ScheduleTime>
XML;
        }
        if (isset($storeCategoryID)) {
            $requestXMLBody .= <<<XML
    <Storefront>
      <StoreCategoryID>$storeCategoryID</StoreCategoryID>
    </Storefront>
XML;
        }
        $requestXMLBody .= <<<XML
  </Item>
<$requestType>
XML;
        return $requestXMLBody;
    }

    function get_item_specifics($appToken, $categoryID) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <WarningLevel>High</WarningLevel>
  <CategorySpecific>
    <CategoryID>$categoryID</CategoryID>
  </CategorySpecific>
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
</GetCategorySpecificsRequest>
XML;

        return $requestXMLBody;
    }

    function get_orders($appToken, $itemInfo) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
  <CreateTimeFrom>{$itemInfo['createTimeFrom']}</CreateTimeFrom>
  <CreateTimeTo>{$itemInfo['createTimeTo']}</CreateTimeTo>
  <OrderRole>Seller</OrderRole>
  <OrderStatus>Active</OrderStatus>
  <Pagination>
    <EntriesPerPage>{$itemInfo['itemsPerPage']}</EntriesPerPage>
    <PageNumber>{$itemInfo['pageIndex']}</PageNumber>
  </Pagination>
</GetOrdersRequest>
XML;

    return $requestXMLBody;
    }

    function get_transactions($appToken, $itemInfo) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetSellerTransactionsRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
  <ModTimeFrom>{$itemInfo['createTimeFrom']}</ModTimeFrom>
  <ModTimeTo>{$itemInfo['createTimeTo']}</ModTimeTo>
  <Pagination>
    <EntriesPerPage>{$itemInfo['itemsPerPage']}</EntriesPerPage>
    <PageNumber>{$itemInfo['pageIndex']}</PageNumber>
  </Pagination>
</GetSellerTransactionsRequest >
XML;

    return $requestXMLBody;
    }

    function get_item_transactions($appToken, $itemInfo) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetItemTransactionsRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <IncludeFinalValueFee>true</IncludeFinalValueFee>
  <ItemID ComplexType="ItemIDType">{$itemInfo['itemId']}</ItemID>
  <Pagination>
    <EntriesPerPage>{$itemInfo['itemsPerPage']}</EntriesPerPage>
    <PageNumber>{$itemInfo['pageIndex']}</PageNumber>
  </Pagination>
</GetItemTransactionsRequest >
XML;

    return $requestXMLBody;
    }

    function get_account($appToken, $itemInfo) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetAccountRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <AccountEntrySortType>AccountEntryFeeTypeDescending</AccountEntrySortType>
  <AccountHistorySelection>BetweenSpecifiedDates</AccountHistorySelection>
  <BeginDate>{$itemInfo['beginTime']}</BeginDate>
  <EndDate>{$itemInfo['endTime']}</EndDate>
  <Pagination>
    <EntriesPerPage>{$itemInfo['itemsPerPage']}</EntriesPerPage>
    <PageNumber>{$itemInfo['pageIndex']}</PageNumber>
  </Pagination>
</GetAccountRequest >
XML;

        return $requestXMLBody;
    }

    function complete_sale($appToken, $itemInfo) {
        $track_numbers = $itemInfo['track_numbers'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <WarningLevel>High</WarningLevel>
  <ItemID>{$itemInfo['item_id']}</ItemID>
  <Paid>true</Paid>
  <Shipment>
    <Notes>{$itemInfo['shipping_note']}</Notes>
XML;
        foreach ($track_numbers as $track_number)
        {
            if (empty($track_number))
            {
                continue;
            }
            $track_number = strtoupper($track_number);

            $requestXMLBody .= <<<XML
    <ShipmentTrackingDetails>
      <ShipmentTrackingNumber>$track_number</ShipmentTrackingNumber>
      <ShippingCarrierUsed>{$itemInfo['shipping_carrier']}</ShippingCarrierUsed>
    </ShipmentTrackingDetails>
XML;
        }
        $requestXMLBody .= <<<XML
    <ShippedTime>{$itemInfo['shipping_date']}</ShippedTime>
  </Shipment>
  <Shipped>true</Shipped>
  <TransactionID>{$itemInfo['transaction_id']}</TransactionID>
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <WarningLevel>High</WarningLevel>
</CompleteSaleRequest>
XML;

        return $requestXMLBody;
    }


	function get_ebay_message($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];
        $begin_time = $itemInfo['begin_time'];
        $end_time = $itemInfo['end_time'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetMemberMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <MailMessageType>All</MailMessageType>
  <MessageStatus>Unanswered</MessageStatus> 
  <Version>719</Version>
  <StartCreationTime>$begin_time</StartCreationTime>
  <EndCreationTime>$end_time</EndCreationTime>
  <Pagination>
    <EntriesPerPage>$itemsPerPage</EntriesPerPage>
    <PageNumber>$pageIndex</PageNumber>
  </Pagination>
</GetMemberMessagesRequest>
XML;

        return $requestXMLBody;
    }
    function get_ebay_mymessage($appToken, $itemInfo) {
        $itemsPerPage = $itemInfo['itemsPerPage'];
        $pageIndex = $itemInfo['pageIndex'];
        $begin_time = $itemInfo['begin_time'];
        $end_time = $itemInfo['end_time'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials> 
  <Version>655</Version>
  <StartTime>$begin_time</StartTime>
  <EndTime>$end_time</EndTime>
  <Pagination>
    <EntriesPerPage>$itemsPerPage</EntriesPerPage>
    <PageNumber>$pageIndex</PageNumber>
  </Pagination>
</GetMyMessagesRequest>
XML;

        return $requestXMLBody;
    }

	function add_ebay_message($appToken, $itemInfo) {
        $itemid = $itemInfo['itemid'];
        $content = $itemInfo['content'];
        $message_id = $itemInfo['message_id'];
        $sendid = $itemInfo['sendid'];

        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<AddMemberMessageRTQRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <WarningLevel>High</WarningLevel>
  <Version>719</Version>
  <ItemID>$itemid</ItemID>
	<MemberMessage>
		<Body>$content</Body>
		<EmailCopyToSender>true</EmailCopyToSender>
		<ParentMessageID>$message_id</ParentMessageID>
		<RecipientID>$sendid</RecipientID>
	</MemberMessage>
</AddMemberMessageRTQRequest>
XML;

        return $requestXMLBody;
    }


	function get_transaction_by_id($appToken, $transaction_id,$item_id) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetOrderTransactionsRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <ItemTransactionIDArray>
    <ItemTransactionID>
		<TransactionID>$transaction_id</TransactionID>
		<ItemID>$item_id</ItemID>
	</ItemTransactionID>
  </ItemTransactionIDArray>
</GetOrderTransactionsRequest>
XML;
    return $requestXMLBody;
    }
?>
