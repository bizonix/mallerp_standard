<?php
class Shipping_statistics_model extends Base_model
{
    public function fetch_scope_statistics($begin_time = NULL, $end_time = NULL, $priority = 1, $shipper = NULL, $split_date = TRUE)
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

        $shippers = array();
        foreach ($scope as $row)
        {
            $begin_time = $row['begin_time'];
            $end_time = $row['end_time'];
            $statistics = $this->fetch_statistics($begin_time, $end_time, $priority, $shipper);

            $shippers = array_unique(array_merge($shippers, $statistics['shippers']));
            unset($statistics['shippers']);
            $scope_statistics[] = array(
                'begin_time'    => $begin_time,
                'end_time'      => $end_time,
                'statistics'    => $statistics,
            );
        }

        return array($scope_statistics, $shippers);
    }
    
    public function fetch_statistics($begin_time = NULL, $end_time = NULL, $priority = 1, $shipper = NULL)
    {
        $this->db->select('is_register, ship_confirm_date, ship_confirm_user, ship_weight, sub_ship_weight_str, qty_str');
        $this->db->from('order_list');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);

        if ($priority <= 1)
        {
            $this->db->where('ship_confirm_user', get_current_user_name());
        }
        else if ( ! empty($shipper))
        {
            $this->db->where('ship_confirm_user', $shipper);
        }
        $query = $this->db->get();

        $restult = $query->result();
        $statistics = array();

        $statistics = array(
            'shippers' => array(),
        );

        foreach ($restult as $row)
        {
            $qties = array_sum(explode(',', $row->qty_str));
            $package_count = substr_count($row->sub_ship_weight_str, ',') + 1;

            if (isset($statistics[$row->is_register]))
            {
                if (isset($statistics[$row->is_register][$row->ship_confirm_user]))
                {
                    $data = $statistics[$row->is_register][$row->ship_confirm_user];
                    $new_data = array(
                        'count'         => $data['count'] + 1,
                        'package_count' => $data['package_count'] + $package_count,
                        'ship_weight'   => $data['ship_weight'] + $row->ship_weight,
                        'qty'           => $qties + $data['qty'],
                    );
                    $statistics[$row->is_register][$row->ship_confirm_user] = $new_data;
                }
                else
                {
                    $data = array(
                        'count'         => 1,
                        'package_count' => $package_count,
                        'ship_weight'   => $row->ship_weight,
                        'qty'           => $qties,
                    );
                    $statistics[$row->is_register][$row->ship_confirm_user] = $data;
                }
            }
            else
            {
                $statistics[$row->is_register] = array();                

                $data = array(
                    'count'         => 1,
                    'package_count' => $package_count,
                    'ship_weight'   => $row->ship_weight,
                    'qty'           => $qties,
                );
                $statistics[$row->is_register][$row->ship_confirm_user] = $data;
            }


            if ( ! in_array($row->ship_confirm_user, $statistics['shippers']))
            {
                $statistics['shippers'][] = $row->ship_confirm_user;
            }
        }
        return $statistics;
    }



    public function fetch_scope_statistics_by_stock_user($begin_time = NULL, $end_time = NULL, $priority = 1, $shipper = NULL, $split_date = TRUE)
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

        $stock_user = array();
        foreach ($scope as $row)
        {
            $begin_time = $row['begin_time'];
            $end_time = $row['end_time'];
            $statistics = $this->fetch_statistics_by_stock_user($begin_time, $end_time, $priority, $shipper);
            $stock_user = array_unique(array_merge($stock_user, $statistics['stock_user']));
            unset($statistics['stock_user']);
            $scope_statistics[] = array(
                'begin_time'    => $begin_time,
                'end_time'      => $end_time,
                'statistics'    => $statistics,
            );
        }

        return array($scope_statistics, $stock_user);
    }

    public function fetch_statistics_by_stock_user($begin_time = NULL, $end_time = NULL, $priority = 1, $stock_user_id = NULL)
    {
        $this->db->select('o.is_register, o.ship_confirm_date, o.ship_weight, o.qty_str, u.name as stock_user');
        $this->db->from('order_list as o');
        $this->db->join('user as u','o.stock_user_id = u.id');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);

        if ($priority <= 1)
        {
            $this->db->where('stock_user_id', get_current_user_id());
        }
        else if ( ! empty($stock_user_id))
        {
            $this->db->where('stock_user_id', $stock_user_id);
        }
        $query = $this->db->get();

        $restult = $query->result();
        $statistics = array();

        $statistics = array(
            'stock_user' => array(),
        );

        foreach ($restult as $row)
        {
            $qties = array_sum(explode(',', $row->qty_str));

            if (isset($statistics[$row->is_register]))
            {
                if (isset($statistics[$row->is_register][$row->stock_user]))
                {
                    $data = $statistics[$row->is_register][$row->stock_user];
                    $new_data = array(
                        'count'         => $data['count'] + 1,
                        'ship_weight'   => $data['ship_weight'] + $row->ship_weight,
                        'qty'           => $qties + $data['qty'],
                    );
                    $statistics[$row->is_register][$row->stock_user] = $new_data;
                }
                else
                {
                    $data = array(
                        'count'         => 1,
                        'ship_weight'   => $row->ship_weight,
                        'qty'           => $qties,
                    );
                    $statistics[$row->is_register][$row->stock_user] = $data;
                }
            }
            else
            {
                $statistics[$row->is_register] = array();

                $data = array(
                    'count'         => 1,
                    'ship_weight'   => $row->ship_weight,
                    'qty'           => $qties,
                );
                $statistics[$row->is_register][$row->stock_user] = $data;
            }


            if ( ! in_array($row->stock_user, $statistics['stock_user']))
            {
                $statistics['stock_user'][] = $row->stock_user;
            }
        }

        return $statistics;
    }
}
?>
