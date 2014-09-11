<?php

class Product_netname_model extends Base_model 
{
    public function update_netname($netname_id, $data)
    {
        $this->update('product_net_name', array('id' => $netname_id), $data);
    }

    public function fetch_netname($netname_id)
    {
        return $this->get_row('product_net_name', array('id' => $netname_id));
    }
}

?>
