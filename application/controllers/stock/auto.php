<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Auto extends Mallerp_no_key
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('product_model');
        $this->load->model('purchase_statistics_model');
    }

    public function auto_stock_check()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_stock_check.php') === FALSE)
        {
            exit;
        }
        $this->stock_model->stock_check(array(), TRUE);
        echo 'Done!';
    }

    public function auto_sale_record_check()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_sale_record_check.php') === FALSE)
        {
            exit;
        }
        //$this->stock_model->empty_sale_record();

        $this->stock_model->sale_record_check();
        echo 'Done!';
    }

    public function update_product_sale_state()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'update_product_sale_state.php') === FALSE)
        {
            exit;
        }

        $this->stock_model->product_clear_to_cessation();
        $this->stock_model->product_cessation_to_clear();
    }

    public function auto_calculate_product_ito_last_30_days()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_calculate_product_ito_last_30_days.php') === FALSE)
        {
            exit;
        }
        $products = $this->product_model->fetch_all_instock_clear_stock_products();
        $last_day = date('Y-m-d'); // next day
        $day_count = 30;
        foreach ($products as $row)
        {
            $product_id = $row->id;
            $result = $this->stock_model->calculate_product_ito($last_day, $day_count, $product_id);
            $ito = $result['ito'];
            $this->product_model->update_product_by_id($product_id, array('ito_in_30_days' => $ito));
            echo "id: $product_id " . $ito, "\n";
        }
    }

    public function auto_calculate_product_ito_last_month()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_calculate_product_ito_last_month.php') === FALSE)
        {
            exit;
        }
        $products = $this->product_model->fetch_all_instock_clear_stock_products();

        $last_month_time = strtotime('-1 month');
        $last_day = date('Y-m-01');  // next day
        $day_count = date('t', $last_month_time);
        $year = date('Y', $last_month_time);
        $month = date('m', $last_month_time);
        $total = count($products);

        foreach ($products as $row)
        {
            echo $total--, ' product left', "\n";
            $product_id = $row->id;
            $price = empty($row->price) ? 0 : $row->price;
            $where = array(
                'product_id'    => $product_id,
                'year'          => $year,
                'month'         => $month,
            );
            if ($this->stock_model->check_product_ito_record_exists($where))
            {
                continue;
            }
            $result = $this->stock_model->calculate_product_ito($last_day, $day_count, $product_id);
            $ito = $result['ito'];

            $total_sale_amount = $result['sale_count'] * $price;
            $total_stock_amount = $result['total_stock_count'] * $price;
            $data = array(
                'product_id'         => $product_id,
                'year'               => $year,
                'month'              => $month,
                'total_sale_amount'  => $total_sale_amount,
                'total_stock_amount' => $total_stock_amount,
                'ito'                => $ito,
                'purchaser_id'       => empty($row->purchaser_id) ? 0 : $row->purchaser_id,

            );
            $this->stock_model->save_product_ito_record($data);
        }
    }

    public function test($product_id)
    {
        $last_day = date('Y-m-d');
        $day_count = 30;
        echo $last_day, "<br/>";
        $result = $this->stock_model->calculate_product_ito($last_day, $day_count, $product_id);
        
        echo '<pre>';
        var_dump($result);
        echo "id: $product_id ";
    }
}

?>
