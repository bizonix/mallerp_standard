<?php
class Fee_price_model extends Base_model {
    
    public function  __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function fetch_all_paypal_cost()
    {
        $this->db->select('p.*,user.name as u_name');
        $this->db->from('paypal_cost as p');
        $this->db->join('user', 'user.id = p.creator');
        $this->db->order_by('created_date');

        $query = $this->db->get();

        return $query->result();
    }

    public function add_currency_code($data)
    {
        $this->db->insert('paypal_cost', $data);
    }

    public function drop_paypal_cost($id)
    {
        $this->delete('paypal_cost', array('id' => $id));
    }


    public function update_exchange_paypal_cost($id, $type, $value)
    {
        $this->update(
            'paypal_cost',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function fetch_paypal_cost_by_id($id)
    {

        $this->db->select('*');
        $this->db->from('paypal_cost');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();

    }

    public function fetch_all_eshop_cost()
    {
        $this->db->select('e.*,user.name as u_name, eshop_code.name as code_name, ec.category as category, ec.id as ec_id, ec.eshop_code as ec_code,');
        $this->db->from('eshop_list_fee as e');
        $this->db->join('user', 'user.id = e.creator');
        $this->db->join('eshop_code', 'eshop_code.code = e.eshop_code','left');
        $this->db->join('eshop_category as ec', 'ec.id = e.category_id ','left');
        $this->db->order_by('eshop_code','sale_mode');

        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_all_eshop_code()
    {
        $this->db->select('*');
        $this->db->from('eshop_code');
        $this->db->order_by('order');

        $query = $this->db->get();

        return $query->result();
    }
    public function fetch_all_sale_mode()
    {
        $this->db->select('*');
        $this->db->from('eshop_sale_mode');
        $this->db->order_by('id');

        $query = $this->db->get();

        return $query->result();
    }

    public function add_currency_eshop($data)
    {
        $this->db->insert('eshop_list_fee', $data);
    }

    public function drop_eshop($id)
    {
        $this->delete('eshop_list_fee', array('id' => $id));
    }

    public function update_exchange_eshop($id, $type, $value)
    {
        $this->update(
            'eshop_list_fee',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function fetch_eshop_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('eshop_list_fee');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_eshop_currency_code($eshop_code)
    {
        return $this->get_one('eshop_code', 'currency', array('code' => $eshop_code));
    }

    public function fetch_eshop_list_formula($eshop_code, $sale_mode, $eshop_category, $price)
    {
        $where = array(
            'eshop_code'        => $eshop_code,
            'sale_mode'         => $sale_mode,
            'category_id'       => $eshop_category,
            'start_price <'     => $price,
            'end_price >='      => $price,
        );
        $formula = $this->get_one('eshop_list_fee', 'formula', $where);

        if ($formula !== NULL)
        {
            return $formula;
        }
        // try default catalog
        $where['category_id'] = 0;
        
        return $this->get_one('eshop_list_fee', 'formula', $where);
    }

    public function fetch_all_trade_cost()
    {
        $this->db->select('e.*,user.name as u_name, eshop_code.name as code_name, ec.category as category, ec.id as ec_id, ec.eshop_code as ec_code');
        $this->db->from('eshop_trade_fee as e');
        $this->db->join('user', 'user.id = e.creator');
        $this->db->join('eshop_code', 'eshop_code.code = e.eshop_code','left');
        $this->db->join('eshop_category as ec', 'ec.id = e.category_id ','left');
        $this->db->order_by('eshop_code','sale_mode');

        $query = $this->db->get();

        return $query->result();
    }

    public function add_currency_trade($data)
    {
        $this->db->insert('eshop_trade_fee', $data);
    }

    public function drop_trade($id)
    {
        $this->delete('eshop_trade_fee', array('id' => $id));
    }

    public function update_exchange_trade($id, $type, $value)
    {
        $this->update(
            'eshop_trade_fee',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function fetch_trade_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('eshop_trade_fee');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_all_categories()
    {
        $this->db->select('e.*,user.name as u_name, eshop_code.name as code_name');
        $this->db->from('eshop_category as e');
        $this->db->join('user', 'user.id = e.creator');
        $this->db->join('eshop_code', 'eshop_code.code = e.eshop_code','left');
        $this->db->order_by('e.eshop_code','e.category');

        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_categories_by_eshop_code($eshop_code)
    {
        $this->db->select('e.*,user.name as u_name, eshop_code.name as code_name');
        $this->db->from('eshop_category as e');
        $this->db->join('user', 'user.id = e.creator');
        $this->db->join('eshop_code', 'eshop_code.code = e.eshop_code','left');
        $this->db->where(array('e.eshop_code' => $eshop_code));

        $query = $this->db->get();

        return $query->result();
    }

    public function add_currency_category($data)
    {
        $this->db->insert('eshop_category', $data);
    }

    public function drop_category($id)
    {
        $this->delete('eshop_category', array('id' => $id));
    }

    public function update_exchange_category($id, $type, $value)
    {
        $this->update(
            'eshop_category',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function fetch_category_by_id($id)
    {
        $this->db->select('ec.*, eshop_code.name as name');
        $this->db->from('eshop_category as ec');
        $this->db->join('eshop_code', 'ec.eshop_code = eshop_code.code','left');
        $this->db->where(array('ec.id' => $id));

        $query = $this->db->get();

        return $query->row();
    }
    
    public function fetch_eshop_trade_formula($eshop_code, $sale_mode, $eshop_category, $price)
    {
        $where = array(
            'eshop_code'        => $eshop_code,
            'sale_mode'         => $sale_mode,
            'category_id'       => $eshop_category,
            'start_price <'     => $price,
            'end_price >='      => $price,
        );
        $formula = $this->get_one('eshop_trade_fee', 'formula', $where);

        if ($formula !== NULL)
        {
            return $formula;
        }
        // try default catalog
        $where['category_id'] = 0;

        return $this->get_one('eshop_trade_fee', 'formula', $where);
    }
    
    public function drop_eshop_code($id)
    {
        $this->delete('eshop_code', array('id' => $id));
    }

    public function add_eshop_code($data)
    {
        $this->db->insert('eshop_code', $data);
    }

    public function fetch_eshop_code($id)
    {
        $this->db->select('*');
        $this->db->from('eshop_code');     
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }
    
    public function verigy_eshop_code($id, $type, $value)
    {
        $this->update(
            'eshop_code',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    
}

?>
