<?php
    function get_site_id($siteName) {
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


    function make_categories_id_name($resp) {
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
                        $categoriesIDAndNameArray["$categoryId"] = "$categoryName";
                    } else {
                        $parentName = substr($parentName, 0, -3);
                        $categoriesIDAndNameArray["$categoryId"] = "$parentName > $categoryName(".$percentItemFound."%)";
                    }
                }
            }
        }

        return $categoriesIDAndNameArray;
    }

    function make_store_category_id_name($resp) {
        $categoriesIDAndNameArray = array();
        if ($resp->Ack == 'Failure') {
            return $categoriesIDAndNameArray;
        }
        $categories = $resp->Store->CustomCategories;
        foreach ($categories->CustomCategory as $category) {
            $parent_name = $category->Name;
            $id = $category->CategoryID;
            if ($category->ChildCategory) {
                foreach ($category->ChildCategory as $childCategory) {
                    $id = $childCategory->CategoryID;
                    $child_name = $parent_name . " > " . $childCategory->Name;
                    if ($childCategory->ChildCategory) {
                        foreach ($childCategory->ChildCategory as $grandchildCategory) {
                            $id = $grandchildCategory->CategoryID;
                            $grandchild_name = $child_name . " > " . $grandchildCategory->Name;
                            $categoriesIDAndNameArray["$id"] = "$grandchild_name";
                        }
                    } else {
                        $categoriesIDAndNameArray["$id"] = "$child_name";
                    }
                }
            } else {
                $categoriesIDAndNameArray["$id"] = "$parent_name";
            }
        }
        return $categoriesIDAndNameArray;
    }

    function countFees($resp) {
        $information = "\n";
        $totalFees = 0.0;
        foreach ($resp->Fees->Fee as $fees) {
            $fee = trim($fees->Fee);
            if ($fee > 0) {
                $name = trim($fees->Name);
                $information .= $name . ': ' . $fee ."\n";
                $totalFees += $fee;
            }
        }
        $information .= $totalFees;
        return $information;
    }

    function get_gmt($args) {
        $hour = $args['hour'];
        $minute = $args['minute'];
        $date = $args['date'];
        $month = $args['month'];
        $year = $args['year'];
        $diff_hours = 7;

        return date("Y-m-d\TH:i:00.000\Z", mktime($hour, $minute, 0, $month, $date, $year)  + $diff_hours * 60 * 60);
    }

    function get_variations($post) {
        $variations = array();
        $variationSpecifics = array();
        $variationSpecificsSet = array();
        $variation_names = array();
        $variation_name_num = 4;
        $variation_num = 7;
        $variation_name_value_map = array(
            'variation_names_0' => 'var__',
            'variation_names_1' => 'var_a_',
            'variation_names_2' => 'var_b_',
            'variation_names_3' => 'var_c_'
        );

        for ($i = 0; $i < $variation_name_num; $i++) {
            if (!empty($post['variation_names_'.$i])) {
                $variation_names[] = 'variation_names_'.$i;
            }
        }
        if (count($variation_names) == 0) {
            return array();
        }
        for ($i = 1; $i <= $variation_num; $i++) {
            $variation_name_value_array = array();
            // sku - must
            $sku = $post['sku_'.$i];
            if (empty ($sku)) {
                continue;
            }
            // price - must
            $price = $post['price_'.$i];
            if (empty($price) && !is_numeric($price)) {
                continue;
            }
            // qnt - must
            $qnt = $post['qnt_'.$i];
            if (empty ($qnt) && !is_numeric($qnt)) {
                continue;
            }
            foreach ($variation_names as $variation_name) {
                $variation_value = $post[$variation_name_value_map[$variation_name].$i];
                if (empty ($variation_value)) {
                    continue;
                }
                $variation_name_value_array[] = array($post[$variation_name] => $variation_value);
            }
            if (count($variation_name_value_array) == 0) {
                continue;
            }
            $variationSpecifics[] = array(
                'sku' => $sku,
                'price' => $price,
                'qnt' => $qnt,
                'variation_name_value' => $variation_name_value_array
            );
        }

        foreach ($variationSpecifics as $variationSpecific) {
            $variation_name_value_array = $variationSpecific['variation_name_value'];
            foreach ($variation_name_value_array as $variation_name_value) {
                foreach ($variation_name_value as $name => $value) {
                    if (! array_key_exists($name, $variationSpecificsSet)) {
                        $variationSpecificsSet[$name] = array($value);
                    } else {
                        if (!in_array($value, $variationSpecificsSet[$name])) {
                            array_push($variationSpecificsSet[$name], $value);
                        }
                    }
                }
            }
        }
        $variations['variationSpecifics'] = $variationSpecifics;
        $variations['variationSpecificsSet'] = $variationSpecificsSet;

        return $variations;
    }
    function item_to_array($resp) {
        $data = array();
        if (!isset($resp->Ack) || $resp->Ack == 'Failure') {
            $data['ack'] = 'Failure';
            return $data;
        }

        $item = $resp->Item;
        $data['user_id'] = $item->Seller->UserID;
        $data['listing_type'] = $item->ListingType;
        $data['ack'] = 'Success';
        $data['site'] = $item->Site;
        $data['primary_category'] = $item->PrimaryCategory;
        $data['store_category_id'] = $item->Storefront->StoreCategoryID;
        $data['sku'] = $item->SKU;
        $data['title'] = $item->Title;
        $data['description'] = $item->Description;
        $data['quantity'] = $item->Quantity;
        $data['start_price'] = $item->StartPrice;
        $data['reserve_price'] = $item->ReservePrice;
        $data['buy_it_now_price'] = $item->BuyItNowPrice;
        $data['min_best_offer'] = $item->ListingDetails->MinimumBestOfferPrice;
        $data['auto_accept'] = $item->ListingDetails->BestOfferAutoAcceptPrice;
        $data['listing_duration'] = $item->ListingDuration;
        $data['pic_url'] = $item->PictureDetails->GalleryURL;
        if ($item->Variations) {
            $data['variations'] = parse_variations($item->Variations);
        } else {
            $data['variations'] = null;
        }

        $shippings = $item->ShippingDetails->ShippingServiceOptions;        
        $i = 0;
        foreach ($shippings as $shipping) {
            $data['shipping_'.$i] = $shipping;
            $i++;
        }
        $i_shippings = $item->ShippingDetails->InternationalShippingServiceOption;
        $i = 0;
        foreach ($i_shippings as $i_shipping) {
            $data['i_shipping_'.$i] = $i_shipping;
            $i++;
        }
        if (isset($item->DispatchTimeMax)) {
            $data['dispatch_time_max'] = $item->DispatchTimeMax;
        } else {
            $data['dispatch_time_max'] = null;
        }

        $data['return_policy'] = $item->ReturnPolicy;
        
        return $data;
    }

    function parse_variations($variations_obj) {
        $variations = array();
        
        foreach ($variations_obj->Variation as $variation) {
            $content = array('sku' => $variation->SKU, 'start_price' => $variation->StartPrice, 'quantity' => $variation->Quantity);

            $values = array();
            foreach ($variation->VariationSpecifics->NameValueList as $name_value) {
                $values[] = $name_value->Value;
            }

            $content['values'] = $values;
            $variations['content'][] = $content;
        }
        
        $names = array();
        foreach ($variations_obj->VariationSpecificsSet->NameValueList as $name_value) {
            $names[] = $name_value->Name;
        }
        $variations['names'] = $names;
        
        return $variations;
    }

    function wrap_description($descrition) {
        $pattern = '@<div[^>]+?>i000000</div>@i';
        if (preg_match($pattern, $descrition)) {
            return $descrition;
        }
        
        $wrapped_description = <<<DESC
<DIV align=center></DIV>
<DIV style="DISPLAY: none">i000000</DIV><!--eof InkFrogGalleryShowcaseFlash--><BR>
<P style="MARGIN: 5px 0px 0px 5px">
DESC;
        $wrapped_description .= $descrition;
        $wrapped_description .= <<<DESC
   </P>
DESC;
        return $wrapped_description;
    }

    function make_item_specifics_name_value($resp) {
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
    
    function fetch_ebay_competitor_price($url)
    {
        $product_pattern = '/<span.+?class="vi-is1-prcp".*?>(.*?)<\/span>/';
		$product_pattern1 = '/<span.+?class="vi-is1-prcd vi-is1-prcp".*?>(.*?)<\/span>/';
        $shipping_pattern = '/<span class="vi-is1-tese">(.*?)<\/span>/';
        $shipping_pattern2 = '/<span.+?id="fshippingCost".*?>(.*?)<\/span>/';
        $matches = array();
        $ebay_html = @ file_get_contents($url);

        $result = array();
        if ($ebay_html !== FALSE)
        {
            preg_match($product_pattern, $ebay_html, $matches);
            if (isset($matches[1]))
            {
                $product_price = preg_replace("/&#.*;/", "", $matches[1]);
                $product_price = preg_replace("/[^\d\.]/", "", str_replace(',','.',$product_price));
                $result['product'] = $product_price;
            }
            else
            {
				preg_match($product_pattern1, $ebay_html, $matches);
				if (isset($matches[1]))
				{
					$product_price = preg_replace("/&#.*;/", "", $matches[1]);
					$product_price = preg_replace("/[^\d\.]/", "", str_replace(',','.',$product_price));
					$result['product'] = $product_price;
				}else{
					return NULL;
				}
            }
			//print_r($result);
			//die();
            $matches = array();
            preg_match($shipping_pattern, $ebay_html, $matches);
            if (isset($matches[1]))
            {
                $shipping_price = preg_replace("/&#.*;/", "", $matches[1]);
                $shipping_price = preg_replace("/[^\d\.]/", "", str_replace(',','.',$shipping_price));
                $result['shipping'] = $shipping_price;
                $result['total'] = $result['product'] + $result['shipping'];
 
                return $result;
            }
            else // try another pattern
            {
                $matches = array();
                preg_match($shipping_pattern2, $ebay_html, $matches);
                if (isset($matches[1]))
                {
					$shipping_price_arr=explode('">',$matches[1]);
                    $shipping_price = preg_replace("/[^\d\.]/", "", str_replace(',','.',$shipping_price_arr[1]));
					//var_dump($shipping_price);
                    $result['shipping'] = $shipping_price;
                    $result['total'] = $result['product'] + $result['shipping'];
 
                    return $result;
                }
                else
                {
                    return NULL;
                }
            }
        }
        return NULL;
    }

    function test() {
        echo VERSION;
    }
?>
