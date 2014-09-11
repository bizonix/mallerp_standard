<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Statistics extends Purchase
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_statistics_model');
        $this->load->model('stock_model');
        $this->load->model('product_model');
        $this->load->model('solr/solr_base_model');
        $this->load->model('solr/solr_statistics_model');
        $this->load->helper('solr');
        $this->load->helper('db_helper');
        $this->template->add_js('static/js/sorttable.js');
        
    }

    public function purchase_statistics()
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

        list($scope_statistics, $purchasers) = $this->purchase_statistics_model->fetch_scope_statistics($begin_time, $end_time, 1, NULL, $split_date);
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'split_date'          => $split_date,
        );

        $this->template->write_view('content', 'purchase/statistics/personal_statistics', $data);
        $this->template->render();
    }


    public function department_purchase_statistics()
    {
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {
            $split_date = TRUE;
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');
            $split_date = $this->input->post('split_date');
        }

        $priority = 2;
        list($scope_statistics, $purchasers) = $this->purchase_statistics_model->fetch_scope_statistics($begin_time, $end_time, $priority, $purchaser, $split_date);
        $all_purchasers = $this->user_model->fetch_users_by_system_code('purchase');
        $purchasers = array_unique($purchasers);
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'split_date'          => $split_date,
            'purchasers'          => $purchasers,
            'all_purchasers'      => $all_purchasers,
            'current_purchaser'   => $purchaser,
        );

        $this->template->write_view('content', 'purchase/statistics/department_statistics', $data);
        $this->template->render();
    }

    public function purchase_sale_statistics()
    {
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
        }

        $priority = 1;
        $statistics = $this->purchase_statistics_model->fetch_sale_statistics($begin_time, $end_time, $priority, NULL);

        $data = array(
            'statistics'          => $statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
        );

        $this->template->write_view('content', 'purchase/statistics/personal_sale_statistics', $data);
        $this->template->render();
    }

    public function department_purchase_sale_statistics()
    {
        $purchaser = NULL;
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
            $purchaser = $this->input->post('purchaser');
        }

        $priority = 2;
        $statistics = $this->purchase_statistics_model->fetch_sale_statistics($begin_time, $end_time, $priority, $purchaser);
        $all_purchasers = $this->user_model->fetch_users_by_system_code('purchase');

        $data = array(
            'statistics'          => $statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'all_purchasers'      => $all_purchasers,
            'current_purchaser'   => $purchaser,
        );

        $this->template->write_view('content', 'purchase/statistics/department_sale_statistics', $data);
        $this->template->render();
    }

    public function download_personal_delay_item_no()
    {
        $current_user_id = get_current_user_id();
        $key = 'purchase_statistics_item_nos_' . $current_user_id;

        $this->_proccess_download_delay_item_no($key);
    }

    public function download_department_delay_item_no()
    {
        $key = 'purchase_statistics_item_nos';

        return $this->_proccess_download_delay_item_no($key);
    }

    private function _proccess_download_delay_item_no($key)
    {
        $this->load->library('excel');

        $statistics = $this->cache->file->get($key);

        $head = array(
            lang('username'),
            lang('delay_times') . "(" . lang('day') . ")",
            lang('item_no'),
            lang('sku_str'),
            lang('sys_remark'),
        );

        uasort($statistics, array($this, 'sort_by_days'));

        $this->excel->array_to_excel($statistics, $head, 'delay-statistics-' . date('Y-m-d'));
    }

    public function sort_by_days($a, $b)
    {
        return (intval($a[1]) < intval($b[1])) ? -1 : 1;
    }

    public function personal_development_statistics()
    {
        $groups = $this->user_model->fetch_users_by_group_name('开发员');
        $developer_ids = object_to_array($groups, 'u_id');
        $user_id = get_current_user_id();
        if(in_array($user_id, $developer_ids))
        {
            $this->department_development_statistics('developer', $user_id);
        }
        else
        {
            $this->department_development_statistics('purchaser', $user_id);
        }
        
    }

    public function develop_department_statistics()
    {
        $this->department_development_statistics('developer');  
    }

    public function purchase_department_statistics()
    {
        $this->department_development_statistics('purchaser');
    }

    public function department_development_statistics($role, $personal = null)
    {
        if ( ! $this->input->is_post())
        {
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
            $sale_begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $sale_end_time = date('Y-m-d H:i:s');
//            $developer_id = NULL;
            $developer_id = ($personal == get_current_user_id()) ? $personal : NULL;
            

        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $sale_begin_time = $this->input->post('sale_begin_time');
            $sale_end_time = $this->input->post('sale_end_time');
            if($this->input->post('personal'))
            {
                $developer_id =  $this->input->post('personal');
                $personal = $developer_id;
            } else {
                $developer_id = $this->input->post('developer_id');
            }

        }
        list($product, $statistics) = $this->purchase_statistics_model->department_development_statistics($begin_time, $end_time, $sale_begin_time, $sale_end_time, $developer_id);
        $data = array(
            'statistics'            => $statistics,
            'product'               => $product,
            'begin_time'            => $begin_time,
            'end_time'              => $end_time,
            'sale_begin_time'       => $sale_begin_time,
            'sale_end_time'         => $sale_end_time,                                           
            'role'                  => $role,
            'current_purchaser_id'  => $developer_id,
            'personal'              => $personal,
        );
        
       $this->template->write_view('content', 'purchase/statistics/department_development_statistics', $data);
       $this->template->render();                
    }

    public function department_ito_statistics()
    {
        if ( ! $this->input->is_post())
        {
            $last_month_time = strtotime('-1 month');
            $year = date('Y', $last_month_time);
            $month = date('m', $last_month_time);
        }
        else
        {
            $year = $this->input->post('year');
            $month = $this->input->post('month');
        }
        $statistics = $this->stock_model->fetch_product_ito_statistics($year, $month);
        $data = array(
            'statistics'    => $statistics,
            'year'          => $year,
            'month'          => $month,
        );
        $this->template->write_view('content', 'purchase/statistics/department_ito_statistics', $data);
        $this->template->render();
    }
    
    public function customer_second_glance_rate()
    {
        if ( ! $this->input->is_post())
        {
            $year = date('Y');
            $month = date('m');
        }
        else
        {
            $year = $this->input->post('year');
            $month = $this->input->post('month');
        }
        $statistics =  $this->purchase_statistics_model->fetch_second_grance_rate_data($year,$month);
        $data = array(
            'statistics'     => $statistics,
            'year'           => $year,
            'month'          => $month,
        );
        $this->template->write_view('content', 'purchase/statistics/customer_second_glance_rate', $data);
        $this->template->render();
    }
}

?>
