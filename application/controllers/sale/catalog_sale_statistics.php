<?php
require_once APPPATH . 'controllers/sale/sale' . EXT;

class Catalog_sale_statistics extends Sale
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
        $group_id = '120';
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
        $groups_a[] = lang('select');
        $groups_b = $this->user_model->fetch_all_groups_by_system_code('order');
        $groups = $groups_a + $groups_b;
        $group_users = array();
        $group_users = $this->user_model->fetch_all_users_by_group_id($group_id);
        $saler_ids = array_flip($group_users);
        $saler_skus = $this->catalog_statistic_model->fetch_dep_saler_sku($saler_ids);
        foreach ($saler_skus as $saler_sku)
        {
            $sku_salers[$saler_sku->sku] = $saler_sku->saler_id;
        }
        
        $datas = $this->catalog_statistic_model->fetch_order_infos($begin_time, $end_time, $status_ids);
        $amount = array();
        $order_counts = array();
        foreach ($datas as  $data)
        {
            $skus = $data->skus;
            $qties = $data->qties;
            $currency = $data->currency;
            $gross = $data->gross;
            $net = $data->net;
            $count = count($skus);
            $tmp_saler_arr = array();
            if ($count <= 1)
            {
                if ( ! isset($sku_salers[$skus[0]]))
                {
                    continue;
                }
                $tmp_saler_id = $sku_salers[$skus[0]];
                if ($gross != 0)
                {
                    if ( ! isset($amount[$tmp_saler_id]))
                    {
                        $amount[$tmp_saler_id] = $gross * $this->cur_rates[$currency];
                    }
                    else
                    {
                        $amount[$tmp_saler_id] += $gross * $this->cur_rates[$currency];
                    }
                }
                else
                {
                    if (! isset($amount[$tmp_saler_id]))
                    {
                        $amount[$tmp_saler_id] = $net * $this->cur_rates[$currency];
                    }
                    else
                    {
                        $amount[$tmp_saler_id] += $net * $this->cur_rates[$currency];
                    }
                }
                if (! isset($order_counts[$tmp_saler_id]))
                {
                    $order_counts[$tmp_saler_id] = 0;
                }
                $order_counts[$tmp_saler_id]++;
            }
            else
            {
                $total_price = 0;
                for($i=0; $i < $count; $i++)
                {
                    if ( ! isset($prices[$skus[$i]]))
                    {
                        $sku_price = 0;
                    }
                    else
                    {
                        $sku_price = $prices[$skus[$i]];
                    }
                    $total_price += $sku_price * $qties[$i];
                }

                for ($j=0; $j < $count; $j++)
                {
                    if ( ! isset($sku_salers[$skus[$j]]))
                    {
                        continue;
                    }
                    else
                    {
                        $tmp_saler_id = $sku_salers[$skus[$j]];
                    }
                    $rates = array();
                    if ($total_price == 0)
                    {
                        $rates = 0;
                    }
                    else
                    {
                        $rates = ($prices[$skus[$j]] * $qties[$j]) / $total_price;
                    }
                    if ($gross != 0)
                    {
                        if ( ! isset($amount[$tmp_saler_id]))
                        {
                            $amount[$tmp_saler_id] = $gross * $rates * $this->cur_rates[$currency];
                        }
                        else
                        {
                            $amount[$tmp_saler_id] +=  $gross * $rates * $this->cur_rates[$currency];
                        }
                    }
                    else
                    {
                        if ( ! isset($amount[$tmp_saler_id]))
                        {
                            $amount[$tmp_saler_id] = $net * $rates * $this->cur_rates[$currency];
                        }
                        else
                        {
                            $amount[$tmp_saler_id] += $net * $rates * $this->cur_rates[$currency];
                        }
                    }
                    if ( ! isset($order_counts[$tmp_saler_id]))
                    {
                        $order_counts[$tmp_saler_id] = 0;
                    }
                    if ( ! in_array($tmp_saler_id, $tmp_saler_arr))
                    {
                        $order_counts[$tmp_saler_id]++;
                    }
                    $tmp_saler_arr[] = $tmp_saler_id;
                }
            }
        }#eof--$datas
        $data = array(
             'begin_time'        => $begin_time,
             'end_time'          => $end_time,
             'groups'            => $groups,
             'cur_group'         => $group_id,
             'group_users'       => $group_users,
             'saler_ids'         => $saler_ids,
             'amount'            => $amount,
             'order_counts'      => $order_counts,

        );
        $this->template->write_view('content','sale/taobao/sale_statistic', $data);
        $this->template->render();

    }
}
?>
