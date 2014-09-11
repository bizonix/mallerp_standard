<?php
require_once APPPATH . 'controllers/sale/sale' . EXT;

class Taobao_sale_statistic extends Sale
{
    public $cur_rates;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('solr');
        $this->load->model('solr/catalog_statistic_model');
        $this->load->model('user_model');
        $this->load->model('rate_model');
        $this->load->model('order_model');
        $rates = $this->order_model->fetch_currency();
        foreach ($rates as $rate)
        {
            $this->cur_rates[$rate->name_en] = $rate->ex_rate;
        }
    }

    public function statistic()
    {        
        $start_second = mktime();
        $group_id = '';
        if(!$this->input->is_post())
        {
             $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
             $end_time = date("Y-m-d H:i:s");
        }
        else
        {
             $begin_time = $this->input->post('begin_time');
             $end_time = $this->input->post('end_time');
             $group_id = $this->input->post('group');
        }
        $sku_prices = $this->catalog_statistic_model->fetch_sku_price();
        $prices = array();
        foreach ($sku_prices as $sku_price)
        {
            $prices[$sku_price->sku] = $sku_price->price;
        }

        $status_id_1 = fetch_status_id('order_status', 'wait_for_feedback');
        $status_id_2 = fetch_status_id('order_status', 'received');
        $status_ids  = array($status_id_1, $status_id_2);
        $groups = $this->user_model->fetch_all_groups_by_system_code('order');
        $group_users = array();
        $group_users = $this->user_model->fetch_all_users_by_group_id($group_id);
        $saler_ids = array_flip($group_users);
        if (empty($saler_ids))
        {
            $saler_ids = array('71', '121', '100', '188', '189');
        }
        $saler_skus = $this->catalog_statistic_model->fetch_dep_saler_sku($saler_ids);

        $prices = array();
        $salers = array();
        foreach ($saler_skus as $saler_sku)
        {
            $salers[$saler_sku->saler_id][$saler_sku->sku] = $saler_sku->sku;
        }

        $datas_arr = $this->catalog_statistic_model->fetch_order_infos($begin_time, $end_time, $status_ids);
        //$datas = isset($datas_arr[0])? $datas_arr[0] :NULL;
        $num = $datas_arr[1];
        echo $num."<br>";
        //echo "<pre>";
        //print_r($datas);
        $limit = 4000;
        $loop = ceil($num / $limit);
        echo $loop;
        $amount_arr = array();
        $order_counts = array();
        for ($i=0; $i<$loop; $i++)   
        {
    
            if ($i > 0)
            {
                $datas_arr = array();
                $datas_arr = $this->catalog_statistic_model->fetch_order_infos($begin_time, $end_time, $status_ids, $limit);
                $limit += 4000;
            }
            $datas = isset($datas_arr[0])? $datas_arr[0] :NULL;
            //echo $i;
            foreach ($saler_ids as $saler_name => $saler_id)
            {
                $amount = 0;
                $order_count = 0;
                if ( !isset($salers[$saler_id]))
                {
                    continue;
                }
                if ( empty ($datas))
                {
                    break;
                }

                foreach ($datas as $key => $data)
                {
                    $count = count($data->skus);
                    if ($count <= 1)
                    {
                        foreach ($data->skus as $value)
                        {
                            $sku = $value;
                        }
                        if (in_array($sku, $salers[$saler_id]))
                        {

                            if ($data->gross != 0)
                            {
                                $amount += $data->gross * $this->cur_rates[$data->currency];
                            }
                            else
                            {
                                $amount += $data->net * $this->cur_rates[$data->currency];
                            }
                            $order_count++;
                            unset($datas[$key]);
                        }
                        else
                        {
                            continue;
                        }
                    }
                    else
                    {
                        $total_price = 0;
                        for($j=0; $j<$count; $j++)
                        {
                            if ( ! isset($prices[$data->skus[$j]]))
                            {
                                $sku_price = 0;
                            }
                            else
                            {
                                $sku_price = $prices[$data->skus[$j]];
                            }
                            $total_price += $sku_price * $data->qties[$j];
                        }
                        foreach ($data->skus as $key2 => $sku)
                        {
                            $rates = array();
                            if ($total_price == 0)
                            {
                                $rates[$sku] = 0;
                            }
                            else
                            {
                                $rates[$sku] = $prices[$sku] / $total_price ;
                            }

                            $k = 0;
                            if(in_array($sku, $salers[$saler_id]))
                            {
                                $k++;
                                if ($data->gross != 0)
                                {
                                    $amount += $data->gross * $this->cur_rates[$data->currency] * $rates[$sku];
                                }
                                else
                                {
                                    $amount += $data->net * $this->cur_rates[$data->currency] * $rates[$sku];
                                }
                                if ($k <= 1)
                                {
                                    $order_count++;
                                }
                                unset($datas[$key]->skus[$key2]);
                                unset($datas[$key]->qties[$key2]);
                                sort($data->skus);
                                sort($data->qties);
                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                }#--eof foreach $data

                if (isset($amount_arr[$saler_id]))
                {
                    $amount_arr[$saler_id] += price($amount);
                }
                else
                {
                    $amount_arr[$saler_id] = price($amount);
                }
                //$amount_arr[$saler_id] += price($amount);
                if (isset($order_counts[$saler_id]))
                {
                    $order_counts[$saler_id] += $order_count;
                }
                else
                {
                    $order_counts[$saler_id] = $order_count;
                }
                //$order_counts[$saler_id] += $order_count;

            }#--eof foreach $saler_id

        echo "abc@";
        //break;
        }#eof--for

        

        print_r($saler_ids);
        echo "<hr>";
        print_r($order_counts);
        echo "<hr>";
        print_r($amount_arr);


        $end_second = mktime();
        $cost_second = $end_second - $start_second;
        echo "<br>用时:".$cost_second."秒";
        $data = array(
             'begin_time'        => $begin_time,
             'end_time'          => $end_time,
             'groups'            => $groups,
             'cur_group'         => $group_id,
             'group_users'       => $group_users,
             'saler_ids'         => $saler_ids,
             'amount_arr'        => $amount_arr,
             'order_counts'      => $order_counts,

        );
        $this->template->write_view('content','sale/taobao/sale_statistic', $data);
        $this->template->render();

    }
}
?>
