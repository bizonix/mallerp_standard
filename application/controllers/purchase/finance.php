<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Finance extends Purchase
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_finance_model');
        $this->load->model('purchase_order_model');       
        $this->load->model('product_model');
        $this->load->model('user_model');
        $this->load->helper('purchase_order');
        $this->load->library('form_validation');
    }

    public function finance_pending()
    {
        $this->enable_search('finance_pending');
        $this->enable_sort('finance_pending');
        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $pending_orders = $this->purchase_finance_model->fetch_all_pending_order();
        $payment_states = $this->purchase_finance_model->fetch_all_payment_states();

        $payment_types = fetch_statuses('payment_type');
        $types = array('' => lang('please_select'));
        foreach ($payment_types as $key => $name)
        {
            $types[$key] = lang($name);
        }
        $payment_types = $types;


        $data = array(
            'pending_orders'     => $pending_orders,
            'purchase_users'     => $purchase_users,
            'payment_states'     => $payment_states,
            'payment_types'      => $payment_types,
        );
        $this->template->write_view('content', 'purchase/finance/finance_pending', $data);
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }

    public function review_order($purchase_order_id)
    {
        if(!empty($purchase_order_id))
        {
            $data = array(
                'payment_state'  => '1',
            );
           $skus_object = $this->purchase_order_model->fetch_all_sku_by_purchase_order_id($purchase_order_id);
           foreach ($skus_object as $row)
           {
                $count = on_way_count($row->sku);

                $data_product = array(
                    'on_way_count' => $count,
                );

                $this->product_model->update_product_by_sku($row->sku, $data_product);
           }
		   $this->purchase_order_model->review_reject_order($purchase_order_id, $data);
           $this->finance_pending();
        }
    }

    public function update_payment_cost()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $item_cost = $this->purchase_finance_model->fetch_purchase_order($id)->item_cost;     
        $costs = $this->purchase_finance_model->fetch_payment_cost($id);
        $payment_costs = isset($costs->payment_cost) ? $costs->payment_cost : '0';
        $total_costs = price($payment_costs + $value);
        try
        {
			$payment_state = $this->purchase_finance_model->fetch_purchase_order($id)->payment_state;
			if($payment_state==0){
				$skus_object = $this->purchase_order_model->fetch_all_sku_by_purchase_order_id($id);
				foreach ($skus_object as $row)
				{
                	$count = on_way_count($row->sku);

                	$data_product = array(
                    	'on_way_count' => $count,
                	);

                	$this->product_model->update_product_by_sku($row->sku, $data_product);
				}
			}
            if ( ! is_numeric($value) ||  $value < 0 )
            {
                echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                return;
            }
            if( $total_costs > $item_cost )
            {
                echo $this->create_json(0, lang('your_input_is_not_range_numeric'), $value);
                return;
            }
            else
            {
//                $completed_id = fetch_status_id('review_state', 'completed');
                $data1 = array(
                        'purchase_order_id'     => $id,
                        'payment_cost'          => price($value),
                );
                if($total_costs > 0 && $total_costs < $item_cost)
                {
                    $data = array(
                            'payment_state'  => '2',
                    );
                }
                if($total_costs == price($item_cost))
                {
                    $data = array(
                            'payment_state'  => '3',
//                            'review_state' => $completed_id,
                    );
                    
                }
				$this->purchase_finance_model->add_purchase_payment($data1);
				$this->purchase_order_model->review_reject_order($id, $data);
                echo $this->create_json(1, lang('ok'), $value);
            }
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }

    }

    public function update_item_cost()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));       
        try
        {
            if ( ! is_numeric($value) ||  $value < 0 )
            {
                echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                return;
            }
            $this->purchase_finance_model->update_item_cost($id, $type, $value);

            echo $this->create_json(1, lang('ok'), $value);
            
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }


    public function batch_update_purchase_order_status($tag)
    {
        $order_count = $this->input->post('order_count');
        $user_id = get_current_user_id();

        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('purchase_order_id_' . $i);
           
            $completed_id = fetch_status_id('review_state', 'completed');

            $pending_skus_obj_arr = $this->purchase_order_model->fetch_skus($order_id);

            try {

                if($tag === 'batch_review')
                {
                    $data = array(
                        'payment_state'  => '1',
                    );

                    $this->purchase_order_model->review_reject_order($order_id, $data);
//                    $this->finance_pending();
                }
                else
                {
                    $data = array(
                        'review_state' => $completed_id,
                    );

                    $this->purchase_finance_model->update_purchase_order_by_id($order_id, $data);
                }

                foreach($pending_skus_obj_arr as $pending_sku_obj)
                {
                    $count = on_way_count($pending_sku_obj->s_sku);

                    $data_product = array(
                        'on_way_count' => $count,
                    );

                    $this->product_model->update_product_by_sku($pending_sku_obj->s_sku, $data_product);
                }

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        echo $this->create_json(1, lang('stock_check_or_count_successfully'));
    }

}

?>
