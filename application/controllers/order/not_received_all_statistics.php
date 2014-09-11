<?php
require_once APPPATH.'controllers/order/order'.EXT;
class Not_received_all_statistics extends Order
{
    public $cur_rates;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('solr/solr_base_model');
        $this->load->model('solr/solr_statistics_model');
        $this->load->model('shipping_code_model');
        $this->load->helper('solr');
        $this->template->add_js('static/js/sorttable.js');
        $rates = $this->order_model->fetch_currency();
        foreach ($rates as $rate)
        {
            $this->cur_rates[$rate->name_en] = $rate->ex_rate;
        }
    }

    public function by_country_and_region()
    {
       $this->set_2column('sidebar_statistics_order');
       $input_user = NULL;
       if(!$this->input->is_post())
       {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
       }
       else
       {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
       }
       $begin_time = to_utc_format($begin_time);
       $end_time = to_utc_format($end_time);
       $input_users = $this->order_model->fetch_input_user();
       $status_id = fetch_status_id('order_status', 'not_received_full_refunded');
       $currencies =  array('RMB', 'USD', 'AUD', 'GBP','EUR','HKD');
       $facet_countries = $this->solr_statistics_model->fetch_country($status_id);
       foreach ($facet_countries as $key => $nums)
       {
           $countries[] = $key;
       }
       
       foreach ($currencies as $currency)
       {
           $return_infos = $this->solr_statistics_model->fetch_return_info_by_country($currency, $status_id, $begin_time, $end_time, $input_user);
           $return_datas = empty($return_infos->facets->country)? NULL : $return_infos->facets->country;
           $return_all_count = empty($return_infos->count)? 0 :  $return_infos->count;
           $return_all_cost[$currency] = empty($return_infos->sum)? 0 : $return_infos->sum;
           foreach ($countries as $country)
           {
               if (!empty($return_datas->$country->count))
               {
                   $tmp_count = $return_datas->$country->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($return_count[$country]))
               {
                   $return_count[$country] += $tmp_count;
               }
               else
               {
                   $return_count[$country] = $tmp_count;
               }

               if(empty($return_datas->$country->sum))
               {
                   $return_cost[$country][$currency] = 0;
               }
               else
               {
                   $return_cost[$country][$currency] = price($return_datas->$country->sum);
               }
               if(isset($return_to_rmb[$country]))
               {
                   $return_to_rmb[$country] += $return_cost[$country][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $return_to_rmb[$country] = $return_cost[$country][$currency];
               }
           }

           if(isset($return_all_count_ot))
           {
               $return_all_count_ot +=  $return_all_count;
           }
           else
           {
               $return_all_count_ot = $return_all_count;
           }
           $i = 1;
           foreach ($countries as $country)
           {
               if($i == 1)
               {
                   $return_cost['others'][$currency] = $return_all_cost[$currency]  - $return_cost[$country][$currency];
               }
               else if($i > 1)
               {
                   $return_cost['others'][$currency] = $return_cost['others'][$currency]- $return_cost[$country][$currency];
               }
               $i++;
           }
           if(isset($return_to_rmb['others']))
           {
               $return_to_rmb['others'] += $return_cost['others'][$currency] * $this->cur_rates[$currency];
           }
           else
           {
               $return_to_rmb['others'] = $return_cost['others'][$currency];
           }

       }
       $k = 1;
       foreach ($countries as $country)
       {
           if($k == 1)
           {
               $return_count['others'] = $return_all_count_ot - $return_count[$country];
           }
           else if($k > 1)
           {
               $return_count['others'] = $return_count['others'] - $return_count[$country];
           }
           $k++;
       }

       foreach ($currencies as $currency)
       {
           $total_infos = $this->solr_statistics_model->fetch_total_info_by_country($currency, $begin_time, $end_time, $input_user);
           $total_datas = empty($total_infos->facets->country)? NULL : $total_infos->facets->country;
           $total_all_count = empty($total_infos->count)? 0 : $total_infos->count;
           $total_all_cost[$currency] = empty($total_infos->sum)? 0 : $total_infos->sum;
           foreach ($countries as $country)
           {
               if (!empty($total_datas->$country->count))
               {
                   $tmp_count = $total_datas->$country->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($total_count[$country]))
               {
                   $total_count[$country] += $tmp_count;
               }
               else
               {
                  $total_count[$country] = $tmp_count;
               }

               if(empty($total_datas->$country->sum))
               {
                   $total_cost[$country][$currency] = 0;
               }
               else
               {
                   $total_cost[$country][$currency] = price($total_datas->$country->sum);
               }
               if(isset($total_to_rmb[$country]))
               {
                  $total_to_rmb[$country] += $total_cost[$country][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                  $total_to_rmb[$country] = $total_cost[$country][$currency];
               }
           }

           if(isset($total_all_count_ot))
           {
               $total_all_count_ot +=  $total_all_count;
           }
           else
           {
               $total_all_count_ot = $total_all_count;
           }
           $j = 1;
           foreach ($countries as $country)
           {
               if($j == 1)
               {
                   $total_cost['others'][$currency] = price($total_all_cost[$currency] - $total_cost[$country][$currency]);
               }
               else if($j > 1)
               {
                   $total_cost['others'][$currency] = price($total_cost['others'][$currency]- $total_cost[$country][$currency]);
               }
               $j++;
           }
           if(isset($total_to_rmb['others']))
           {
               $total_to_rmb['others'] += $total_cost['others'][$currency] * $this->cur_rates[$currency];
           }
           else
           {
               $total_to_rmb['others'] = $total_cost['others'][$currency];
           }
       }
       $x = 1;
       foreach ($countries as $country)
       {
           if($x == 1)
           {
               $total_count['others'] = $total_all_count_ot - $total_count[$country];
           }
           else if($x > 1)
           {
               $total_count['others'] = $total_count['others'] - $total_count[$country];
           }
           $x++;
       }
       $countries['other'] = 'others';
       foreach ($countries as $country)
       {
           if($total_count[$country] == 0)
           {
               $return_count_rate[$country] = 0;
           }
           else
           {
               $return_count_rate[$country] = price($return_count[$country]/$total_count[$country],'4');
           }
           if($total_to_rmb[$country] == 0)
           {
               $return_cost_rate[$country] = 0;
           }
           else
           {
               $return_cost_rate[$country] = price($return_to_rmb[$country]/$total_to_rmb[$country],'4');
           }
       }
      $data = array(
          'input_users'             => $input_users,
          'current_user'            => $input_user,
          'begin_time'              => $begin_time,
          'end_time'                => $end_time,
          'return_count'            => $return_count,
          'total_count'             => $total_count,
          'return_count_rate'       => $return_count_rate,
          'return_cost'             => $return_cost,
          'total_cost'              => $total_cost,
          'return_cost_rate'        => $return_cost_rate,
          'countries'               => $countries,
          'currencies'              => $currencies,

      );
     $this->template->write_view('content', 'order/not_receive_all/by_country_and_region', $data);
     $this->template->add_js('static/js/sorttable.js');
     $this->template->render();
    }
    
    public function by_shipping_way()
    {
       $this->set_2column('sidebar_statistics_order');
       $input_user = NULL;
       if(!$this->input->is_post())
       {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
       }
       else
       {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
       }
       $begin_time = to_utc_format($begin_time);
       $end_time = to_utc_format($end_time);
       $input_users = $this->order_model->fetch_input_user();
       $status_id = fetch_status_id('order_status', 'not_received_full_refunded');
       $currencies =  array('RMB', 'USD', 'AUD', 'GBP','EUR','HKD');
       $ship_codes = $this->shipping_code_model->fetch_all_shipping_codes();
       foreach($ship_codes as $ship_code)
       {
           $ship_code_arr[] = $ship_code->code;
       }

       foreach ($currencies as $currency)
       {
           $return_infos = $this->solr_statistics_model->fetch_retrun_info_by_shipping_code($currency, $status_id, $begin_time, $end_time, $input_user);
           $return_datas = empty($return_infos->facets->shipping_code)? NULL : $return_infos->facets->shipping_code;
           $return_all_count = empty($return_infos->count)? 0 :  $return_infos->count;
           $return_all_cost[$currency] = empty($return_infos->sum)? 0 : $return_infos->sum;
           foreach ($ship_code_arr as $ship_code)
           {
               if (!empty($return_datas->$ship_code->count))
               {
                   $tmp_count = $return_datas->$ship_code->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($return_count[$ship_code]))
               {
                   $return_count[$ship_code] += $tmp_count;
               }
               else
               {
                   $return_count[$ship_code] = $tmp_count;
               }

               if(empty($return_datas->$ship_code->sum))
               {
                   $return_cost[$ship_code][$currency] = 0;
               }
               else
               {
                   $return_cost[$ship_code][$currency] = price($return_datas->$ship_code->sum);
               }
               if(isset($return_to_rmb[$ship_code]))
               {
                   $return_to_rmb[$ship_code] += $return_cost[$ship_code][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $return_to_rmb[$ship_code] = $return_cost[$ship_code][$currency];
               }
           }

       }

       foreach ($currencies as $currency)
       {
           $total_infos = $this->solr_statistics_model->fetch_total_info_by_ship_shipping_code($currency, $begin_time, $end_time, $input_user);
           $total_datas = empty($total_infos->facets->shipping_code)? NULL : $total_infos->facets->shipping_code;
           $total_all_count = empty($total_infos->count)? 0 :  $total_infos->count;
           $total_all_cost[$currency] = empty($total_infos->sum)? 0 : $total_infos->sum;
           foreach ($ship_code_arr as $ship_code)
           {
               if (!empty($total_datas->$ship_code->count))
               {
                   $tmp_count = $total_datas->$ship_code->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($total_count[$ship_code]))
               {
                   $total_count[$ship_code] += $tmp_count;
               }
               else
               {
                   $total_count[$ship_code] = $tmp_count;
               }

               if(empty($total_datas->$ship_code->sum))
               {
                   $total_cost[$ship_code][$currency] = 0;
               }
               else
               {
                   $total_cost[$ship_code][$currency] = price($total_datas->$ship_code->sum);
               }
               if(isset($total_to_rmb[$ship_code]))
               {
                   $total_to_rmb[$ship_code] += $total_cost[$ship_code][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $total_to_rmb[$ship_code] = $total_cost[$ship_code][$currency];
               }

           }
       }

       foreach ($ship_code_arr as $ship_code)
       {
           if($total_count[$ship_code] == 0)
           {
               $return_count_rate[$ship_code] = 0;
           }
           else
           {
               $return_count_rate[$ship_code] = price($return_count[$ship_code]/$total_count[$ship_code],'4');
           }
           if($total_to_rmb[$ship_code] == 0)
           {
               $return_cost_rate[$ship_code] = 0;
           }
           else
           {
               $return_cost_rate[$ship_code] = price($return_to_rmb[$ship_code]/$total_to_rmb[$ship_code],'4');
           }
       }
       
       arsort($return_count);
       foreach ($return_count as $key => $vl)
       {
           $return_count[$key] = $vl;
       }
       foreach ($return_count as $key => $vl)
       {
           foreach ($ship_code_arr as $ship_code)
           {
               if($key == $ship_code)
               {
                   $ship_code_arrs[] = $key;
               }

           }
       }

       $data = array(
          'input_users'             => $input_users,
          'current_user'            => $input_user,
          'begin_time'              => $begin_time,
          'end_time'                => $end_time,
          'return_count'            => $return_count,
          'total_count'             => $total_count,
          'return_count_rate'       => $return_count_rate,
          'return_cost'             => $return_cost,
          'total_cost'              => $total_cost,
          'return_cost_rate'        => $return_cost_rate,
          'currencies'              => $currencies,
          'ship_codes'              => $ship_code_arrs,

       );

       $this->template->write_view('content','order/not_receive_all/by_shipping_way',$data);
       $this->template->render();

    }

    public function by_input_account()
    {
       $this->set_2column('sidebar_statistics_order');
       $input_user = NULL;
       if(!$this->input->is_post())
       {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
       }
       else
       {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
       }
       $begin_time = to_utc_format($begin_time);
       $end_time = to_utc_format($end_time);
       $input_users = $this->order_model->fetch_input_user();
       foreach ($input_users as $input_user)
       {
          $user_arr[] =   $input_user->input_user;
       }
       $status_id = fetch_status_id('order_status', 'not_received_full_refunded');
       $currencies =  array('RMB', 'USD', 'AUD', 'GBP','EUR','HKD');
       
       foreach ($currencies as $currency)
       {
           $return_infos = $this->solr_statistics_model->fetch_return_info_by_input_user($currency, $status_id, $begin_time, $end_time);
           $return_datas = empty($return_infos->facets->input_user)? NULL : $return_infos->facets->input_user;
           $return_all_count = empty($return_infos->count)? 0 :  $return_infos->count;
           $return_all_cost[$currency] = empty($return_infos->sum)? 0 : $return_infos->sum;
           foreach ($user_arr as $input_user)
           {
               if (!empty($return_datas->$input_user->count))
               {
                   $tmp_count = $return_datas->$input_user->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if (isset($return_count[$input_user]))
               {
                   $return_count[$input_user] += $tmp_count;
               }
               else
               {
                   $return_count[$input_user] = $tmp_count;
               }

               if (empty($return_datas->$input_user->sum))
               {
                   $return_cost[$input_user][$currency] = 0;
               }
               else
               {
                   $return_cost[$input_user][$currency] = price($return_datas->$input_user->sum);
               }
               if (isset($return_to_rmb[$input_user]))
               {
                   $return_to_rmb[$input_user] += $return_cost[$input_user][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $return_to_rmb[$input_user] = $return_cost[$input_user][$currency];
               }

           }
       }

       foreach ($currencies as $currency)
       {
           $total_infos = $this->solr_statistics_model->fetch_total_info_by_input_user($currency, $begin_time, $end_time);
           $total_datas = empty($total_infos->facets->input_user)? NULL : $total_infos->facets->input_user;
           $total_all_count = empty($total_infos->count)? 0 :  $total_infos->count;
           $total_all_cost[$currency] = empty($total_infos->sum)? 0 : $total_infos->sum;
           foreach ($user_arr as $input_user)
           {
               if (!empty($total_datas->$input_user->count))
               {
                   $tmp_count = $total_datas->$input_user->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if (isset($total_count[$input_user]))
               {
                   $total_count[$input_user] += $tmp_count;
               }
               else
               {
                   $total_count[$input_user] = $tmp_count;
               }

               if (empty($total_datas->$input_user->sum))
               {
                   $total_cost[$input_user][$currency] = 0;
               }
               else
               {
                   $total_cost[$input_user][$currency] = price($total_datas->$input_user->sum);
               }
               if (isset($total_to_rmb[$input_user]))
               {
                   $total_to_rmb[$input_user] += $total_cost[$input_user][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $total_to_rmb[$input_user] = $total_cost[$input_user][$currency];
               }

           }
       }

       foreach ($user_arr as $input_user)
       {
           if ($total_count[$input_user] == 0)
           {
               $return_count_rate[$input_user] = 0;
           }
           else
           {
               $return_count_rate[$input_user] = price($return_count[$input_user]/$total_count[$input_user],'4');
           }
           if ($total_to_rmb[$input_user] == 0)
           {
               $return_cost_rate[$input_user] = 0;
           }
           else
           {
               $return_cost_rate[$input_user] = price($return_to_rmb[$input_user]/$total_to_rmb[$input_user],'4');
           }
       }
       arsort($return_count);
       foreach ($return_count as $key => $vl)
       {
           $return_count[$key] = $vl;
       }
       foreach ($return_count as $key => $vl)
       {
           foreach ($user_arr as $user)
           {
               if($key == $user)
               {
                   $user_arrs[] = $key;
               }
           }
       }
       $data = array(
          'input_users'             => $input_users,
          'current_user'            => $input_user,
          'begin_time'              => $begin_time,
          'end_time'                => $end_time,
          'return_count'            => $return_count,
          'total_count'             => $total_count,
          'return_count_rate'       => $return_count_rate,
          'return_cost'             => $return_cost,
          'total_cost'              => $total_cost,
          'return_cost_rate'        => $return_cost_rate,
          'currencies'              => $currencies,
          'user_arr'                => $user_arrs,

      );
     $this->template->write_view('content', 'order/not_receive_all/by_input_account', $data);
     $this->template->add_js('static/js/sorttable.js');
     $this->template->render();
    }

    public function by_ship_confirm_user()
    {
       $this->set_2column('sidebar_statistics_order');
       $input_user = NULL;
       if(!$this->input->is_post())
       {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
       }
       else
       {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
       }
       $begin_time = to_utc_format($begin_time);
       $end_time = to_utc_format($end_time);
       $input_users = $this->order_model->fetch_input_user();
       $ship_users = $this->order_model->fetch_ship_confirm_user();
       foreach ($ship_users as $users)
       {
           if(empty($users->ship_confirm_user))
           {
               continue;
           }
           $ship_user_arr[] =   $users->ship_confirm_user;
       }
       $status_id = fetch_status_id('order_status', 'not_received_full_refunded');
       $currencies =  array('RMB', 'USD', 'AUD', 'GBP','EUR','HKD');

       foreach ($currencies as $currency)
       {
           $return_infos = $this->solr_statistics_model->fetch_return_info_by_ship_confirm_user($currency, $status_id, $begin_time, $end_time,$input_user);
           $return_datas = empty($return_infos->facets->ship_confirm_user)? NULL : $return_infos->facets->ship_confirm_user;
           $return_all_count = empty($return_infos->count)? 0 :  $return_infos->count;
           $return_all_cost[$currency] = empty($return_infos->sum)? 0 : $return_infos->sum;
           foreach ($ship_user_arr as $ship_user)
           {
               if (!empty($return_datas->$ship_user->count))
               {
                   $tmp_count = $return_datas->$ship_user->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($return_count[$ship_user]))
               {
                   $return_count[$ship_user] += $tmp_count;
               }
               else
               {
                   $return_count[$ship_user] = $tmp_count;
               }

               if(empty($return_datas->$ship_user->sum))
               {
                   $return_cost[$ship_user][$currency] = 0;
               }
               else
               {
                   $return_cost[$ship_user][$currency] = price($return_datas->$ship_user->sum);
               }
               if(isset($return_to_rmb[$ship_user]))
               {
                   $return_to_rmb[$ship_user] += $return_cost[$ship_user][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $return_to_rmb[$ship_user] = $return_cost[$ship_user][$currency];
               }
           }
       }

       foreach ($currencies as $currency)
       {
           $total_infos = $this->solr_statistics_model->fetch_total_info_by_ship_confirm_user($currency, $begin_time, $end_time, $input_user);
           $total_datas = empty($total_infos->facets->ship_confirm_user)? NULL : $total_infos->facets->ship_confirm_user;
           $total_all_count = empty($total_infos->count)? 0 :  $total_infos->count;
           $total_all_cost[$currency] = empty($total_infos->sum)? 0 : $total_infos->sum;
           foreach ($ship_user_arr as $ship_user)
           {
               if (!empty($total_datas->$ship_user->count))
               {
                   $tmp_count = $total_datas->$ship_user->count;
               }
               else
               {
                   $tmp_count = 0;
               }
               if(isset($total_count[$ship_user]))
               {
                   $total_count[$ship_user] += $tmp_count;
               }
               else
               {
                   $total_count[$ship_user] = $tmp_count;
               }

               if(empty($total_datas->$ship_user->sum))
               {
                   $total_cost[$ship_user][$currency] = 0;
               }
               else
               {
                   $total_cost[$ship_user][$currency] = price($total_datas->$ship_user->sum);
               }
               if(isset($total_to_rmb[$ship_user]))
               {
                   $total_to_rmb[$ship_user] += $total_cost[$ship_user][$currency] * $this->cur_rates[$currency];
               }
               else
               {
                   $total_to_rmb[$ship_user] = $total_cost[$ship_user][$currency];
               }

           }
       }

       foreach ($ship_user_arr as $ship_user)
       {
           if($total_count[$ship_user] == 0)
           {
               $return_count_rate[$ship_user] = 0;
           }
           else
           {
               $return_count_rate[$ship_user] = price($return_count[$ship_user]/$total_count[$ship_user],'4');
           }
           if($total_to_rmb[$ship_user] == 0)
           {
               $return_cost_rate[$ship_user] = 0;
           }
           else
           {
               $return_cost_rate[$ship_user] = price($return_to_rmb[$ship_user]/$total_to_rmb[$ship_user],'4');
           }
       }
       arsort($return_count);
       foreach ($return_count as $key => $vl)
       {
           $return_count[$key] = $vl;
       }
       foreach ($return_count as $key => $vl)
       {
           foreach ($ship_user_arr as $ship_user)
           {
               if($key == $ship_user)
               {
                   $ship_user_arrs[] = $key;
               }
           }
       }
       $data = array(
          'input_users'             => $input_users,
          'current_user'            => $input_user,
          'begin_time'              => $begin_time,
          'end_time'                => $end_time,
          'return_count'            => $return_count,
          'total_count'             => $total_count,
          'return_count_rate'       => $return_count_rate,
          'return_cost'             => $return_cost,
          'total_cost'              => $total_cost,
          'return_cost_rate'        => $return_cost_rate,
          'currencies'              => $currencies,
          'ship_users'              => $ship_user_arrs,

       );

      $this->template->write_view('content','order/not_receive_all/by_ship_confirm_user',$data);
      $this->template->add_js('static/js/sorttable.js');
      $this->template->render();
    }

    public function by_sku()
    {
       $this->set_2column('sidebar_statistics_order');
       $input_user = NULL;
       if(!$this->input->is_post())
       {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
       }
       else
       {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $input_user = $this->input->post('input_user');
       }
       $input_users = $this->order_model->fetch_input_user();
       $status_id = fetch_status_id('order_status', 'not_received_full_refunded');

       $skus_obj =  $this->solr_statistics_model->fetch_total_count_of_sku('skus', $begin_time, $end_time, $input_user);
       $skus = $skus_obj['facet'];
       $return_skus_obj  = $this->solr_statistics_model->fetch_return_count_of_sku('skus', $status_id, $begin_time, $end_time, $input_user);
       $return_skus = $return_skus_obj['facet'];
       $data = array(
          'input_users'             => $input_users,
          'current_user'            => $input_user,
          'begin_time'              => $begin_time,
          'end_time'                => $end_time,
          'return_skus'             => $return_skus,
          'skus'                    => $skus,

     );

      $this->template->write_view('content','order/not_receive_all/by_sku',$data);
      $this->template->add_js('static/js/sorttable.js');
      $this->template->render();
    }

}

?>
