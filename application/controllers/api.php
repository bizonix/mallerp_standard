<?php
class Api extends CI_Controller
{
    protected $nav;
    const NAME = 'void';
    private $referrer_url;
	private $return_array;
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('order_model');
		$this->load->model('ebay_order_model');
        $this->load->model('product_model');
		$this->load->model('product_makeup_sku_model');
        $this->load->model('order_shipping_record_model');
        $this->load->model('shipping_code_model');
		$this->load->model('shipping_subarea_model');
		$this->load->library('excel');
        $this->load->helper('shipping_helper');
		$this->load->helper('db_helper');

        date_default_timezone_set(DEFAULT_TIMEZONE);
        
        // check user session
        $current_uri = fetch_request_uri();
        
        if ( ! $this->input->is_post())
        {
            if (debug_mode ())
            {
                $this->output->enable_profiler(TRUE);
            }
        }
        
        $this->load->driver('cache', array('backup' => 'file'));
    }

    public function index()
    {
        echo "mallerp api page";
    }
	public function get_current_language()
    {
        return DEFAULT_LANGUAGE;
    }
	public function ydf_excel($date=NULL)
    {
		header("Content-Type: text/html; charset=gb2312");
		if($date==NULL){$date=date('Y-m-d');}
        $orders=$this->get_ydf_order($date);
		$head = array(
            	'Sales Record Number',
				'Buyer Fullname',
				'Buyer Company',
				'Buyer Address 1',
				'Buyer Address 2',
				'Buyer City',
				'Buyer State',
				'Buyer Zip',
				'Buyer Phone Number',
				'Buyer Country',
				'Custom Label',
				'Description EN',
				'Description CN',
				'HS Code',
				'Quantity',
				'Sale Price',
				'Country of Manufacture',
				'Mark',
				'weight',
				'Length',
				'Width',
				'Height',
				'Shipping Service',
				'Shipping Service Name',
				'Track Number'
        );
		foreach($orders as $order)
		{
			$skus = explode(',', $order->sku_str);
			$qties = explode(',', $order->qty_str);
			$shipping_method = shipping_method($order->is_register);
			//echo $order->id."<br>";
			$count=count($skus);
			$product_name='';
			$product_name_en='';
			$qty=0;
			$weight=0;
			$rmb = price($this->order_model->calc_currency($order->currency, $order->gross));
			for ($i = 0; $i < $count; $i++) {
				if($i==0)
				{
					$product_name .= iconv("UTF-8","GB2312//IGNORE",get_product_name($skus[$i]));
					$product_name_en .= iconv("UTF-8","GB2312//IGNORE",get_product_name_en($skus[$i]));
				}
				
				$qty+=$qties[$i];
				$weight+=get_weight_by_sku($skus[$i])*$skus[$i];
			}
			$shipping_method_name_cn= iconv("UTF-8","GB2312//IGNORE",$shipping_method->name_cn);
			$data[]=array(
				$order->id,
				$order->name,
				" ",
				$order->address_line_1,
				$order->address_line_2,
				$order->town_city,
				$order->state_province,
				$order->zip_code,
				$order->contact_phone_number,
				$order->country,
				$skus[0],
				$product_name_en,
				$product_name,
				" ",
				$qty,
				$rmb,
				" ",
				" ",
				$weight,
				" ",
				" ",
				" ",
				$shipping_method->ydf_code,
				$shipping_method_name_cn,
				$order->track_number
						  );
		}
		$this->excel->array_to_excel($data, $head, 'ydf_list_' . $date);
    }
	public function get_ydf_order($date) {
		$is_register=array('HKR','CNR','CHR','SGR','SGS','CHS','CNS','HKS','MYR','MYS');
        $this->db->select('*');
        $this->db->from('order_list');
		$this->db->where_in('is_register', $is_register);
		$this->db->like(array('ship_confirm_date' => $date));
        $query = $this->db->get();
        return $query->result();
    }
	

    
}

?>
