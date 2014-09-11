<?php
require_once APPPATH.'controllers/stock/stock'.EXT;

class Inout extends Stock
{
    protected $order_statuses = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
		$this->load->model('user_model');
        $this->load->model('product_model');
        $this->load->model('purchase_model');
        $this->load->model('purchase_order_model');
        $this->load->model('order_model');
        $this->load->library('form_validation');
        
        $this->load->helper('order');
        $this->load->helper('purchase_order');

        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }

    public function instock_apply()
    {
        $this->enable_search('product');
        $data = array(
            'products'      => $this->product_model->fetch_all_apply_instock_products(),
            'stock_type'    => 'in',
        );
        $this->template->write_view('content', 'stock/inout/inoutstock', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
    }

    public function instock_verify()
    {
        $this->enable_search('product_apply');
        $this->enable_sort('product_apply');
        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $data = array(
            'products'           => $this->product_model->fetch_all_instock_apply_products(),
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'stock/inout/instock_verify', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
    }

    public function proccess_instock_verify()
    {
        $apply_id = $this->input->post('apply_id');
        $status = $this->input->post('status');
        $user_id = get_current_user_id();

        $this->_instock_verify($apply_id, $status, $user_id);
        if ($status == 1)
        {
            echo $this->create_json(1, lang('instock_approved'));
        }
        else
        {
            echo $this->create_json(1, lang('instock_rejected'));
        }
    }

    public function proccess_batch_instock_verify()
    {
        $apply_count = $this->input->post('apply_count');
        $status = $this->input->post('status');
        $user_id = get_current_user_id();
        for ($i = 0; $i < $apply_count; $i++)
        {
            $apply_id = $this->input->post('apply_id_' . $i);
            $this->_instock_verify($apply_id, $status, $user_id);
        }
    }

    public function outstock()
    {
        $this->enable_search('product');
        $outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'products'        => $this->product_model->fetch_real_all_products(),
            'stock_type'      => 'out',
            'outstock_types'  => $outstock_types,
        );
        $this->template->write_view('content', 'stock/inout/inoutstock', $data);
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->render();
    }

    public function proccess_batch_inoutstock()
    {
        $product_count = $this->input->post('product_count');
        $stock_type = $this->input->post('stock_type');
        $is_outstock = ($stock_type == 'out');
        $user_id = get_current_user_id();
        for ($i = 0; $i < $product_count; $i++)
        {
            $product_id = $this->input->post('product_id_' . $i);
            $count = $this->input->post('stock_count_' . $i);
            $type = $this->input->post('type_' . $i);
            $type_extra = $this->input->post('type_extra_' . $i);
            $shelf_code = $this->input->post('shelf_code_' . $i);
            
            if ( ! $this->product_model->check_exists('product_shelf_code', array('name' => $shelf_code)) || $count <= 0)
            {
                continue;
            }
            try
            {
                if ($is_outstock)
                {
                    $this->product_model->update_product_stock_count($product_id, $count, TRUE, $type, $type_extra);
                }
                else
                {
                    $report_id = $this->product_model->apply_product_instock($product_id, $count);
                    $old_shelf_code = $this->product_model->fetch_shelf_code_by_id($product_id);
                    if (empty($old_shelf_code))
                    {
                        $old_shelf_code = '';
                    }
                    $data = array(
                        'report_id'         => $report_id,
                        'old_shelf_code'    => $old_shelf_code,
                        'new_shelf_code'    => $shelf_code,
                    );
                    $this->stock_model->save_instock_shelf_code($data);
                }
            }
            catch (Exception $e)
            {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        if ($stock_type == 'out')
        {
            echo $this->create_json(1, lang('outstock_successfully'));
        }
        else
        {
            echo $this->create_json(1, lang('instock_apply_successfully'));
        }
    }

    public function outstock_record()
    {
        $this->enable_search('outstock');
        $this->enable_sort('outstock');
        
        $products = $this->stock_model->fetch_outstock_record();
        $data = array(
            'products'  => $products,
        );

        $this->template->write_view('content', 'stock/inout/outstock_record', $data);
        $this->template->render();
    }

    public function instock_record()
    {
        $this->enable_search('instock');
        $this->enable_sort('instock');

        $products = $this->stock_model->fetch_instock_record();
        $data = array(
            'products'  => $products,
        );

        $this->template->write_view('content', 'stock/inout/instock_record', $data);
        $this->template->render();
    }

    public function instock_by_label()
    {
        $this->template->write_view('content', 'stock/inout/instock_by_label');
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function proccess_instock_by_label()
    {
        $order_id = $this->input->post('order_id');
        if (empty($order_id))
        {
            echo $this->create_json(0, lang('error_notice'));
            return '';
        }
        $status_name = $this->order_model->fetch_order_status_name($order_id);
        
        if ($status_name != 'wait_for_shipping_confirmation')
        {
            echo $this->create_json(0, lang('error_notice'));
            return '';
        }

        $user_name = get_current_login_name();
        $type_extra = $user_name . '/' . date('Y-m-d H:i:s');

        $this->product_model->update_product_stock_count_by_order_id($order_id, 'label_instock', $type_extra, FALSE);

        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('instock_by_label_remark'), date('Y-m-d H:i:s'), get_current_user_name());

        $data = array(
            'order_status'          => $this->order_statuses['wait_for_purchase'],
            'sys_remark'            => $remark,
        );

        $this->order_model->update_order_information($order_id, $data);
    }

    public function select_orders_by_item_no()
    {
        $item_no = $this->input->post('item_no');

        if (empty($item_no))
        {
            echo $this->create_json(0, lang('error_notice'));
            return '';
        }

        $orders = $this->order_model->fetch_order_by_field($item_no);

        $data = array(
            'orders'            => $orders,
        );

        $this->load->view('stock/inout/list', $data);
    }

    private function _instock_verify($apply_id, $status, $user_id)
    {
        if ($status == 1)
        {
            $instock_apply = $this->product_model->fetch_instock_apply_count_by_id($apply_id);
            $new_shelf_code = $this->stock_model->fetch_instock_new_shelf_code($apply_id);
            if (! $instock_apply || $instock_apply->change_count <= 0)
            {
                return;
            }

            // update product stock
            $this->product_model->product_instock_verified(
                $instock_apply->product_id,
                $instock_apply->change_count,
                $new_shelf_code
            );
            // update apply status
            $data = array(
                'status'        => $status,
                'verifyer'      => $user_id,
                'verify_date'   => get_current_time(),
            );
            $this->product_model->update_instock_apply($apply_id, $data);

            /*
             * 更新在途数量。
             * **/
            $sku = $this->stock_model->get_one('product_basic', 'sku', array('id'=>$instock_apply->product_id));

            $count = on_way_count($sku);

            $data_product = array(
                'on_way_count' => $count,
            );

            $this->product_model->update_product_by_sku($sku, $data_product);
           /*
            * End by Cheng
            * **/

        }
        else if ($status == -1)
        {
            // update apply status
            $data = array(
                'status'        => $status,
                'verifyer'      => $user_id,
                'verify_date'   => get_current_time(),
            );
            $this->product_model->update_instock_apply($apply_id, $data);
        }
    }

    public function inout_stock_record()
    {
        $this->enable_search('inout_stock');
        $this->enable_sort('inout_stock');

        $products = $this->stock_model->fetch_inout_stock_record();
        $data = array(
            'products'  => $products,
        );

        $this->template->write_view('content', 'stock/inout/inout_stock_record', $data);
        $this->template->render();
    }

    public function update_instock_info()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        switch ($type)
        {
            case 'old_shelf_code' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'shelf code',
                        'rules' => 'trim|required',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }

                if ( ! $this->stock_model->check_exists('product_shelf_code', array('name' => $value)))
                {
                    echo $this->create_json(0, lang('shelf_code_doesnot_exists'));
                    return;
                }

                try
                {
                    $sku = $this->input->post('sku');
                    $this->product_model->update_product($sku, array('shelf_code' => $value));
                    echo $this->create_json(1, lang('ok'), $value);
                }
                catch(Exception $e)
                {
                    $this->ajax_failed();
                }
                break;
            case 'shelf_code' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'shelf code',
                        'rules' => 'trim|required',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }

                if ( ! $this->stock_model->check_exists('product_shelf_code', array('name' => $value)))
                {
                    echo $this->create_json(0, lang('shelf_code_doesnot_exists'));
                    return;
                }

                try
                {
                    $this->stock_model->update_instock_shelf_code($id, $value);
                    echo $this->create_json(1, lang('ok'), $value);
                }
                catch(Exception $e)
                {
                    $this->ajax_failed();
                }
                break;
            case 'change_count' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'change count',
                        'rules' => 'trim|required|is_natural_no_zero',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }

                try
                {
                    $this->stock_model->update_intstock_change_count($id, $value);
                    echo $this->create_json(1, lang('ok'), $value);
                }
                catch(Exception $e)
                {
                    $this->ajax_failed();
                }
                break;
        }
    }

    public function outstock_type_manage()
    {

        $outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'outstock_types'   => $outstock_types,
        );
        $this->template->write_view('content','stock/inout/outstock_type_manage', $data);
        $this->template->render();
    }

    public function add_oustock_type()
    {
        $user_name = get_current_user_name();
        $data = array(
           'type'                => '[edit]',
           'creator'             => $user_name,
       
        );
        try
        {
            $this->stock_model->save_outstock_type($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function verify_outstock_type()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
            if ($this->stock_model->check_exists('product_outstock_type', array('type' => $value)))
            {
               echo $this->create_json(0, lang('product_outstock_type_exists'));
               return;
            }
            $user_name = get_current_user_name();
            $this->stock_model->update_outstock_type($id, $type, $value, $user_name);

            if($type == 'type')
            {
                 echo $this->create_json(1, lang('ok'), $value);
            }

            if($type == 'is_saled')
            {
                 $value = empty ($value)?0:1;
                 echo $this->create_json(1, lang('ok'), empty($value) ? lang('no') : lang('yes'));
            }
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_outstock_type()
    {
        $id = $this->input->post('id');
        $this->stock_model->delete_outstock_type($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }
	public function quick_in_stock()
	{
		$outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'outstock_types'   => $outstock_types,
        );
        $this->template->write_view('content','stock/inout/quick_in_stock', $data);
        $this->template->render();
	}
	public function save_quick_in_stock()
    {
        $qty = $this->input->post('qty');
		$sku = $this->input->post('sku');
		$note = $this->input->post('note');
		$user_id = get_current_user_id();
		if( ! positive_numeric($qty))
        {
            echo $this->create_json(0, lang('qty_not_natural'));
            return;
        }
		if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
                echo $this->create_json(0, lang('product_sku_doesnot_exists') . "($sku)");
                return;
        }
		
		$this->product_model->update_product_stock_count_by_sku($sku, $qty, false, lang('quick_in_stock'), $note,'SZ',$user_id);
			
        echo $this->create_json(1, lang('configuration_accepted'));
    }
	public function quick_out_stock()
	{
		$outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'outstock_types'   => $outstock_types,
        );
        $this->template->write_view('content','stock/inout/quick_out_stock', $data);
        $this->template->render();
	}
	public function save_quick_out_stock()
    {
        $qty = $this->input->post('qty');
		$sku = $this->input->post('sku');
		$note = $this->input->post('note');
		$user_id = get_current_user_id();
		if( ! positive_numeric($qty))
        {
            echo $this->create_json(0, lang('qty_not_natural'));
            return;
        }
		if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
                echo $this->create_json(0, lang('product_sku_doesnot_exists') . "($sku)");
                return;
        }
		
		$this->product_model->update_product_stock_count_by_sku($sku, $qty, true, lang('quick_out_stock'), $note,'SZ',$user_id);
			
        echo $this->create_json(1, lang('configuration_accepted'));
    }
	public function print_sku_bar()
	{
		$outstock_types = $this->stock_model->fetch_outstock_type();
        $data = array(
            'outstock_types'   => $outstock_types,
        );
        $this->template->write_view('content','stock/inout/print_sku_bar', $data);
        $this->template->render();
	}
	public function save_print_sku_bar()
	{
		$qty_str = $this->input->post('qty');
		$sku_str = $this->input->post('sku');
		$sku_arr=explode(',', $sku_str);
		$qty_arr=explode(',', $qty_str);
		
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
		if(count($sku_arr)!=count($qty_arr)){return ;}
		foreach($sku_arr as $key=>$sku)
		{
			$qty = $qty_arr[$key];
			if($qty>0)
			{
				$width = 50;
				$height = 30;
				$pagelayout = array($width,$height);
				$my_tcpdf['page_format'] = $pagelayout;
				$my_tcpdf['page_orientation'] = 'L';
				$tcpdf['encoding'] = 'UTF-8';
				$this->load->library('pdf',$my_tcpdf);
        		$this->pdf->SetCreator('Mallerp');
        		$this->pdf->SetAuthor('Mansea');
        		$this->pdf->SetTitle('Ebay ShipOrder List');
        		$this->pdf->SetSubject('Mallerp');
        		$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        		$this->pdf->SetFont('arialunicid0', '', 23);
				$this->pdf->setPrintHeader(false);
				$this->pdf->setPrintFooter(false);
				$this->pdf->SetMargins(0, 0, 0);
				$CI = & get_instance();
				$print_date = date('Y.m.d');
			
				$sql1 = 'name_cn,shelf_code,purchaser_id,price';
				$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
				$user_names = $this->user_model->fetch_user_login_name_by_id($myproduct->purchaser_id);
				
				
				$htmlprint_date = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:7;">{$print_date}</span>
EOD;
				$purchaser_id = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:7;">{$user_names}</span>
EOD;
				$htmlprint_name_cn = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">{$myproduct->name_cn}</span>
EOD;
				$htmlprint_shelf_code = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">{$myproduct->shelf_code}</span>
EOD;
				for($i=1;$i<=$qty;$i++)
				{
					$this->pdf->AddPage();
					$this->pdf->write1DBarcode(strtoupper($sku), 'C128A', 2, 1, 45, 10, 0.8, $style, 'C'); //write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
					$this->pdf->writeHTMLCell($w = 48, $h = 13, $x = 1, $y =13.5, $htmlprint_name_cn, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 15, $h = 3, $x = 35, $y =25, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 5, $h = 3, $x = 2, $y =25, $purchaser_id, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 25, $h = 3, $x = 8, $y =25, $htmlprint_shelf_code, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				}
				
			
			}
		}
		$filename = "sku_bar_" . date("Y-m-d_His") . ".pdf";
		$this->pdf->Output($filename, 'D');
	}
        
}

?>
