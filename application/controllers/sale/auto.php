<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Auto extends Mallerp_no_key
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sale_model');
        $this->load->model('order_model');
        $this->load->model('myinfo_model');
        $this->load->model('product_model');
        $this->load->model('product_netname_model');
        $this->load->model('stock_model');
        $this->load->model('purchase_statistics_model');
        $this->load->helper('order');
    }

    public function calculate_all_order_profit_rates()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'calculate_all_order_profit_rates.php') === FALSE)
        {
            exit;
        }
        $orders = $this->sale_model->fetch_all_orders_for_profit_rate();

        foreach ($orders as $order)
        {
            calculate_order_profit_rate($order->id);
        }
        
        echo 'Done!';
    }

    public function calculate_order_profit_rate($order_id)
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'calculate_order_profit_rate.php') === FALSE)
        {
            exit;
        }
        calculate_order_profit_rate($order_id);
        echo "Done!\n";
    }

    
    public function customer_second_glance_rate_check() //月顾客回头率 $user_amount表示sales总销售额,$grance_amount表示回头顾客销售额
    {
        set_time_limit(0);
        $year = date('Y');
        $month = date('m', strtotime('-1 month'));
        $now = get_current_time();
        $rates = $this->order_model->fetch_currency();
        foreach ($rates as $rate)
        {
            $cur_rates[$rate->name_en] = $rate->ex_rate;
        }
        $user_amount = array();
        $saler_ids = $this->purchase_statistics_model->fetch_saler_id();

        foreach ($saler_ids as $saler_id) //根据saler_id来遍历
        {
            $cur_m_total_amount = $this->purchase_statistics_model->fetch_cur_month_sales_total_amount($saler_id->saler_id);
            $amount_t = "";
            foreach($cur_m_total_amount as $total)
            {
                $amount_t = $amount_t + $cur_rates[$total->currency] * $total->total_amount;
            }
            if(empty($amount_t))
            {
                $amount_t="0";
            }
            $user_amount[$saler_id->saler_id] = $amount_t;
            var_dump($saler_id);
        }

        var_dump($user_amount);

        foreach ($saler_ids as $saler_id)
        {
            $buyer_ids = $this->purchase_statistics_model->fetch_buyer_id_in_saler_id($saler_id->saler_id);
            if(empty($buyer_ids))
            {
               $grance_amount[$saler_id->saler_id] = "0";
               continue;
            }
            $saler_amount_t = "";
            foreach ($buyer_ids as $buyer_id)
            {
                $chk_ksk = $this->purchase_statistics_model->check_second_ksk_in_past_six_month($buyer_id->buyer_id);
                
                if(count($chk_ksk)>0)
                {

                    $saler_amounts = $this->purchase_statistics_model->fetch_amount_by_buyer_id($buyer_id->saler_id,$buyer_id->buyer_id);
                    if(!isset($chk_buyer_id))
                    {
                        $chk_buyer_id = "";
                    }
                    if($buyer_id->buyer_id == $chk_buyer_id)
                    {
                        continue;
                    }
                    foreach ($saler_amounts as $amount)
                    {
                        $saler_amount_t = $saler_amount_t  + $cur_rates[$amount->currency] * $amount->gross;
                    }
                    $chk_buyer_id = $buyer_id->buyer_id;
                    $grance_amount[$buyer_id->saler_id] = $saler_amount_t;
                }
                else
                {
                    $grance_amount[$buyer_id->saler_id] = "0";
                }
                var_dump($buyer_id);
            }
        }

        foreach ($user_amount as $key_saler_id => $month_total_amount)
        {
            $where = array(
                'saler_id'      => $key_saler_id,
                'year'          => $year,
                'month'         => $month,
            );
            if ($this->stock_model->check_customer_second_glance_rate_exists($where))
            {
                continue;
            }
            if($month_total_amount == "0")
            {
                $rate = 0;
            }
            else
            {
                $rate = $grance_amount[$key_saler_id]/$month_total_amount;
            }
            $rate = number_format($rate,'4','.','');
            $data = array(
                'year'                 => $year,
                'month'                => $month,
                'second_glance_amount' => $grance_amount[$key_saler_id],
                'totable_amount'       => $month_total_amount,
                'second_glance_rate'   => $rate,
                'saler_id'             => $key_saler_id,
                'created_date'         => $now,
             );
           $this->stock_model->save_customer_second_glance_rate($data);
        }

    }
    
    /**
     * check_netname_sku 
     * 
     * check net name sku from the ebay page
     *
     * @param string $sku 
     * @param string $item_id 
     * @access public
     * @return void
     */
    public function check_netname_sku($netname_id)
    {
        $netname = $this->product_netname_model->fetch_netname($netname_id);
        if (empty($netname))
        {
            return;
        }

        $sku = $netname->sku;
        $item_id = $netname->item_id;

        if (empty($sku) OR empty($item_id))
        {
            return;
        }
        $ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=' . $item_id;
        $sku = str_replace(',', '+', $sku);
        $pattern = "/\s+($sku)(?:\s+[^(<\/div>)]+)?\s*<\/div>/i";
        
        echo $ebay_url, "\n";

        $ebay_html = @ file_get_contents($ebay_url);

        var_dump($ebay_html);

        $matches = array();
        $checked = 0;
        if ($ebay_html !== FALSE)
        {
            preg_match($pattern, $ebay_html, $matches);

            echo "$pattern\n";
            var_dump($matches);

            if (isset($matches[1]))
            {
                $checked = 1;
                echo "Yes\n";
            }
            else
            {
                $checked = -1; // sku failed.
            }
        }
        if ($checked == -1)
        {
            $message = sprintf(lang('netname_sku_check_failed_info'), $netname->net_name, $netname->sku, $ebay_url);
            $data = array(
                'message'       => $message,
                'creator'       => lang('program'),
                'group'         => array('sale'),
            );
            $this->myinfo_model->add_important_message($data);
        }
        if ($checked != 0)
        {
            $data = array(
                'checked'   => $checked,
            );
            $this->product_netname_model->update_netname($netname_id, $data);
        }
    }
}

?>
