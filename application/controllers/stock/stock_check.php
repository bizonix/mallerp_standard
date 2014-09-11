<?php

require_once APPPATH . 'controllers/stock/stock' . EXT;

class Stock_check extends Stock {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('product_model');
		$this->load->model('product_shelf_code_model');
		$this->load->library('excel');
        $this->load->helper('product_permission');
    }

    public function waiting_check_or_count()
    {
        $this->enable_search('product');
        $this->enable_sort('product');

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        $all_stock_user_ids = array('' => lang('please_select'));
        $all_stock_user_ids['-1'] = '#';
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }
        $all_codes = $this->stock_model->fetch_stock_code();
        $data = array(
            'products' => $this->product_model->waiting_check_or_count(),
            'all_stock_user_ids' => $all_stock_user_ids,
			'all_codes'=>$all_codes,
        );
        $this->template->write_view('content', 'stock/stock_check/stock_check', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
    }

    public function proccess_batch_check_or_count()
    {
        $product_count = $this->input->post('product_count');
        $user_id = get_current_user_id();
        for ($i = 0; $i < $product_count; $i++) {
            $product_id = $this->input->post('product_id_' . $i);
            $count = trim($this->input->post('stock_count_' . $i));
            $shelf_code = trim($this->input->post('shelf_code_' . $i));
			$stock_code = trim($this->input->post('stock_code_' . $i));
            $type = $this->input->post('type_' . $i);
            $duty = $this->input->post('duty_' . $i);
            $type_extra = trim($this->input->post('type_extra_' . $i));
			if($stock_code == 'SZ'){
				$before_change_count = $this->product_model->fetch_product($product_id)->stock_count;
			}
			if($stock_code == 'DE'){
				$before_change_count = $this->product_model->fetch_product($product_id)->de_stock_count;
			}
			if($stock_code == 'UK'){
				$before_change_count = $this->product_model->fetch_product($product_id)->uk_stock_count;
			}
			if($stock_code == 'AU'){
				$before_change_count = $this->product_model->fetch_product($product_id)->au_stock_count;
			}
			if($stock_code == 'YB'){
				$before_change_count = $this->product_model->fetch_product($product_id)->yb_stock_count;
			}
            if ($count < 0) {
                continue;
            }
            try {
				if($stock_code == 'SZ'){
					$data = array(
						'shelf_code'        => $shelf_code,
						'stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				}
				if($stock_code == 'DE'){
					$data = array(
						'shelf_code'        => $shelf_code,
						'de_stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				}
				if($stock_code == 'UK'){
					$data = array(
						'shelf_code'        => $shelf_code,
						'uk_stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				}
				if($stock_code == 'AU'){
					$data = array(
						'shelf_code'        => $shelf_code,
						'au_stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				}
				if($stock_code == 'YB'){
					$data = array(
						'shelf_code'        => $shelf_code,
						'yb_stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				}
                $this->product_model->verify_product_stock($product_id, $data);
                $stock_data = array(
                    'product_id'             => $product_id,
                    'user_id'                => $user_id,
                    'before_change_count'    => $before_change_count,
                    'change_count'           => abs($count - $before_change_count),
                    'after_change_count'     => $count,
                    'type'                   => $type,
                    'type_extra'             => $type_extra,
                    'stock_type'             => 'product_check_count',
                );
                $this->stock_model->save_stock_check_or_count($stock_data);

                $sku = $this->stock_model->get_one('product_basic', 'sku', array('id' => $product_id));
                if(abs($count - $before_change_count) != 0)
                {
                    $duty_data = array(
                        'sku'                    => $sku,
                        'stock_checker'          => $user_id,
                        'differences_remark'     => $type,
                        'remark'                 => $type_extra,
                        'before_change_count'    => $before_change_count,
                        'change_count'           => abs($count - $before_change_count),
                        'after_change_count'     => $count,
                        'duty'                   => $duty,
                        'update_time'            => get_current_time(),
                    );
                    $this->stock_model->save_stock_check_duty($duty_data);
                }
            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }

        echo $this->create_json(1, lang('stock_check_or_count_successfully'));
    }

    public function check_or_count_recorder()
    {      
        $this->enable_search('check_or_count');
        $this->enable_sort('check_or_count');

        $products = $this->stock_model->check_or_count_record();    
        $data = array(
            'products'  => $products,
        );

        $this->template->write_view('content', 'stock/stock_check/check_or_count_recorder', $data);
        $this->template->render();
    }

    public function stock_differences_review() {
        $this->enable_search('differences_review');
        $this->enable_sort('differences_review');

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');
        $role = $this->user_model->fetch_user_priority_by_system_code('stock');
        if($role > 1 OR  $this->is_super_user()) {
            $user_id = NULL;
        } else {
            $user_id = get_current_user_id();
        }
        $records = $this->stock_model->fetch_all_stock_differences($user_id);

        
        $all_stock_user_ids = array('' => lang('please_select'));
        $all_stock_user_ids['-1'] = '#';
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }
        $data = array(
            'records'  => $records,
            'role'     => $role,
            'all_stock_user_ids' => $all_stock_user_ids,
        );

        $this->template->write_view('content', 'stock/stock_check/stock_differences_review', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
    }

    public function confirm_review() {
        $id = $this->input->post('id');
        $duty = $this->input->post('duty');
        $type_extra = trim($this->input->post('type_extra'));
        try {
            $this->stock_model->update('stock_check_duty', array('id' => $id), array('duty' => $duty, 'remark' => $type_extra, 'review_status' => '1'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
	public function update_shelf_code()
	{
		$outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'outstock_types'   => $outstock_types,
        );
        $this->template->write_view('content','stock/stock_check/update_shelf_code', $data);
        $this->template->render();
	}
	public function save_update_shelf_code()
	{
		$shelf_code = $this->input->post('shelf_code');
		$sku = $this->input->post('sku');
		$user_id = get_current_user_id();
		if ( ! $this->product_model->check_exists('product_shelf_code', array('name' => $shelf_code)))
	    {
			$this->product_shelf_code_model->save_currency_shelf_code(array('name'=>$shelf_code,'creator'=>$user_id));
	    }
		
		if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
                echo $this->create_json(0, lang('product_sku_doesnot_exists') . "($sku)");
                return;
        }
		
		$this->product_model->update_product_by_sku($sku, array('shelf_code'=>$shelf_code));
			
        echo $this->create_json(1, lang('configuration_accepted'));
	}
	public function import_stock_count()
	{
		$data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'stock/stock_check/import_stock_count', $data);
        $this->template->render();
	}
	function do_import_stock_count_upload()
    {
		setlocale(LC_ALL, 'en_US.UTF-8');
		
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100000';
        $config['max_width']  = '1024';
        $config['max_height']  = '7680';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('stock/stock_check/import_stock_count', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);
            $output_data = array();
			$i=0;
			$user_id = get_current_user_id();
            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1 or $row[0]==''){continue;}
				$sku = $row[0];
				$product =$this->product_model->fetch_product_by_sku($sku);
				if($product)
				{
					$product_id=$product->id;
				}else{
					$output_data[$row[0]]=$row[0]. ' not exist!';
					continue;
				}
				
            	$count = $row[1];
            	//$shelf_code = trim($this->input->post('shelf_code_' . $i));
				//$stock_code = trim($this->input->post('stock_code_' . $i));
            	$type = 'correct';
            	$duty = '-1';
            	$type_extra = '';
				$before_change_count = $this->product_model->fetch_product($product_id)->stock_count;
            	if ($count < 0) {
                	continue;
            	}
				$data = array(
						//'shelf_code'        => $shelf_code,
						'stock_count'       => $count,
						'stock_check_date'  => get_current_time(),
					);
				$this->product_model->verify_product_stock($product_id, $data);
            
				
                
                $stock_data = array(
                    'product_id'             => $product_id,
                    'user_id'                => $user_id,
                    'before_change_count'    => $before_change_count,
                    'change_count'           => abs($count - $before_change_count),
                    'after_change_count'     => $count,
                    'type'                   => $type,
                    'type_extra'             => $type_extra,
                    'stock_type'             => 'product_check_count',
                );
                $this->stock_model->save_stock_check_or_count($stock_data);

                //$sku = $this->stock_model->get_one('product_basic', 'sku', array('id' => $product_id));
                if(abs($count - $before_change_count) != 0)
                {
                    $duty_data = array(
                        'sku'                    => $sku,
                        'stock_checker'          => $user_id,
                        'differences_remark'     => $type,
                        'remark'                 => $type_extra,
                        'before_change_count'    => $before_change_count,
                        'change_count'           => abs($count - $before_change_count),
                        'after_change_count'     => $count,
                        'duty'                   => $duty,
                        'update_time'            => get_current_time(),
                    );
                    $this->stock_model->save_stock_check_duty($duty_data);
                }
				$output_data[$row[0]]=$row[1];
			}
				
            $data_page = array(
                'data' => $output_data,
            );
            $this->template->write_view('content', 'pi/import_product_success', $data_page);
            $this->template->render();
        }
    }

}

?>
