<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Finance_order extends Order
{
    public function __construct() {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('ebay_order_model');
        $this->load->model('shipping_code_model');
        $this->load->model('product_model');
    }
    
    public function confirm_order()
    {
        $this->enable_search('order');
        $this->enable_sort('order');

        $orders = $this->order_model->fetch_wait_for_confirmation_orders(NULL, 'wait_for_finance_confirmation');

        $data = array(
            'orders'            => $orders,
            'waiting_skus'      => array(),
            'confirm_type'      => 'wait_for_finance_confirmation',
        );

        $this->template->write_view('content', 'order/regular_order/confirm_order', $data);
        $this->template->add_js('static/js/ajax/order.js');
        // Render the template
        $this->template->render();
    }

    public function make_confirmed()
    {
        $order_id = $this->input->post('order_id');
        $user_name = $this->get_current_user_name();

        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('finance_confirm_order_remark'), date('Y-m-d H:i:s'), $user_name);

        $data = array(
            'order_status'          => $this->order_statuses['wait_for_purchase'],
            'bursary_check_user'    => $user_name,
            'bursary_check_date'    => date('Y-m-d H:i:s'),
            'sys_remark'            => $remark,
        );

        try
        {
            $this->order_model->update_order_information($order_id, $data);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function make_batch_confirmed()
    {
        $order_count = $this->input->post('order_count');
        if ($order_count < 1)
        {
            echo $this->create_json(0, lang('ok'));
            return;
        }
        $user_name = $this->get_current_user_name();
        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('order_id_' . $i);
            $sku_string = trim(trim($this->input->post('sku_string_' . $i)), ',');

            $remark = $this->order_model->get_sys_remark($order_id);
            $remark .= sprintf(lang('finance_confirm_order_remark'), date('Y-m-d H:i:s'), $user_name);

            $skus = explode(',', $sku_string);
            $sku_not_exists = FALSE;
            foreach ($skus as $sku)
            {
                if ( ! $this->product_model->fetch_product_id(strtoupper($sku)))
                {
                    $sku_not_exists = TRUE;
                    break;
                }
            }
            if ($sku_not_exists)
            {
                continue;
            }
            $data = array(
                'order_status'          => $this->order_statuses['wait_for_purchase'],
                'check_user'            => $user_name,
                'bursary_check_date'    => date('Y-m-d H:i:s'),
                'sys_remark'            => $remark,
            );

            try
            {
                $this->order_model->update_order_information($order_id, $data);
            }
            catch (Exception $e)
            {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function make_wait_confirmed()
    {
        $order_id = $this->input->post('order_id');
        $description = $this->input->post('note');
        $user_name = $this->get_current_user_name();

        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('finance_return_order_remark'), date('Y-m-d H:i:s'), $user_name);

        $data = array(
            'order_status'          => $this->order_statuses['wait_for_confirmation'],
            'descript'              => $description,
            'sys_remark'            => $remark,
        );

        try
        {
            $this->order_model->update_order_information($order_id, $data);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function make_holded()
    {
        $order_id = $this->input->post('order_id');
        $user_name = $this->get_current_user_name();
        if (! $order_id)
        {
            return;
        }
        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('finance_hold_order_remark'), date('Y-m-d H:i:s'), $user_name);
        $data = array(
            'order_status'          => $this->order_statuses['finance_holded'],
            'sys_remark'            => $remark,
        );
        try
        {
            $this->order_model->update_order_information($order_id, $data);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

}

?>
