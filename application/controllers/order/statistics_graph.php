<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Statistics_graph extends Order
{    
    protected $line_config;

    public function __construct() {
        parent::__construct();
        $this->load->model('solr_order_model');
        $this->load->helper('solr');
        $this->load->model('solr/solr_base_model');
        $this->load->model('order_model');
        $this->load->model('solr/solr_statistics_model');
        $this->load->model('solr/statistics_graph_model');
        $this->load->model('shipping_code_model');
        $this->load->library('charts');
        $this->template->add_js('static/js/sorttable.js');
        $rates = $this->order_model->fetch_currency();
        foreach ($rates as $rate)
        {
            $this->cur_rates[$rate->name_en] = $rate->ex_rate;
        }
        $this->line_config = array('Legend' => '',
            'LegendFontName' => 'GeosansLight.ttf',
            'LegendFontSize' => 15,
            'Textbox' => "",
            'TextboxFontSize' => 12,
            'TextboxFontName' => 'Silkscreen.ttf',
            'Xlabel' => '',
            'Ylabel' => '',
            'DataR' => 0,
            'DataG' => 225,
            'DataB' => 0,
            'XAngle' => 45,
        );
    }
    
    public function order_count_statistics()
    {
        if ($this->input->is_post())
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $gap = $this->input->post('gap_type');
            $graph = $this->input->post('graph_type');
        }
        else
        {
            $begin_time = date('Y-m-d', strtotime('Last month') ). ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
            $gap = '+1DAY';
            $graph = 'line';
        }
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $response_array = $this->statistics_graph_model->fetch_order_count_statistics_by_time_gap($begin_time, $end_time, $gap);
        $facet_datas = $response_array->facet_counts;
        $input_datetimes = $facet_datas->facet_dates->input_datetime;
        $input_datetimes = (array)$input_datetimes;
        unset($input_datetimes['gap']);
        unset($input_datetimes['end']);
        $x = array_keys($input_datetimes);
        foreach ($x as $key => $item)
        {
            $x[$key] = date('y/m/d', strtotime($item));
        }
        $y = array_values($input_datetimes);
        $data = array(
            'facet_datas'       => $facet_datas,
            'begin_time'        => $begin_time,
            'end_time'          => $end_time,
            'gap_type'          => $gap,
            'input_datetimes'   => $input_datetimes,
            'graph_type'        => $graph,
        );
        $data['charts'] = $this->charts->cartesianChart($graph, $x, $y, 1000, 500, 'order_count_statistics.png', $this->line_config);

        $this->set_2column('sidebar_count_statistics');
        $this->template->write_view('content', 'order/statistics_graph/order_count_statistics', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    public function by_country_and_region() {
        $this->set_2column('sidebar_count_statistics');
        $input_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d", strtotime('Last month')) . ' ' . '00:00:00';
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
        }
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $input_users = $this->order_model->fetch_input_user();
        $status_id = fetch_status_id('order_status', 'closed');
        $currencies = array('RMB', 'USD', 'AUD', 'GBP', 'EUR');
        $facet_countries = $this->solr_statistics_model->fetch_country_all();
        foreach ($facet_countries as $key => $nums) {
            $countries[] = $key;
        }

        foreach ($currencies as $currency) {
            $total_infos = $this->solr_statistics_model->fetch_total_info_by_country($currency, $begin_time, $end_time, $input_user, $status_id);
            $total_datas = empty($total_infos->facets->country) ? NULL : $total_infos->facets->country;
            $total_all_count = empty($total_infos->count) ? 0 : $total_infos->count;
            $total_all_cost[$currency] = empty($total_infos->sum) ? 0 : $total_infos->sum;
            foreach ($countries as $country) {
                if (!empty($total_datas->$country->count)) {
                    $tmp_count = $total_datas->$country->count;
                } else {
                    $tmp_count = 0;
                }
                if (isset($total_count[$country])) {
                    $total_count[$country] += $tmp_count;
                } else {
                    $total_count[$country] = $tmp_count;
                }

                if (empty($total_datas->$country->sum)) {
                    $total_cost[$country][$currency] = 0;
                } else {
                    $total_cost[$country][$currency] = price($total_datas->$country->sum);
                }
                if (isset($total_to_rmb[$country])) {
                    $total_to_rmb[$country] += $total_cost[$country][$currency] * $this->cur_rates[$currency];
                } else {
                    $total_to_rmb[$country] = $total_cost[$country][$currency];
                }
            }

            if (isset($total_all_count_ot)) {
                $total_all_count_ot += $total_all_count;
            } else {
                $total_all_count_ot = $total_all_count;
            }
            $j = 1;
            foreach ($countries as $country) {
                if ($j == 1) {
                    $total_cost['others'][$currency] = price($total_all_cost[$currency] - $total_cost[$country][$currency]);
                } else if ($j > 1) {
                    $total_cost['others'][$currency] = price($total_cost['others'][$currency] - $total_cost[$country][$currency]);
                }
                $j++;
            }
            if (isset($total_to_rmb['others'])) {
                $total_to_rmb['others'] += $total_cost['others'][$currency] * $this->cur_rates[$currency];
            } else {
                $total_to_rmb['others'] = $total_cost['others'][$currency];
            }
        }
        $x = 1;
        foreach ($countries as $country) {
            if ($x == 1) {
                $total_count['others'] = $total_all_count_ot - $total_count[$country];
            } else if ($x > 1) {
                $total_count['others'] = $total_count['others'] - $total_count[$country];
            }
            $x++;
        }
        $countries['other'] = 'others';
        $data = array(
            'input_users' => $input_users,
            'current_user' => $input_user,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'total_count' => $total_count,
            'total_cost' => $total_cost,
            'countries' => $countries,
            'currencies' => $currencies,
            'rates'      => $this->cur_rates,
        );
        $this->template->write_view('content', 'order/statistics_graph/by_country_and_region', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    public function by_shipping_way() {
       $this->set_2column('sidebar_count_statistics');
        $input_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d", strtotime('Last month')) . ' ' . '00:00:00';
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
        }
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $input_users = $this->order_model->fetch_input_user();
        $status_id = fetch_status_id('order_status', 'closed');
        $currencies = array('RMB', 'USD', 'AUD', 'GBP', 'EUR');
        $ship_codes = $this->shipping_code_model->fetch_all_shipping_codes();
        foreach ($ship_codes as $ship_code) {
            $ship_code_arr[] = $ship_code->code;
        }

        foreach ($currencies as $currency) {
            $total_infos = $this->solr_statistics_model->fetch_total_info_by_ship_shipping_code($currency, $begin_time, $end_time, $input_user, $status_id);
            $total_datas = empty($total_infos->facets->shipping_code) ? NULL : $total_infos->facets->shipping_code;
            $total_all_count = empty($total_infos->count) ? 0 : $total_infos->count;
            $total_all_cost[$currency] = empty($total_infos->sum) ? 0 : $total_infos->sum;
            foreach ($ship_code_arr as $ship_code) {
                if (!empty($total_datas->$ship_code->count)) {
                    $tmp_count = $total_datas->$ship_code->count;
                } else {
                    $tmp_count = 0;
                }
                if (isset($total_count[$ship_code])) {
                    $total_count[$ship_code] += $tmp_count;
                } else {
                    $total_count[$ship_code] = $tmp_count;
                }

                if (empty($total_datas->$ship_code->sum)) {
                    $total_cost[$ship_code][$currency] = 0;
                } else {
                    $total_cost[$ship_code][$currency] = price($total_datas->$ship_code->sum);
                }
                if (isset($total_to_rmb[$ship_code])) {
                    $total_to_rmb[$ship_code] += $total_cost[$ship_code][$currency] * $this->cur_rates[$currency];
                } else {
                    $total_to_rmb[$ship_code] = $total_cost[$ship_code][$currency];
                }
            }
        }

        $data = array(
            'input_users' => $input_users,
            'current_user' => $input_user,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'total_count' => $total_count,
            'total_cost' => $total_cost,
            'currencies' => $currencies,
            'ship_codes' => $ship_code_arr,
            'rates'      => $this->cur_rates,
        );

        $this->template->write_view('content', 'order/statistics_graph/by_shipping_way', $data);
        $this->template->render();
    }

    public function by_input_account() {
       $this->set_2column('sidebar_count_statistics');
       $input_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
        }
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $input_users = $this->order_model->fetch_input_user();
        foreach ($input_users as $input_user) {
            $user_arr[] = $input_user->input_user;
        }
        $status_id = fetch_status_id('order_status', 'closed');
        $currencies = array('RMB', 'USD', 'AUD', 'GBP', 'EUR');

        foreach ($currencies as $currency) {
            $total_infos = $this->solr_statistics_model->fetch_total_info_by_input_user($currency, $begin_time, $end_time, $status_id);
            $total_datas = empty($total_infos->facets->input_user) ? NULL : $total_infos->facets->input_user;
            $total_all_count = empty($total_infos->count) ? 0 : $total_infos->count;
            $total_all_cost[$currency] = empty($total_infos->sum) ? 0 : $total_infos->sum;
            foreach ($user_arr as $input_user) {
                if (!empty($total_datas->$input_user->count)) {
                    $tmp_count = $total_datas->$input_user->count;
                } else {
                    $tmp_count = 0;
                }
                if (isset($total_count[$input_user])) {
                    $total_count[$input_user] += $tmp_count;
                } else {
                    $total_count[$input_user] = $tmp_count;
                }

                if (empty($total_datas->$input_user->sum)) {
                    $total_cost[$input_user][$currency] = 0;
                } else {
                    $total_cost[$input_user][$currency] = price($total_datas->$input_user->sum);
                }
                if (isset($total_to_rmb[$input_user])) {
                    $total_to_rmb[$input_user] += $total_cost[$input_user][$currency] * $this->cur_rates[$currency];
                } else {
                    $total_to_rmb[$input_user] = $total_cost[$input_user][$currency];
                }
            }
        }

        $data = array(
            'input_users' => $input_users,
            'current_user' => $input_user,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'total_count' => $total_count,
            'total_cost' => $total_cost,
            'currencies' => $currencies,
            'user_arr' => $user_arr,
            'rates'      => $this->cur_rates,
        );
        $this->template->write_view('content', 'order/statistics_graph/by_input_account', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    public function by_ship_confirm_user() {
       $this->set_2column('sidebar_count_statistics');
       $input_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
        }
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $input_users = $this->order_model->fetch_input_user();
        $ship_users = $this->order_model->fetch_ship_confirm_user();
        foreach ($ship_users as $users) {
            if (empty($users->ship_confirm_user)) {
                continue;
            }
            $ship_user_arr[] = $users->ship_confirm_user;
        }
        $status_id = fetch_status_id('order_status', 'closed');
        $currencies = array('RMB', 'USD', 'AUD', 'GBP', 'EUR');

        foreach ($currencies as $currency) {
            $total_infos = $this->solr_statistics_model->fetch_total_info_by_ship_confirm_user($currency, $begin_time, $end_time, $input_user, $status_id);
            $total_datas = empty($total_infos->facets->ship_confirm_user) ? NULL : $total_infos->facets->ship_confirm_user;
            $total_all_count = empty($total_infos->count) ? 0 : $total_infos->count;
            $total_all_cost[$currency] = empty($total_infos->sum) ? 0 : $total_infos->sum;
            foreach ($ship_user_arr as $ship_user) {
                if (!empty($total_datas->$ship_user->count)) {
                    $tmp_count = $total_datas->$ship_user->count;
                } else {
                    $tmp_count = 0;
                }
                if (isset($total_count[$ship_user])) {
                    $total_count[$ship_user] += $tmp_count;
                } else {
                    $total_count[$ship_user] = $tmp_count;
                }

                if (empty($total_datas->$ship_user->sum)) {
                    $total_cost[$ship_user][$currency] = 0;
                } else {
                    $total_cost[$ship_user][$currency] = price($total_datas->$ship_user->sum);
                }
                if (isset($total_to_rmb[$ship_user])) {
                    $total_to_rmb[$ship_user] += $total_cost[$ship_user][$currency] * $this->cur_rates[$currency];
                } else {
                    $total_to_rmb[$ship_user] = $total_cost[$ship_user][$currency];
                }
            }
        }
        $data = array(
            'input_users' => $input_users,
            'current_user' => $input_user,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'total_count' => $total_count,
            'total_cost' => $total_cost,
            'currencies' => $currencies,
            'ship_users' => $ship_user_arr,
            'rates'      => $this->cur_rates,
        );

        $this->template->write_view('content', 'order/statistics_graph/by_ship_confirm_user', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }
}