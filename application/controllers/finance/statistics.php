<?php
require_once APPPATH.'controllers/finance/finance'.EXT;

class Statistics extends Finance
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('accounting_cost_model');
		$this->load->model('ebay_order_model');
    }
    
    public function product_stock_count()
    {
        $this->load->library('excel');
        $result = $this->accounting_cost_model->fetch_all_products();
        
        $head = array(
            'SKU',
            '中文名称',
            '商品分类',
            '市场型号',
            '货舱号',
            '货架号',
            '库存数量',
            '样品价',
			'采购员',
            '更新时间',
            '上次盘点时间',
        );
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                $row->sku,
                $row->p_name_cn,
                $row->c_name_cn,
                $row->market_model,
                $row->stock_code,
                $row->shelf_code,
                $row->stock_count,
                $row->price,
				($row->purchaser_id>0)?fetch_user_name_by_id($row->purchaser_id):" ",
                $row->updated_date,
                $row->stock_check_date,
            );
        }

        $this->excel->array_to_excel($data, $head, '_' . date('Y-m-d'));
    }

    public function order_cost_statistics()
    {
        if ( ! $this->input->is_post())
        {
            $begin_time = date('Y-m-d 00:00:00');
            $end_time = date('Y-m-d 24:00:00');
            $current_auction_site = NULL;
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $current_auction_site = $this->input->post('auction_site');
        }

        $orders = $this->accounting_cost_model->fetch_all_cost_order_by_time($begin_time, $end_time, $current_input_user);
        $input_user_object = $this->accounting_cost_model->fetch_all_input_users_by_time($begin_time, $end_time);

        $input_users = array('' => lang('all'));
        foreach ($input_user_object as $row)
        {
            $input_users[$row->input_user] = $row->input_user;
        }
        
        $countries = array(
			'Germany'     => 0,
            'United States'     => 0,
			'United Kingdom'    => 0,
            'Australia'         => 0,
            'Canada'            => 0,
            'Russia'            => 0,
            'Others'            => 0,
        );
        $product_total_cost = 0;
        $total_shipping_cost = 0;
        $shipping_cost_by_code = array();
        $shipping_count_by_code = array();
        $total_revenue = 0;
        $revenue_by_currency = array();
        $total_paypal_cost = 0;
		$total_ebay_cost = 0;
        $paypal_by_currency = array();
		$ebay_by_currency = array();
        foreach ($orders as $order)
        {
            // calculate orders by countries.
            if (in_array($order->country, array_keys($countries)))
            {
                $countries[$order->country]++;
            }
            else
            {
                $countries['Others']++;
            }

            $product_total_cost += $order->product_cost_all;

            // shipping cost
            $total_shipping_cost += $order->shipping_cost;
            $is_register = strtoupper(trim($order->is_register));
            $is_register = str_replace('-', '', $is_register);
            if (isset($shipping_cost_by_code[$is_register]))
            {
                $shipping_cost_by_code[$is_register] += $order->shipping_cost;
                $shipping_count_by_code[$is_register]++;
            }
            else
            {
                $shipping_cost_by_code[$is_register] = $order->shipping_cost;
                $shipping_count_by_code[$is_register] = 1;
            }

            // revenue
            $revenue = $order->net;
            if (isset($revenue_by_currency[$order->currency]))
            {
                $revenue_by_currency[$order->currency] += $revenue;
            }
            else
            {
                $revenue_by_currency[$order->currency] = $revenue;
            }
            
            // paypal cost
            if (isset($paypal_by_currency[$order->currency]))
            {
                $paypal_by_currency[$order->currency] += $order->fee;
            }
            else
            {
                $paypal_by_currency[$order->currency] = $order->fee;
            }
			// ebay cost
			$ebay_orders=$this->ebay_order_model->fetch_ebay_order_by_paypal($order->transaction_id);
			foreach($ebay_orders as $ebay_order)
			{
				if (isset($ebay_by_currency[$ebay_order->fvf_currency]))
				{
					$ebay_by_currency[$ebay_order->fvf_currency] += $ebay_order->final_value_fee;
				}
				else
				{
					$ebay_by_currency[$ebay_order->fvf_currency] = $ebay_order->final_value_fee;
				}
			}
            
			
        }
        $order_count = count($orders);
        
        $data = array(
            'begin_time'            => $begin_time,
            'end_time'              => $end_time,
            'order_count'           => $order_count,
            'countries'             => $countries,
            'product_total_cost'    => $product_total_cost,
            'total_shipping_cost'   => $total_shipping_cost,
            'shipping_cost_by_code' => $shipping_cost_by_code,
            'shipping_count_by_code'=> $shipping_count_by_code,
            'revenue_by_currency'   => $revenue_by_currency,
            'paypal_by_currency'    => $paypal_by_currency,
            'input_users'           => $input_users,
            'current_auction_site'    => $current_auction_site,
			'ebay_by_currency'      => $ebay_by_currency,
        );
        
        $this->template->write_view('content', 'finance/order_cost_statistics', $data);
        $this->template->render();
    }
}

?>
