<?php

class MY_Ebay {
    
    public function getItem($session, $xml) {   
        $respondXML = $session->sendHttpRequest($xml);

        $resp = simplexml_load_string($respondXML);

        return $resp;
    }

    public function  verifyAddItem($itemInfo) {
        $resp =  self::addItem($itemInfo, 'VerifyAddItem');

        if ($resp->Ack == 'Success' || $resp->Ack == 'Warning') {
            $toReturn['Ack'] = 'Success';
            $toReturn['Fees'] = self::countFees($resp);            
        } else {
            $toReturn['Ack'] = 'Failure';
            $err_num = count($resp->Errors);
            $toReturn['Message'] = sprintf('%s', $resp->Errors[$err_num - 1]->LongMessage);
        }
        
        return json_encode($toReturn);
    }

    public function  addItem($session, $xml) {
        require_once 'XMLBody/addItem.php';
        global $devID, $appID, $certificateID, $serverUrl, $compatabilityLevel;
        global $appToken;

        $siteID = self::getSiteId($itemInfo['site']);

        $requestType = $callName.'Request';

        $newDescription = htmlentities($newDescription);
        $requestXMLBody = addItemXMLBody($appToken, $itemInfo, $requestType);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        return $resp;
    }

    static public function  verifyAddMultiSkuItem($itemInfo) {
        $resp =  self::addMultiSkuItem($itemInfo, 'VerifyAddFixedPriceItem');

        if ($resp->Ack == 'Success' || $resp->Ack == 'Warning') {
            $toReturn['Ack'] = 'Success';
            $toReturn['Fees'] = self::countFees($resp);
        } else {
            $toReturn['Ack'] = 'Failure';
            $err_num = count($resp->Errors);
            $toReturn['Message'] = sprintf('%s', $resp->Errors[$err_num - 1]->LongMessage);
        }

        return json_encode($toReturn);
    }
    
    static public function  addMultiSkuItem($itemInfo, $callName = 'AddFixedPriceItem') {
        require_once 'XMLBody/addMultiSkuItem.php';
        global $devID, $appID, $certificateID, $serverUrl, $compatabilityLevel;
        global $appToken;

        $siteID = self::getSiteId($itemInfo['site']);

        $requestType = $callName.'Request';
        $newDescription = htmlentities($newDescription);
        $requestXMLBody = self::addMultiSkuItemXMLBody($appToken, $itemInfo, $requestType);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        return $resp;
    }

    static private function countFees($resp) {
        $totalFees = 0.0;
        foreach ($resp->Fees->Fee as $fees) {
            $fee = trim($fees->Fee);            
            $totalFees += $fee;
        }

        return $totalFees;
    }

    static public function searchCategories($keyword, $site) {
        require_once 'XMLBody/searchCategories.php';
        
        global $devID, $appID, $certificateID, $serverUrl, $compatabilityLevel;
        global $appToken, $payPalEmailAddress;

        $siteID = self::getSiteId($site);
        $callName = 'GetSuggestedCategories';

        $keyword = htmlentities($keyword);
        $requestXMLBody = searchCategoriesXMLBody($appToken, $keyword);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        return self::makeCategoriesToIDAndNameArray($resp);
    }

    static private function makeCategoriesToIDAndNameArray($resp) {
        $categoriesIDAndNameArray = array();
        $suggestedCategoryArray = $resp->SuggestedCategoryArray;

        foreach($suggestedCategoryArray as $suggestedCategoryObj) {
            $suggestedCategoryInsideArray = $suggestedCategoryObj->SuggestedCategory;
            foreach ($suggestedCategoryInsideArray as $suggestedCategoryInsideObj) {
                $percentItemFound = $suggestedCategoryInsideObj->PercentItemFound;
                $suggestedCategoryInsideObj = $suggestedCategoryInsideObj->Category;
                $categoryId = $suggestedCategoryInsideObj->CategoryID;
                $categoryName = $suggestedCategoryInsideObj->CategoryName;
                $categoryParentName = array();
                foreach ($suggestedCategoryInsideObj->CategoryParentName as $t) {
                    $categoryParentName[] = $t;
                }
                
                if(is_array($categoryParentName)) {
                    $parentName = '';
                    foreach ($categoryParentName as $name) {
                        $parentName .= "$name > ";
                    }
                    if (empty ($parentName)) {
                        $categoriesIDAndNameArray[$categoryId] = "$categoryName";
                    } else {
                        $parentName = substr($parentName, 0, -3);
                        $categoriesIDAndNameArray["$categoryId"] = "$parentName > $categoryName(".$percentItemFound."%)";
                    }
                }
            }
        }
        
        return $categoriesIDAndNameArray;
    }

