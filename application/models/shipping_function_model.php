<?php
class Shipping_function_model extends Base_model
{
    public function fetch_all_weights_by_company($company_type_id)
    {
        $this->db->select('start_weight, end_weight');
        $this->db->distinct();
        $this->db->where(array('company_type_id' => $company_type_id));
        $query = $this->db->get('shipping_function');

        return $query->result();
    }

    public function fetch_rule_with_exact_weight($start_weight, $end_weight, $subarea_id, $company_type_id)
    {
        return $this->get_row(
            'shipping_function',
            array(
                'start_weight'      => $start_weight,
                'end_weight'        => $end_weight,
                'subarea_id'        => $subarea_id,
                'company_type_id'   => $company_type_id
            )
        );
    }

    public function fetch_rules_in_range($weight, $subarea_id)
    {
        return $this->get_result(
            'shipping_function',
            '*',
            array('start_weight <' => $weight, 'end_weight >=' => $weight, 'subarea_id' => $subarea_id)
        );
    }

    public function fetch_rule($start_weight, $end_weight, $subarea_id)
    {
        return $this->get_row(
            'shipping_function',
            array('start_weight' => $start_weight, 'end_weight' => $end_weight, 'subarea_id' => $subarea_id)
        );
    }

    public function add_new_rule($data)
    {
        $this->db->insert('shipping_function', $data);
    }

    public function drop_rule($start_weight, $end_weight, $company_type_id)
    {
        return $this->delete('shipping_function', array(
            'start_weight'      => $start_weight,
            'end_weight'        => $end_weight,
            'company_type_id'   => $company_type_id,
        ));
    }

    public function save_rule($where, $data)
    {
        return $this->update('shipping_function', $where, $data);
    }

    public function create_new_rules($company_type_ids, $subarea_id)
    {
        foreach ($company_type_ids as $ctid)
        {
            $result = $this->fetch_all_weights_by_company($ctid);
            //$result = $this->get_result('shipping_function', 'id', array('company_type_id' => $ctid));
            foreach ($result as $row)
            {
                $data = array(
                    'start_weight'      => $row->start_weight,
                    'end_weight'        => $row->end_weight,
                    'subarea_id'        => $subarea_id,
                    'company_type_id'   => $ctid,
                );
                $this->db->insert('shipping_function', $data);
            }
        }
    }

    public function  check_exists($where) {
        return parent::check_exists('shipping_function', $where);
    }

    public function fetch_global_rule($company_type_id)
    {
        return $this->get_one('shipping_global_function', 'global_rule', array('company_type_id' => $company_type_id));
    }

    public function save_global_rule($global_rule, $company_type_id)
    {
        $table = 'shipping_global_function';
        $where = array('company_type_id' => $company_type_id);
        $data = array('global_rule' => $global_rule);
        if (parent::check_exists($table, $where))
        {
            $this->update($table, $where, $data);
        }
        else
        {
            $data['company_type_id'] = $company_type_id;
            $this->db->insert($table, $data);
        }
    }
}
?>
