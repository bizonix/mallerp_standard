<?php
require_once APPPATH.'controllers/sale/myebay'.EXT;

class Auto_ebay extends Myebay 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ebay_model');
        $this->load->helper('ebay');
    }
    
    public function track_ebay_competitors()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'track_ebay_competitors.php') === FALSE)
        {
            exit;
        }
        $competitors = $this->ebay_model->fetch_all_competitors();        
        
        $ebay_ids_reset = array();
        foreach ($competitors as $competitor)
        {
            $item_id = $competitor->item_id;
            if ( ! in_array($item_id, $ebay_ids_reset))
            {
                // reset ebay alarm                
                $this->ebay_model->update_ebay_by_item_id($item_id, array('alarm' => -1));
                $ebay_ids_reset[] = $item_id;
            }
            $this->_proccess_track_ebay_competitor($competitor->id);
        }
    }
    
    public function track_ebay_competitor($id)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'track_ebay_competitor.php') === FALSE)
        {
            exit;
        }
        $this->_proccess_track_ebay_competitor($id);
    }
    
    private function _proccess_track_ebay_competitor($id)
    {
        $ebay_url_base = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
        $competitor = $this->ebay_model->fetch_competitor_by_id($id);
        $item_id = $competitor->item_id;
		//$item_id = 270801805639;
        $ebay_item = $this->ebay_model->fetch_ebay_item_by_item_id($item_id);
		if($ebay_item->currency=='EUR'){
			$ebay_url_base = 'http://www.ebay.de/itm/ws/eBayISAPI.dll?ViewItem&item=';
		}
		if($ebay_item->currency=='GBP'){
			$ebay_url_base = 'http://www.ebay.co.uk/itm/ws/eBayISAPI.dll?ViewItem&item=';
		}
		if($ebay_item->currency=='AUD'){
			$ebay_url_base = 'http://www.ebay.com.au/itm/ws/eBayISAPI.dll?ViewItem&item=';
		}
        if (empty($ebay_item))
        {
            //$this->ebay_model->drop_competitor_by_item_id($item_id);
            return;
        }
        $competitor_result = fetch_ebay_competitor_price($competitor->url);
        if (empty($competitor_result))
        {
            return;
        }
        $our_result = fetch_ebay_competitor_price($ebay_url_base . $ebay_item->item_id);
        if (empty($our_result))
        {
            return;
        }
		//var_dump($our_result);die();

        $price = $competitor_result['total'];
        $our_price = $our_result['total'] + $competitor->allowed_difference;
        if ($our_price > $price)
        {
            $balance = $price - $our_price;
            $status = 1;
        }
        else
        {
            $balance = '+' . ($price - $our_price);
            $status = 0;
        }

        $competitor_data = array(
            'balance'       => $balance,
            'status'        => $status,
            'track_time'    => get_current_time(),
        );
        $this->ebay_model->update_competitor($competitor->id, $competitor_data);
        var_dump($competitor_data);
        /*
         * warn our saler
         */            
        if ($status && $ebay_item->alarm != 1)
        {
            $this->ebay_model->update_ebay_by_item_id($item_id, array('alarm' => 1));
        }
        /**
         * update our item price.
         **/
        $this->ebay_model->update_ebay_by_item_id(
            $item_id, 
            array(
                'price' => $our_result['product'], 
                'shipping_price' => $our_result['shipping'],
            )
        );
        if ($status)
        {
            $click_url = $competitor->url;
            $saler_ids = $this->ebay_model->fetch_saler_ids_by_ebay_id($ebay_item->ebay_id);
            var_dump($saler_ids);
            $this->events->trigger(
                'ebay_item_price_too_high',
                array(
                    'type'             => 'ebay_item_price_too_high_notify',
                    'click_url'        => $click_url,
                    'content'          => sprintf(lang('competitor_has_lower_price'), $competitor->seller_id),
                    'owner_id'         => 0,
                    'allowed_user_ids' => $saler_ids,
                )
            );
        }
    }

    public function mark_order_shipped($order_id)
    {
        $order = $this->order_model->get_order($order_id);
    }

    private function __process_mark_order_shipped()
    {
    }
}

?>
