<?php
class Purchase_statistics_model extends Base_model
{
    public function fetch_scope_statistics($begin_time = NULL, $end_time = NULL, $priority = 1, $purchaser_id = NULL, $split_date = TRUE)
    {
        if ($split_date)
        {
            $scope = split_time_scope($begin_time, $end_time);
        }
        else
        {
            $scope[] = array(
                'begin_time' => $begin_time,
                'end_time'   => $end_time,
            );
        }
        $scope_statistics = array();
        $all_purchasers = array();
        foreach ($scope as $row)
        {
            $begin_time = $row['begin_time'];
            $end_time = $row['end_time'];
            $statistics = $this->fetch_statistics($begin_time, $end_time, $priority, $purchaser_id);
            $all_purchasers = array_merge($all_purchasers, array_keys($statistics));

            unset($statistics['purchasers']);
            $scope_statistics[] = array(
                'begin_time'    => $begin_time,
                'end_time'      => $end_time,
                'statistics'    => $statistics,
            );
        }

        return array($scope_statistics, $all_purchasers);
    }
    
    public function fetch_statistics($begin_time = NULL, $end_time = NULL, $priority = 1, $purchaser_id = NULL)
    {
        $sql =<<< SQL
(UNIX_TIMESTAMP(ship_confirm_date) - UNIX_TIMESTAMP(check_date)) as delay_times,
sku_str, qty_str, item_no, sys_remark, purchaser_id_str
SQL;
        $this->db->select($sql);
        $this->db->from('order_list_completed');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $this->db->where('check_date !=', '');
        $query = $this->db->get();
        $result = $query->result();

        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $this->db->where('check_date !=', '');
        $query = $this->db->get();
        $result = array_merge($result, $query->result());

        $current_user_id = get_current_user_id();
        
        $statistics = array();
        $purchaser_skus = array();
        $item_nos = array();

        $user_name_map = array();

        foreach ($result as $row)
        {
            $skus = explode(',', $row->sku_str);
            $qties = explode(',', $row->qty_str);
            $purchaser_ids = explode(',', $row->purchaser_id_str);

            $i = 0;
            $not_put = array();
            foreach ($skus as $sku)
            {
                if (empty($purchaser_ids[$i]))
                {
                    continue;
                }
                $tmp_purchaser_id = $purchaser_ids[$i];
                
                if ($priority > 1)
                {
                    if ($purchaser_id > 0 && $tmp_purchaser_id != $purchaser_id)
                    {
                        continue;
                    }
                }
                else
                {
                    if ($current_user_id != $tmp_purchaser_id)
                    {
                        continue;
                    }                   
                }
                
                if ( ! isset($statistics[$tmp_purchaser_id]))
                {
                    $statistics[$tmp_purchaser_id] = array();
                }
                $readable =  secs_to_readable($row->delay_times);
                $days = $readable['days'];

                // count once even if there are more products for one purchaser
                if ( ! isset($not_put[$tmp_purchaser_id]))
                {
                    if ($days > 15)
                    {
                        $days = 15;
                    }
                    if ( ! isset($statistics[$tmp_purchaser_id]['delay_times']))
                    {
                        $statistics[$tmp_purchaser_id]['delay_times'] = array();
                    }
                    if ( ! isset($statistics[$tmp_purchaser_id]['delay_times'][$days]))
                    {
                        $statistics[$tmp_purchaser_id]['delay_times'][$days] = 0;
                    }
                    $statistics[$tmp_purchaser_id]['delay_times'][$days]++;
                    $not_put[$tmp_purchaser_id] = TRUE;

                    if ($days > 2)
                    {
                        if (isset($user_name_map[$tmp_purchaser_id]))
                        {
                            $user_name = $user_name_map[$tmp_purchaser_id];
                        }
                        else
                        {
                            $user_name = fetch_user_name_by_id($tmp_purchaser_id);
                            $user_name_map[$tmp_purchaser_id] = $user_name;
                        }
                        $item_nos[] = array(
                            $user_name,
                            $days,
                            $row->item_no,
                            $row->sku_str,
                            $row->sys_remark,
                        );
                    }
                }

                if ( ! isset($statistics[$tmp_purchaser_id]['skus']))
                {
                    $statistics[$tmp_purchaser_id]['skus'] = $qties[$i];
                }
                else
                {
                    $statistics[$tmp_purchaser_id]['skus'] += $qties[$i];
                }

                $i++;
            }
        }

        $key = 'purchase_statistics_item_nos';
        if ($purchaser_id)
        {
            $key = 'purchase_statistics_item_nos_' . $purchaser_id;
        }
        else if ($priority == 1)
        {
            $key = 'purchase_statistics_item_nos_' . $current_user_id;
        }
        $this->cache->file->save($key, $item_nos, 60 * 60 * 24 * 30);  // 30 days
        
        return $statistics;
    }

