<?php
require_once APPPATH.'controllers/finance/finance'.EXT;

class Accounting_cost extends Finance
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
		$this->load->model('product_model');
        $this->load->model('accounting_cost_model');
        $this->load->model('shipping_company_model');
        $this->load->model('shipping_function_model');
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_subarea_group_model');
        $this->load->model('shipping_type_model');
        $this->load->helper('shipping_helper');
        $this->load->helper('order_helper');
        $this->load->library('excel');
    }

    public function manage()
    {
        $this->enable_search('accounting_cost');
        $this->enable_sort('accounting_cost');

        $orders = $this->accounting_cost_model->fetch_all_costs();
        $data = array(
            'orders' => $orders,
        );
        $this->template->write_view('content', 'finance/cost_management', $data);
        $this->template->add_js('static/js/ajax/finance.js');
        $this->template->render();
    }
    
    public function view_list()
    {
        $this->enable_search('accounting_cost');
        $this->enable_sort('accounting_cost');

        $orders = $this->accounting_cost_model->fetch_costs_by_cost_user();
        $data = array(
            'orders' => $orders,
            'action' =>'cost_view_list'
        );
        $this->template->write_view('content', 'finance/cost_management', $data);
        $this->template->add_js('static/js/ajax/finance.js');
        $this->template->render();
    }

    public function save_accounting_costs()
    {
        $order_count = $this->input->post('order_count');
        $user_id = get_current_user_id();

        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('order_id_' . $i);
            $shipping_cost = trim($this->input->post('shipping_cost_' . $i));
            $product_cost = trim($this->input->post('product_cost_' . $i));
            $product_cost_string = trim(trim($this->input->post('product_cost_string_' . $i)),',');

            if ( ! is_numeric($shipping_cost) ||  ! is_numeric($product_cost) || $shipping_cost <= 0 || $product_cost <= 0 )
            {
                continue;
            }
            try {
                $data = array(
                    'cost_user' => $user_id,
                    'cost_date' => get_current_time(),
                    'cost' => $shipping_cost + $product_cost,
                    'shipping_cost' => $shipping_cost,
                    'product_cost_all' => $product_cost,
                    'product_cost' => $product_cost_string,
                );

                $this->order_model->update_order_information($order_id, $data);
                calculate_order_profit_rate($order_id);

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        echo $this->create_json(1, lang('stock_check_or_count_successfully'));
    }
    
    public function download_order_info()
    {
        $head = array(
            lang('item_number'),
            lang('ship_remark'),
            lang('customer_remark'),
            lang('is_register'),
            lang('track_number'),
            lang('total_weight'),
            lang('shipping_cost'),
            lang('total_cost'),
            lang('total_profit_rate'),
            lang('product_information'),
            lang('ship_confirm_date'),
            lang('receipt'),
            lang('transaction_number'),
            lang('input_user'),
            lang('cost_date'),
        );
        
        if (!$this->input->is_post()) {
            return;
        }
        
        $order_ids_str = trim($this->input->post('order_ids_str'), ',');
        
        if(empty ($order_ids_str))
        {
            $content = lang('select_one_download');
            echo "<script >alert('$content'); history.back();</script>";
            return;
        }
        
        $order_ids = array();

        foreach (explode(',', $order_ids_str) as $id) {
                $order_ids[] = $id;
        }

        $orders = $this->accounting_cost_model->fetch_costs_by_cost_user_to_array($order_ids);
        
        $show_data = array();

        foreach ($orders as $order)
        {
            $receipt = $order->currency . ' : ' . $order->net && $order->net != 0 ? $order->net : $order->gross;
            
            $item_ids = explode(',', $order->item_id_str);
            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
            $item_title = explode(',', $order->item_title_str);
            $product_cost_string = explode(',', $order->product_cost);

            $count = count($skus);

            $product_information = '';
            $product_name = '';

            $total_product_cost = 0;
            
            for ($i = 0; $i < $count; $i++)
            {
                $item_id = element($i, $item_ids);

                $title = element($i, $item_title);

                $product_information .= $title . '      ';

                if ($item_id)
                {
                    $product_information .= "Item ID: $item_id      ";
                }
                $purchaser_name = '';
                if (isset($purchasers[$skus[$i]]))
                {
                    $purchaser_name = $purchasers[$skus[$i]];
                }
                else
                {
                    $purchaser_name = get_purchaser_name_by_sku($skus[$i]);
                    $purchasers[$skus[$i]] = $purchaser_name;
                }

                $product_cost = get_cost_by_sku($skus[$i]);
                if ($product_cost == 0)
                {
                    $product_cost = '';
                }

                $price_html = $product_cost;


                if(element($i, $product_cost_string))
                {
                    $cost =$product_cost_string[$i];
                }
                else
                {
                    $cost = $product_cost;
                }
//                if(isset ($action) && $action =='cost_view_list')
//                {            
                    $price_html =$cost;        
//                }
                $total_product_cost += $cost*element($i, $qties);

                $product_information .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . '      ' . $purchaser_name . '      ' . lang('cost_price') . ' : '.$price_html;
            }

            $default = isset ($product_cost_string[$count])?$product_cost_string[$count]:'0.65';

            $product_information .= '      ' . lang('other_cost_price') . ' : ' . $default;

            $top = $order->product_cost_all == 0 ? $total_product_cost : $order->product_cost_all;

            $product_information .= '      ' . lang('total_cost') . ' : ' . $top;
            
            $show_data[] = array(
                'item_number'           =>$order->item_no,
                'ship_remark'           =>$order->ship_remark,
                'customer_remark'       =>$order->descript,
                'is_register'           =>$order->is_register,
                'track_number'          =>$order->track_number,
                'total_weight'          =>$order->ship_weight,
                'shipping_cost'         =>$order->shipping_cost,
                'total_cost'            =>$top,
                'total_profit_rate'     =>$order->profit_rate,
                'product_information'   =>$product_information,
                'ship_confirm_date'     =>$order->ship_confirm_date,
                'receipt'               =>$receipt."($order->currency)",
                'transaction_number'    =>$order->transaction_id,
                'input_user'            =>$order->input_user,
                'cost_date'             =>$order->cost_date,
            );
        }
        
        $this->excel->array_to_excel($show_data, $head, 'Order_info_' . date('Y-m-d'));
    }
}
?>