    static public function getMyebayList($itemInfo) {
        require_once 'XMLBody/getMyebayList.php';
        global $devID, $appID, $certificateID, $serverUrl, $compatabilityLevel;
        global $appToken;

        $compatabilityLevel = 683;
        $siteID = self::getSiteId($itemInfo['site']);
        $callName = 'GetMyeBaySelling';
        
        $requestXMLBody = getMyebayListXMLBody($appToken, $itemInfo);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        return $resp;
    }

    static public function checkItemSpecificsEnabled($categoryID) {
        require_once './config.php';
        require_once './eBaySession.php';
        $callName = 'GetCategoryFeatures';
        
        $requestXMLBody = self::checkItemSpecificsEnabledXMLBody($appToken, $categoryID);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        if ($resp->Ack == 'Success') {
             $str = $resp->Category->ItemSpecificsEnabled;
             if ($str == 'Enabled') {
                 return true;
             }
        }

        return false;
    }

    static private function checkItemSpecificsEnabledXMLBody($appToken, $categoryID) {
        $requestXMLBody = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<GetCategoryFeaturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>$appToken</eBayAuthToken>
  </RequesterCredentials>
  <CategoryID>$categoryID</CategoryID>
  <DetailLevel>ReturnAll</DetailLevel>
  <FeatureID>ItemSpecificsEnabled</FeatureID>
</GetCategoryFeaturesRequest>
XML;

        return $requestXMLBody;
    }

    static public function getItemSpecifics($categoryID) {
        if (! self::checkItemSpecificsEnabled($categoryID)) {
            return array();
        }

        require_once './config.php';
        require_once './eBaySession.php';
        $callName = 'GetCategorySpecifics';

        $requestXMLBody = self::getItemSpecificsXMLBody($appToken, $categoryID);

        $session = new eBaySession($devID, $appID, $certificateID, $serverUrl, $compatabilityLevel, $siteID, $callName);
        $respondXML = $session->sendHttpRequest($requestXMLBody);

        $resp = simplexml_load_string($respondXML);

        return self::makeItemSpecificsNameAndValue($resp);
    }

    static private function getItemSpecificsXMLBody($appToken, $categoryID) {
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

    static private function makeItemSpecificsNameAndValue($resp) {
        $ItemSpecifics = array();
        if (!$resp || $resp->Ack != 'Success') {
            return $ItemSpecifics;
        }

        $nameRecommendations = $resp->Recommendations->NameRecommendation;
        foreach ($nameRecommendations as $nameRecommendation) {
            $name = $nameRecommendation->Name;
            $values = array();
            $valueRecommendations = $nameRecommendation->ValueRecommendation;

            foreach ($valueRecommendations as $valueRecommendation) {
                $values[] = $valueRecommendation->Value;
            }
            $ItemSpecifics["$name"] = $values;
        }

        return $ItemSpecifics;
    }
    static private function getSiteId($siteName) {
        $siteId;
        switch ($siteName) {
            case 'US':
                $siteId = 0;
                break;
            case 'Australia':
                $siteId = 15;
                break;
            case 'UK':
                $siteId = 3;
                break;
            case 'France':
                $siteId = 71;
                break;
        }

        return $siteId;
    }
}
?>
