<?php
class Waiting_for_perfect_model extends Base_model {

    public function waiting_for_perfect($where){
        $this->set_offset('product_basic');
        $this->db->select('product_basic.*');
        $this->db->from('product_basic');
        $this->set_where('product_basic');
        $this->db->where($where);        
        $this->set_sort('product_basic');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_product_basic_count($where), 'product_basic');
        return $query->result();        
    }

    public function fetch_product_basic_count($where, $tag = TRUE ) {
        $this->db->from('product_basic');
        $this->db->where($where);
        if($tag)
        {
            $this->set_where('product_basic');
        }
        
       return $this->db->count_all_results();
    }

}
