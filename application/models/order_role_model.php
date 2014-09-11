<?php
class Order_role_model extends Base_model
{
    public function update_order_role($data)
    {
        $table = 'order_list';
        $where = array(
            'id' => $data['order_id']            ,
        );
        
        unset($data['order_id']);
        $this->update($table, $where, $data);
    }

    public function add_order_role($order_id)
    {
        if ( ! isset($this->CI->order_model))
        {
            $this->CI->load->model('order_model');
        }
        $order = $this->CI->order_model->fetch_order($order_id);
        
        if (empty($order))
        {
            echo $order_id, ': order is empty', "\n";
            return;
        }
        $skus = explode(',', $order->sku_str);

        if (empty($skus[0]))
        {
            echo $order_id, ': sku is empty', "\n";
            return;
        }

        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        if ( ! isset($this->CI->sale_order_model))
        {
            $this->CI->load->model('sale_order_model');
        }
        $purchaser_id_str = '';
        $count = count($skus);
        $index = 0;
        foreach ($skus as $sku)
        {
            $index++;
            if (empty($sku))
            {
                $purchaser_id_str = '';
                break;
            }
            $purchaser_id = $this->CI->product_model->fetch_product_purchaser_id_by_sku($sku);
            $purchaser_id_str .= $purchaser_id;
            if ($index < $count)
            {
                $purchaser_id_str .= ',';
            }
        }

        $stock_user_id = $this->CI->product_model->fetch_product_stock_user_id_by_sku($skus[0]);
        $developer_id = $this->CI->product_model->fetch_product_developer_id_by_sku($skus[0]);
        $tester_id = $this->CI->product_model->fetch_product_tester_id_by_sku($skus[0]);
        $saler_id = empty($order->to_email) ? 0 : $this->CI->sale_order_model->fetch_sale_id_by_paypal_email($order->to_email);

        $data = array(
            'order_id'          => $order->id,
            'stock_user_id'     => $stock_user_id,
            'purchaser_id_str'  => $purchaser_id_str,
            'developer_id'      => empty($developer_id) ? 0 : $developer_id,
            'saler_id'          => empty($saler_id) ? 0 : $saler_id,
            'tester_id'         => empty($tester_id) ? 0 : $tester_id,
        );
        $this->order_role_model->update_order_role($data);
    }
}

?>
