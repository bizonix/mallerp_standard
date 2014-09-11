<?php
require_once APPPATH.'controllers/sale/sale'.EXT;

class View_glance_rate extends Sale
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_statistics_model');
        $this->load->helper('db_helper');
        $this->template->add_js('static/js/sorttable.js');
    }

    public function customer_second_glance_rate()
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
