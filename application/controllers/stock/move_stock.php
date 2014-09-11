<?php
require_once APPPATH.'controllers/stock/stock'.EXT;

class Move_stock extends Stock
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('abroad_stock_model');
        $this->load->model('product_model');
		$this->load->model('order_model');
		$this->load->model('sale_model');
		$this->load->model('product_makeup_sku_model');
		$this->load->model('shipping_code_model');
        $this->load->library('form_validation');
        $this->load->helper('validation_helper');
    }

    public function move_form()
	{
        $currency = $this->order_model->fetch_currency();

        $income_types = $this->order_model->fetch_all_income_type();

        $option = array();
        foreach ($income_types as $income_type) {
            $option[$income_type->receipt_name] = $income_type->receipt_name;
        }

        $currency_arr = array();
        foreach ($currency as $v) {
            $currency_arr[$v->code] = $v->name_en;
        }

        $data = array(
            'order' => NULL,
            'currency_arr' => $currency_arr,
            'action' => 'edit',
            'income_type' => $option,
        );

        $this->template->write_view('content', 'stock/abroad_stock/move_form', $data);
        $this->template->add_js('static/js/ajax/move_stock.js');
        $this->template->render();
	}
	public function move_save()
	{
		if ($this->input->is_post())
        {
			$ship_order_no=$this->input->post('ship_order_no');
            $log_type = $this->input->post('log_type');
            $storage_code = $this->input->post('storage_code');
			$ship_confirm_date = $this->input->post('ship_confirm_date');
			$locale = $this->input->post('locale');
			$collect_address = $this->input->post('collect_address');
			$ship_confirm_user = $this->input->post('ship_confirm_user');
			$transaction_number = $this->input->post('transaction_number');
			$abroad_stock_remark = $this->input->post('abroad_stock_remark');
			$sku_arr = $this->input->post('sku');
			$qty_arr = $this->input->post('qty');
			 
        }
		
		$sku_str = '';
        $qty_str = '';
        foreach ($sku_arr as $val=>$sku) {
            if (!$this->product_model->check_exists('product_basic', array('sku' => $sku))) {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            } else {
                $sku_str = $sku_str . $sku . ',';
            }
        }

        foreach ($qty_arr as $qty) {
            if (!is_positive($qty)) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            } else {
                $qty_str = $qty_str . $qty . ',';
            }
        }
		$sku_str = substr($sku_str, 0, strlen($sku_str) - 1);
        $qty_str = substr($qty_str, 0, strlen($qty_str) - 1);
		$creator = $this->get_current_login_name();
		$created_date = get_current_time();
		$data=array(
			'ship_order_no'=>$ship_order_no,
			'log_type'=>$log_type,
			'storage_code'=>$storage_code,
			'ship_confirm_date'=>$ship_confirm_date,
			'locale'=>$locale,
			'remark'=>$abroad_stock_remark,
			'collect_address'=>$collect_address,
			'ship_confirm_user'=>$ship_confirm_user,
			'transaction_number'=>$transaction_number,
			'sku_str'=>$sku_str,
			'qty_str'=>$qty_str,
			'creator'=>$creator,
			'created_date'=>$created_date,
			);


		$this->product_model->save_move_stock($data);
		foreach($sku_arr as $val=>$sku){
			$count=$qty_arr[$val];
			$this->change_stock_count($sku,$storage_code,$count);
		}

		echo $this->create_json(1, lang('purchase_apply_saved'));
		
		
	}
	private function change_stock_count($sku,$stock_code,$count)
	{
		$select='id,au_on_way_count,de_on_way_count,uk_on_way_count,yb_on_way_count,sku';
		$product=$this->product_model->fetch_product_by_sku($sku,$select);
		if($stock_code == 'AU')
		{
			$on_way_count=($product->au_on_way_count)+$count;
			$data=array(
				'au_on_way_count'=>$on_way_count,
				);
		}
		if($stock_code == 'DE')
		{
			$on_way_count=($product->de_on_way_count)+$count;
			$data=array(
				'de_on_way_count'=>$on_way_count,
				);
		}
		if($stock_code == 'UK')
		{
			$on_way_count=($product->uk_on_way_count)+$count;
			$data=array(
				'uk_on_way_count'=>$on_way_count,
				);
		}
		if($stock_code == 'YB')
		{
			$on_way_count=($product->yb_on_way_count)+$count;
			$data=array(
				'yb_on_way_count'=>$on_way_count,
				);
		}
		$type_extra='';
		$type=sprintf(lang('move_stock_type'),$stock_code);
		if ($this->sale_model->check_exists('product_makeup_sku', array('makeup_sku' => $sku)))
		{
			$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
			$sku_arr=explode(',', $makeup_sku->sku);
			$qty_arr=explode(',', $makeup_sku->qty);
			foreach($sku_arr as $key=>$value)
			{
				$count_sku=$count*$qty_arr[$key];
				$this->product_model->update_product_stock_count_by_sku($value,$count_sku, TRUE, $type, $type_extra);
				//$this->product_model->update_product_stock_count($product->id,$count,TRUE, $type, $type_extra);
			}
		}else{
			$this->product_model->update_product_stock_count($product->id,$count,TRUE, $type, $type_extra);
		}
		
		
		$this->product_model->update_product_by_sku($sku, $data);
	}
	public function confirm_arrival()
	{
		$this->enable_search('move_stock_list');
		$this->template->add_js('static/js/ajax/move_stock.js');
		$this->render_list('stock/abroad_stock/move_stock_list', 'view', 'fetch_all_move_list');
		//$this->enable_search('move_stock_list');
        //$this->render_list('stock/abroad_stock/move_stock_list', 'edit');
	}
	public function confirm_arrival_received($id)
	{
		$received_count = $this->input->post('received_count');
		$move_stock_list=$this->product_model->fetch_all_move_list_by_id($id);
		$stock_code=$move_stock_list->storage_code;
		//print_r($move_stock_list->ship_order_no);
		$sku_arr = explode(',',$move_stock_list->sku_str);
		$qty_arr = explode(',',$received_count);
		try {
		foreach($sku_arr as $val=>$sku){
			$count=$qty_arr[$val];
			$this->confirm_arrival_change_stock_count($sku,$stock_code,$count);
			if($this->product_model->check_confirm_arrival_notify($id,$sku))
			{
				$message = $this->messages->load('abroad_stock_confirm_arrival_notify');
                $this->events->trigger(
                    'return_confirm_arrival_after',
                    array(
                        'type'          => 'abroad_stock_confirm_arrival_notify',
                        'click_url'     => site_url('stock/move_stock/confirm_arrival'),
                        'content'       => sprintf(lang($message['message'].'_notify'), $sku,$stock_code),
                        'owner_id'      => $this->get_current_user_id(),
                    )
                );
			}
		}
		$data=array(
					'status'=>1,
					'received_count'=>$received_count,
					);
		$this->product_model->update_move_list_by_id($id,$data);
		}catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
		
		//$this->confirm_arrival_change_stock_count($sku,$stock_code,$count);
	}
	private function confirm_arrival_change_stock_count($sku,$stock_code,$count)
	{
		$select='id,au_on_way_count,de_on_way_count,uk_on_way_count,yb_on_way_count,sku,au_stock_count,de_stock_count,uk_stock_count,yb_stock_count';
		$product=$this->product_model->fetch_product_by_sku($sku,$select);
		if($stock_code == 'AU')
		{
			$on_way_count=($product->au_on_way_count)-$count>0?($product->au_on_way_count)-$count:0;
			$stock_count=($product->au_stock_count)+$count;
			$data=array(
				'au_on_way_count'=>$on_way_count,
				'au_stock_count'=>$stock_count,
				);
		}
		if($stock_code == 'DE')
		{
			$on_way_count=($product->de_on_way_count)-$count>0?($product->de_on_way_count)-$count:0;
			$stock_count=($product->de_stock_count)+$count;
			$data=array(
				'de_on_way_count'=>$on_way_count,
				'de_stock_count'=>$stock_count,
				);
		}
		if($stock_code == 'UK')
		{
			$on_way_count=($product->uk_on_way_count)-$count>0?($product->uk_on_way_count)-$count:0;
			$stock_count=($product->uk_stock_count)+$count;
			$data=array(
				'uk_on_way_count'=>$on_way_count,
				'uk_stock_count'=>$stock_count,
				);
		}
		if($stock_code == 'YB')
		{
			$on_way_count=($product->yb_on_way_count)-$count>0?($product->yb_on_way_count)-$count:0;
			$stock_count=($product->yb_stock_count)+$count;
			$data=array(
				'yb_on_way_count'=>$on_way_count,
				'yb_stock_count'=>$stock_count,
				);
		}
		$this->product_model->update_product_by_sku($sku, $data);
	}
	private function render_list($url, $action, $method)
    {
        $move_stock_lists = $this->product_model->$method();
        $data = array(
            'move_stock_lists'    => $move_stock_lists,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
