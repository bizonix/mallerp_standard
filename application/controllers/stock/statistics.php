<?php
require_once APPPATH.'controllers/stock/stock'.EXT;

class Statistics extends Stock
{
    private $shipping_codes = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('product_model');

        $this->load->model('shipping_statistics_model');
        $this->load->model('shipping_code_model');
        $this->template->add_js('static/js/sorttable.js');

        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_codes = array();
        foreach ($shipping_code_object as $row)
        {
            $this->shipping_codes[] = $row->code;
        }

    }

    public function stock_check()
    {
        $report = $this->stock_model->stock_check(array(), TRUE);

        $data = array(
            'report'    => $report,
        );
        
        $this->template->write_view('content', 'stock/statistics/order_status', $data);
        $this->template->render();
    }

    public function pick_up_products()
    {
        $time = $this->input->post('end_time');
        if ( ! $time)
        {
            $time = date('Y-m-d 11:00:00');
        }
        $report = $this->stock_model->stock_check(array('order_list.input_date <=' => $time));
        $product_be_used = $report['products_be_used'];
        $is_outstock = $this->stock_model->product_statistics_outstock_exsists($time);
        $data = array(
            'products'    => $product_be_used,
            'time'        => $time,
            'is_outstock' => $is_outstock,
        );
        
        $this->template->write_view('content', 'stock/statistics/pick_up_products', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
        $user_name = $this->get_current_user_name();
        $this->stock_model->delete_product_statistics(array('created_date' => $time));
        /*
         * the fellowing code may will be used in the future
         */
        /*
        foreach ($product_be_used as $sku => $qty)
        {
            $data = array(
                'sku'           => $sku,
                'qty'           => $qty,
                'user'          => $user_name,
                'created_date'  => $time,
            );
            $this->stock_model->save_product_statistics($data);
        }
         */
    }
/*
    public function outstock()
    {
        $time = $this->input->post('end_time');
        if ( ! $time)
        {
            echo $this->create_json(0, lang('statistics_time_should_not_empty'));
            return ;
        }
        if ( ! $this->stock_model->product_statistics_exsists(array('created_date' => $time)))
        {
            echo $this->create_json(0, lang('no_record_by_statistics_time'));

            return ;
        }
        if ($this->stock_model->product_statistics_outstock_exsists($time))
        {
            echo $this->create_json(0, lang('already_outstock_by_statistics_time'));

            return ;
        }
        $products = $this->stock_model->fetch_product_statistics(array('created_date' => $time));
        $product_count = count($products);
        $item_count = 0;
        foreach ($products as $p)
        {
            $item_count += $p->qty;
            $this->product_model->update_product_stock_count_by_sku($p->sku,  $p->qty, TRUE, 'statistics_outstock', $time);
        }
        $this->stock_model->save_product_statistics_outstock($time);

        echo $this->create_json(1, sprintf(lang('outstock_by_statistics_successfully'), $product_count, $item_count));
    }
 * 
 */

    public function personal_statistics()
    {
        if ( ! $this->input->is_post())
        {
            $split_date = FALSE;
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $split_date = $this->input->post('split_date');
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }

        list($scope_statistics, $stock_user) = $this->shipping_statistics_model->fetch_scope_statistics_by_stock_user($begin_time, $end_time, 1, NULL, $split_date);
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'split_date'          => $split_date,
            'shipping_codes'      => $this->shipping_codes,
        );

        $this->template->write_view('content', 'stock/statistics/personal_statistics', $data);
        $this->template->render();
    }

    public function department_statistics()
    {
        $current_stock_user = NULL;
        if ( ! $this->input->is_post())
        {
            $split_date = FALSE;
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $current_stock_user = $this->input->post('stock_user');
            $split_date = $this->input->post('split_date');
        }

        $priority = 2;
        list($scope_statistics, $stock_user) = $this->shipping_statistics_model->fetch_scope_statistics_by_stock_user($begin_time, $end_time, $priority, $current_stock_user, $split_date);

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'department'          => TRUE,
            'stock_user'          => $stock_user,
            'all_stock_users'     => $all_stock_users,
            'current_stock_user'  => $current_stock_user,
            'split_date'          => $split_date,
            'shipping_codes'      => $this->shipping_codes,
        );

        $this->template->write_view('content', 'stock/statistics/department_statistics', $data);
        $this->template->render();
    }

}
?>