    public function fetch_sale_statistics($begin_time = NULL, $end_time = NULL, $priority = 1, $purchaser_id = NULL)
    {
        $sql =<<< SQL
sku_str, qty_str, purchaser_id_str
SQL;
        $this->db->select($sql);
        $this->db->from('order_list_completed');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $query = $this->db->get();
        $result = $query->result();

        $this->db->select('product_basic.market_model');
        $this->db->from('product_basic');
        $querys = $this->db->get();
        $results = $query->result();


        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $query = $this->db->get();
        $result = array_merge($result, $query->result(),$results);

        $current_user_id = get_current_user_id();

        $statistics = array();
        $purchaser_skus = array();

        foreach ($result as $row)
        {
            $skus = explode(',', $row->sku_str);
            $qties = explode(',', $row->qty_str);
            $purchaser_ids = explode(',', $row->purchaser_id_str);

            $i = 0;
            $not_put = array();
            foreach ($skus as $sku)
            {
                if (empty($purchaser_ids[$i]))
                {
                    continue;
                }
                $tmp_purchaser_id = $purchaser_ids[$i];
                
                if ($priority > 1)
                {
                    if ($purchaser_id > 0 && $tmp_purchaser_id != $purchaser_id)
                    {
                        continue;
                    }
                }
                else
                {
                    if ($current_user_id != $tmp_purchaser_id)
                    {
                        continue;
                    }
                }

                if ( ! isset($statistics[$tmp_purchaser_id]))
                {
                    $statistics[$tmp_purchaser_id] = array();
                }

                if ( ! isset($statistics[$tmp_purchaser_id][$sku]))
                {
                    $statistics[$tmp_purchaser_id][$sku] = $qties[$i];
                }
                else
                {
                    $statistics[$tmp_purchaser_id][$sku] += $qties[$i];
                }

                $i++;
            }
        }

        return $statistics;
    }

    public function department_development_statistics($begin_time, $end_time, $sale_begin_time, $sale_end_time, $developer_id)
    {
        $CI = get_instance();
        $priority = $this->user_model->fetch_user_priority_by_system_code('purchase');
        $current_user_id = get_current_user_id();
        $results = $this->CI->purchase_statistics_model->fetch_all_product_developer_id_and_sku($begin_time, $end_time);
        $sku_for_developer_id = array();
        foreach ($results as $result) {
            $sku_for_developer_id[$result->sku] = $result->product_develper_id;
        }

        $results = $this->CI->solr_statistics_model->fetch_development_statistics($sale_begin_time,$sale_end_time);

        $statistics = array();
        $developer_skus = array();
        $product = array();
        $is_super_user = $CI->is_super_user();
        foreach ($results as $result)
        {
            $result = is_array($result) ? $result : array();
            foreach($result as $row)
            {
            $skus = $row->skus;
            $qties = $row->qties;

            for($i = 0; $i < count($skus); $i++)
            {
                if ( ! array_key_exists($skus[$i], $developer_skus))
                {
                    $tmp_developer_id = @$sku_for_developer_id[$skus[$i]];
                    $developer_skus[$skus[$i]] = $tmp_developer_id;
                    if($priority > 1 || $is_super_user)
                    {
                        if (!empty($developer_id) && $tmp_developer_id != $developer_id)
                        {
                            continue;
                        }
                    } elseif ($current_user_id != $tmp_developer_id)
                        {
                            continue;
                        }
                    
                    if ( ! isset($statistics[$tmp_developer_id]))
                    {
                        $statistics[$tmp_developer_id] = array();
                    }

                    $statistics[$tmp_developer_id]['skus'][] = $skus[$i];

                    if(! isset($product['qty'][$skus[$i]]))
                    {
                        $product['qty'][$skus[$i]] =  $qties[$i];
                        $product['order'][$skus[$i]] = 1;
                    }
                }
                else
                {
                    $tmp_developer_id = $developer_skus[$skus[$i]];
                    if($priority > 1 || $is_super_user)
                    {
                        if (!empty($developer_id) && $tmp_developer_id != $developer_id)
                        {
                            continue;
                        }
                    } elseif ($current_user_id != $tmp_developer_id)
                        {
                            continue;
                        }
                   
                    $product['qty'][$skus[$i]] +=  @$qties[$i];
                    $product['order'][$skus[$i]] ++;
                }
            }
            }
        }
        return array($product, $statistics);
    }

