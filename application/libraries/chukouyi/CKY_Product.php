<?php
require_once APPPATH . 'libraries/chukouyi/CKY_Basic' . EXT;

class Product{
    public $Catetory;
    public $Intro;
    public $Title;
    public $Flag;
    public $Packing;
    public $Weight;
    public $Warning;
}

class ProductAdd {
    public $product;
    public $key;
}

class Instock {
    public $Sign;
    public $LogType;
    public $StorageCode;
    public $ArriveTime;
    public $Locale;
    public $Remark;
    public $IsCollect;
    public $CollectTime;
    public $CollectAddress;
    public $CollectContact;
    public $CollectPhone;
}

class InstockAdd {
    public $instore;
    public $key;
}

class InStoreCase {
    public $CaseNo;
    public $Weight;
    public $Packing;
}

class InStoreCaseAdd {
    public $CaseList;
    public $OrderNo;
    public $key;
}

class InStoreProduct {
    public $CaseNo;
    public $Title;
    public $Quantity;
    public $DeclaredName;
    public $DeclaredValue;
}

class InStoreProductAdd {
    public $ProductList;
    public $OrderNo;
    public $key;
}

class InStoreSubmit {
    public $orderSign;
    public $key;
}

class GetStock {
    public $title;
    public $storageCode;
    public $key;
}

class GetProductList {
    public $pageIndex;
    public $pageSize;
    public $key;
}


class CKY_Product extends CKY_Basic 
{
    public function __construct() {
        parent::__construct();        
    }
    
    public function product_add($data)
    {
        $productAdd = new ProductAdd;
        $product = new Product;
        $product->Catetory = $data['Catetory'];
        $product->Intro = $data['Intro'];
        $product->Title = $data['Title'];
        $product->Flag = $data['Flag'];
        $product->Packing = $data['Packing'];
        $product->Weight = $data['Weight'];
        $product->Warning = $data['Warning'];
        
        $productAdd->product = $product;
        $productAdd->key = $this->key;
        
        echo '<pre>';
        echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';
        try {
            $client = new SoapClient($this->product_gateway_url);

            $response = $client->ProductAdd($productAdd);
            
            var_dump($response);
        } catch (SOAPFault $exception) {
            var_dump($exception);
        }
    }
    
    public function instore_add($data)
    {
        $instock = new Instock();
        $instock->LogType = $data['LogType'];
        $instock->StorageCode = $data['StorageCode'];
        $instock->ArriveTime = $data['ArriveTime'];
        $instock->Locale = $data['Locale'];
        $instock->Remark = $data['Remark'];
        $instock->IsCollect = $data['IsCollect'];
        $instock->CollectTime = $data['CollectTime'];
        $instock->CollectAddress = $data['CollectAddress'];
        $instock->CollectContact = $data['CollectContact'];
        $instock->CollectPhone = $data['CollectPhone'];
        
        $instock_add = new InstockAdd;
        $instock_add->instore = $instock;
        $instock_add->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->InStoreAdd($instock_add);   
            $result = $response->InStoreAddResult;
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
    
    public function instore_case_add($data)
    {
        $instock = new InStoreCase();
        $instock->CaseNo = $data['CaseNo'];
        $instock->Weight = $data['Weight'];
        $instock->Packing = $data['Packing'];
        
        $instock_case_add = new InStoreCaseAdd;
        $instock_case_add->CaseList = array($instock);
        $instock_case_add->OrderNo = $data['OrderNo'];
        $instock_case_add->key = $this->key;
       
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->InStoreCaseAdd($instock_case_add);
            $result = $response->InStoreCaseAddResult;
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
    
    public function instore_product_add($datas, $order_sign)
    {
        $instocks = array();
        foreach ($datas as $data)
        {
            $instock = new InStoreProduct();
            $instock->CaseNo = $data['CaseNo'];
            $instock->DeclaredName = $data['DeclaredName'];
            $instock->DeclaredValue = $data['DeclaredValue'];
            $instock->Title = $data['Title'];
            $instock->Quantity = $data['Quantity'];
            $instocks[] = $instock;
        }
        $instock_product_add = new InStoreProductAdd;
        $instock_product_add->ProductList = $instocks;
        $instock_product_add->OrderSign = $order_sign;
        $instock_product_add->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->InStoreProductAdd($instock_product_add);
            
            $result = $response->InStoreProductAddResult;
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

    public function instore_submit($data)
    {
        $instock_submit = new InStoreSubmit;
        $instock_submit->orderSign = $data['OrderNo'];
        $instock_submit->key = $this->key;
        
        try {
            $client = new SoapClient($this->order_gateway_url);

            $response = $client->InStoreSubmit($instock_submit);            
            
            $result = $response->InStoreSubmitResult;
            
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
    
    public function get_stock_info_by_sku($data)
    {
        $get_stock = new GetStock();
        $get_stock->title          = $data['sku'];
        $get_stock->storageCode    = $data['storage_code'];
        $get_stock->key            = $this->key;
        
        try 
        {
            $client = new SoapClient($this->product_gateway_url);

            $response = $client->GetStock($get_stock);
            
            $result = $response->GetStockResult;
            
            if ($result->Success)
            {
                return array(
                    'title' => $result->Result->Title,
                    'storage_no' => $result->Result->StorageNo,
                    'amount' => $result->Result->Amount,
                    'amount_out' => $result->Result->AmountOut,
                    'weight' => $result->Result->Weight,
                    'packing' => $result->Result->Packing,
                );
            }
            else
            {
                return array('status' => FALSE, 'message' => $result->Message);
            }            
        } catch (SOAPFault $exception) {
            return FALSE;
        } 
    }
    
    public function get_product_list($data)
    {
                
        $getProductList = new GetProductList();
        $getProductList->pageIndex   = $data['page_index'];
        $getProductList->pageSize    = $data['page_size'];
        $getProductList->key         = $this->key;
        
        echo '<pre>';
        echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';

        try 
        {
            $client = new SoapClient($this->product_gateway_url);

            $response = $client->GetProductList($getProductList);
            
            var_dump($response);
            
//            $result = $response->GetStockResult;
            
//            if ($result->Success)
//            {
//                return array(
//                    'title' => $result->Result->Title,
//                    'storage_no' => $result->Result->StorageNo,
//                    'amount' => $result->Result->Amount,
//                    'amount_out' => $result->Result->AmountOut,
//                    'weight' => $result->Result->Weight,
//                    'packing' => $result->Result->Packing,
//                );
//            }
//            else
//            {
//                return array('status' => FALSE, 'message' => $result->Message);
//            }            
        } catch (SOAPFault $exception) {
            return FALSE;
        } 
    }
    
}

?>
