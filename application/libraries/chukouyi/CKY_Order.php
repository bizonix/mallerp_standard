<?php
require_once APPPATH . 'libraries/chukouyi/CKY_Basic' . EXT;


class OutstoreAdd {
    public $outstore;
    public $key;
}

class Outstore {
    public $Sign;
    public $StorageCode;
    public $Remark;
}

class OutStoreProductAdd {
    public $ProductList;
    public $orderSign;
    public $key;
}

class OutStoreProduct {
    public $Title;
    public $Quantity;
    public $TransactionID;
    public $Consignee;
    public $ShippingAddress;
    public $AddressLine1;
    public $AddressLine2;
    public $Phone;
    public $City;
    public $Province;
    public $Country;
    public $PostCode;
    public $Shipping;
    public $Service;
    public $Remark;
}

class OutStorePackage {
    public $TransactionID;
    public $Consignee;
    public $ShippingAddress;
    public $AddressLine1;
    public $AddressLine2;
    public $Phone;
    public $City;
    public $Province;
    public $Country;
    public $PostCode;
    public $Shipping;
    public $Service;
    public $Remark;
}

class OutStorePackageAdd {
    public $pack;
    public $orderSign;
    public $key;
}

class OutStorePackProduct {
    public $Sign;
    public $Title;
    public $Quantity;
}

class OutStorePackageProductAdd {
    public $ProductList;
    public $packSign;
    public $orderSign;
    public $key;
}

class OutStoreSubmit {
    public $orderSign;
    public $key;
}

class OutStoreProductList {
    public $orderSign;
    public $key;
}


class CKY_Order extends CKY_Basic 
{
    public function __construct() {
        parent::__construct();        
    }
    
    public function outstore_add($data)
    {
        $outstock = new Outstore();
        $outstock->StorageCode = $data['StorageCode'];
        $outstock->Remark = $data['Remark'];
        
        $outstock_add = new OutstoreAdd;
        $outstock_add->outstore = $outstock;
        $outstock_add->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->OutStoreAdd($outstock_add);
            $result = $response->OutStoreAddResult;            
            
            if ($result->Success)
            {
                return array('status' => TRUE, 'order_sign' => $result->Result);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }
        } catch (SOAPFault $exception) {
            
            return FALSE;
        }
    }
    
    public function outstore_product_add($data)
    {
        $outstock = new OutStoreProduct();
        $outstock->Title = $data['Title'];
        $outstock->Quantity = $data['Quantity'];
        $outstock->TransactionID = $data['TransactionID'];
        $outstock->AddressLine1 = $data['AddressLine1'];
        $outstock->AddressLine2 = $data['AddressLine2'];
        
        $outstock->Consignee = $data['Consignee'];
        $outstock->Phone = $data['Phone'];
        $outstock->City = $data['City'];
        $outstock->Province = $data['Province'];
        $outstock->Country = $data['Country'];
        $outstock->PostCode = $data['PostCode'];
        $outstock->Shipping = $data['Shipping'];
        $outstock->Service = $data['Service'];
        $outstock->Remark = $data['Remark'];
        
        $outstock_product_add = new OutStoreProductAdd;
        $outstock_product_add->ProductList = array($outstock);
        $outstock_product_add->orderSign = $data['OrderNo'];
        $outstock_product_add->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->OutStoreProductAdd($outstock_product_add);
            $result = $response->OutStoreProductAddResult;            
            
            if ($result->Success)
            {
                return array('status' => TRUE);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }
        } catch (SOAPFault $exception) {
            return FALSE;
        }    
    }    
    
    public function outstore_package_add($data)
    {
        $outstore_package = new OutStorePackage();
        $outstore_package->TransactionID = $data['TransactionID'];
        $outstore_package->AddressLine1 = $data['AddressLine1'];
        $outstore_package->AddressLine2 = $data['AddressLine2'];
        
        $outstore_package->Consignee = $data['Consignee'];
        $outstore_package->Phone = $data['Phone'];
        $outstore_package->City = $data['City'];
        $outstore_package->Province = $data['Province'];
        $outstore_package->Country = $data['Country'];
        $outstore_package->PostCode = $data['PostCode'];
        $outstore_package->Shipping = $data['Shipping'];
        $outstore_package->Service = $data['Service'];
        $outstore_package->Remark = $data['Remark'];
        
        $outstore_package_add = new OutStorePackageAdd;
        $outstore_package_add->pack = $outstore_package;
        $outstore_package_add->orderSign = $data['OrderNo'];
        $outstore_package_add->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->OutStorePackageAdd($outstore_package_add);
            $result = $response->OutStorePackageAddResult;            
            
            if ($result->Success)
            {
                return array('status' => TRUE, 'order_sign' => $result->Result);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }            
        } catch (SOAPFault $exception) {
            return TRUE;
        }    
    }
    
    public function outstore_package_product_add($datas, $package_sign, $order_sign)
    {
        $products = array();
        foreach ($datas as $data)
        {
            $outstock = new OutStorePackProduct();
            $outstock->Title = $data['Title'];
            $outstock->Quantity = $data['Quantity'];
            $outstock->Sign = $package_sign;
            $products[] = $outstock;
        }
        
        $outstock_package_product_add = new OutStorePackageProductAdd;
        $outstock_package_product_add->ProductList = $products;
        $outstock_package_product_add->packSign = $package_sign;
        $outstock_package_product_add->orderSign = $order_sign;
        $outstock_package_product_add->key = $this->key;       
        
        try {
            $client = new SoapClient($this->order_gateway_url);
            $response = $client->OutStorePackageProductAdd($outstock_package_product_add);            
            
            $result = $response->OutStorePackageProductAddResult;
            if ($result->Success)
            {
                return array('status' => TRUE);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }            
        } catch (SOAPFault $exception) {
            return FALSE;
        }    
    }    
    
    public function outstore_submit($data)
    {        
        $outstock_submit = new OutStoreSubmit;
        $outstock_submit->orderSign = $data['OrderNo'];
        $outstock_submit->key = $this->key;
                
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->OutStoreSubmit($outstock_submit);
            
            $result = $response->OutStoreSubmitResult;
            if ($result->Success)
            {
                return array('status' => TRUE);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }          
        } catch (SOAPFault $exception) {
            return FALSE;
        }
    }
        
    public function outstore_product_list($data)
    { 
        $outstore_product_list = new OutStoreProductList();
        $outstore_product_list->orderSign = $data['order_no'];
        $outstore_product_list->key = $this->key;
         
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->OutStoreProductList($outstore_product_list);

            $result = $response->OutStoreProductListResult;

            if ($result->Success)
            {
                $arr = array();
                
                $outstore_product_object_arr = $result->Result->OutStoreProduct;
                if ( ! is_array($outstore_product_object_arr)) {
                    $outstore_product_object_arr = array($outstore_product_object_arr);
                }
                foreach ($outstore_product_object_arr as $outstore_product) 
                {
                    $arr[$outstore_product->TransactionID] = array(
                        'state'         => $outstore_product->State, 
                        'track_number'  => $outstore_product->TrackingNumber
                    ); 
                }

                return array('status' => TRUE, 'result' => $arr);
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }  
            
        } catch (SOAPFault $exception) {
            return FALSE;
        }
    }        
}

?>