    public function fetch_product_developer_id_by_sku($sku, $begin_time, $end_time)
    {
        $this->db->select('product_develper_id');
        $this->db->from('product_basic');
        $where = array(
            'sku'                 => $sku,
            'updated_date >='     => $begin_time,
            'updated_date <='     => $end_time,
         );
         $this->db->where($where);
         $query = $this->db->get();
         $result = $query->row();
         
         return  isset($result->product_develper_id) ? $result->product_develper_id : '-1';
    }
    
    public function fetch_second_grance_rate_data($year,$month)
    {
        $this->set_offset('statistics');
        $this->db->select('second_glance_amount,totable_amount,second_glance_rate,saler_id');
        $this->db->from('customer_second_glance_rate');
        $array = array('year' => $year,'month' => $month);
        $this->db->where($array);
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $total =  $this->fetch_grance_rate_total_count($year,$month);
        $this->set_total($total, 'statistics');
        return $query->result();
    }
    public function fetch_grance_rate_total_count($year,$month)
    {
        $this->db->select('*');
        $this->db->from('customer_second_glance_rate');
        $array = array('year' => $year,'month' => $month);
        $this->db->where($array);
        $this->set_where('statistics');
        return $this->db->count_all_results();
    }
    public function fetch_saler_name_by_id($saler_id)
    {
        return $this->get_one('user', 'name', array('id' => $saler_id));
    }

    //
    public function fetch_saler_id()
    {
        $this->db->select('saler_id');
        $this->db->from('order_list');
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_cur_month_sales_total_amount($saler_id)
    {
        $cur_time = date('Y-m', strtotime('-1 month'));
        $this->db->select('saler_id,sum(gross) as total_amount,currency');
        $this->db->from('order_list');
        $this->db->like('input_date',$cur_time,'after');
        $this->db->where(array('saler_id' => $saler_id));
        //$this->db->where(array('saler_id !=' => NULL));
        $this->db->group_by('currency');
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_buyer_id_in_saler_id($saler_id)
    {
        $cur_time = date('Y-m', strtotime('-1 month'));
        $this->db->select('saler_id,buyer_id');
        $this->db->from('order_list');
        $this->db->like('input_date',$cur_time,'after');
        $this->db->where(array('saler_id' => $saler_id));
        $this->db->where(array('buyer_id !=' => " "));
        $query = $this->db->get();
        return $query->result();
    }

    public function check_second_ksk_in_past_six_month($buyer_id)
    {
        $cur_time = date('Y-m', strtotime('-1 month'));
        $past_time = date('Y-m', strtotime('-7 month'));
        $first_day_time = strtotime($cur_time."-01");
        $past_day_time = strtotime($past_time."-01");
        $first_day = date("Y-m-d H:i:s",$first_day_time);
        $past_day = date("Y-m-d H:i:s",$past_day_time);
        $this->db->select('buyer_id,gross');
        $this->db->from('order_list');
        $array = array('input_date >=' => $past_day, 'input_date <' => $first_day);
        $this->db->where($array);
        $this->db->where('buyer_id', $buyer_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function fetch_amount_by_buyer_id($saler_id,$buyer_id)
    {
        $cur_time = date('Y-m', strtotime('-1 month'));
        $this->db->select('saler_id,buyer_id,gross,currency');
        $this->db->from('order_list');
        $this->db->like('input_date',$cur_time,'after');
        $this->db->where(array('saler_id' => $saler_id,'buyer_id' => $buyer_id));
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_all_product_developer_id_and_sku($begin_time, $end_time)
    {
        $this->db->select('product_develper_id, sku');
        $this->db->from('product_basic');
        $where = array(
            'updated_date >='     => $begin_time,
            'updated_date <='     => $end_time,
            'product_develper_id >' => 0,
         );
         $this->db->where($where);
         $query = $this->db->get();
         $result = $query->result();

         return $result;
    }
}
?>
