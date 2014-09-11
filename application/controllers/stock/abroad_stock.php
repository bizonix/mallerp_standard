<?php
require_once APPPATH.'controllers/stock/stock'.EXT;

class Abroad_stock extends Stock
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('abroad_stock_model');
        $this->load->model('product_model');
        $this->load->library('form_validation');
        $this->load->library('chukouyi/CKY_Product');
        $this->load->library('chukouyi/CKY_Order');
        $this->load->helper('validation_helper');
        $this->load->helper('solr_helper');
    }

    public function in_store_apply_page($list_id = NULL, $status = NULL)
    {
        /**
         * 当点击完成的时候进入。
         */
        if ($list_id && $status)
        {            
            $result = $this->sumbit_abroad_instock($list_id);          
            if ($result !== TRUE)
            {
                echo $this->create_json(0, $result);
                return;
            }
            $apply_list = $this->abroad_stock_model->fecth_in_store_apply_by_date();
            
            $all_list_detail_arr = array();
            foreach ($apply_list as $apply) 
            {
                list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($apply->id);
                $all_list_detail_arr["$apply->id"]['list_info'] = $list_info;
                $all_list_detail_arr["$apply->id"]['boxes_info'] = $boxes_info;
                $all_list_detail_arr["$apply->id"]['products_info'] = $products_info;
            }
        
            $data = array(
                'apply_list' => $apply_list,
                'all_list_detail' => $all_list_detail_arr,
            );

            $this->load->view('stock/abroad_stock/in_store_apply_page', $data);
            return;
        }

        $apply_list = $this->abroad_stock_model->fecth_in_store_apply_by_date();
        
        $all_list_detail_arr = array();
        foreach ($apply_list as $apply) 
        {
            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($apply->id);
            $all_list_detail_arr["$apply->id"]['list_info'] = $list_info;
            $all_list_detail_arr["$apply->id"]['boxes_info'] = $boxes_info;
            $all_list_detail_arr["$apply->id"]['products_info'] = $products_info;
        }
        
        /**
         * 当点击上一步的时候进入。
         */
        if($list_id && $status == 0)
        {
            $apply_obj = $this->abroad_stock_model->fetch_apply_list_info_by_id($list_id);
            $data = array(
                'apply_obj' => $apply_obj,
                'apply_list' => $apply_list,
                'all_list_detail' => $all_list_detail_arr,
            );
            
            $this->template->add_js('static/js/ajax/stock.js');
            $this->template->add_js('static/js/ajax/calendar-setup.js');
            $this->template->add_js('static/js/ajax/calendar.js');
            $this->load->view('stock/abroad_stock/in_store_apply_page', $data);
            return;
        }
        
        $data = array(
            'apply_list' => $apply_list,
            'all_list_detail' => $all_list_detail_arr,
        );
        
        $this->template->add_js('static/js/ajax/stock.js');
        $this->template->write_view('content', 'stock/abroad_stock/in_store_apply_page', $data);
        $this->template->render();
        return;
    }

    public function in_store_case($id = null, $case_no = null)
    {
        /**
         * 当点击继续加箱的时候进入。
         */
        if ($id && $case_no)
        {            
            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($id);
            
            $data_for_view = array(
                'list_id'           =>  $id,
                'case_no'           =>  $case_no,
                'list_info'         =>  $list_info,
                'boxes_info'        =>  $boxes_info,
                'products_info'     =>  $products_info,
            );

            $this->load->view('stock/abroad_stock/in_store_case_page', $data_for_view);

            return;
        }
        
         /**
         * 当点击上一步的时候进入。
         */
        if($id && $case_no == 0)
        {
            $case_obj = $this->abroad_stock_model->fetch_case_info_by_id($id);

            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($case_obj->list_id);
            
            $data = array(
                'list_id'           =>  $case_obj->list_id,
                'list_info'         =>  $list_info,
                'boxes_info'        =>  $boxes_info,
                'products_info'     =>  $products_info,
                'case_obj'          =>  $case_obj,
                'page_status'          =>  'close_case_back_button',
            );

            $this->load->view('stock/abroad_stock/in_store_case_page', $data);
            return;
        }
        
        $rules = array(
            array(
                'field' => 'log_type',
                'label' => lang('log_type'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'storage_code',
                'label' => lang('storage_code'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'arrive_time',
                'label' => lang('arrive_time'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'locale',
                'label' => lang('locale'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'is_collect',
                'label' => lang('is_collect'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'collect_time',
                'label' => lang('collect_time'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'collect_address',
                'label' => lang('collect_address'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'collect_contact',
                'label' => lang('collect_contact'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'collect_phone',
                'label' => lang('collect_phone'),
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        
  
        $log_type = trim($this->input->post('log_type'));
        $storage_code = trim($this->input->post('storage_code'));
        $arrive_time = substr(trim($this->input->post('arrive_time')), 0, 10);
        $locale = trim($this->input->post('locale'));
        $remark  = trim($this->input->post('abroad_stock_remark'));
        $is_collect = trim($this->input->post('is_collect'));
        $collect_time = to_utc_format(trim($this->input->post('collect_time')));
        $collect_address = trim($this->input->post('collect_address'));
        $collect_contact = trim($this->input->post('collect_contact'));
        $collect_phone = trim($this->input->post('collect_phone'));

        
        $data_to_database = array(
//            'sign'                          => $result['order_sign'],
            'log_type'                      => $log_type,
            'storage_code'                  => $storage_code,
            'arrive_time'                   => $arrive_time,
            'locale'                        => $locale,
            'remark'                        => $remark,
            'is_collect'                    => $is_collect,
            'collect_time'                  => $collect_time,
            'collect_address'               => $collect_address,
            'collect_contact'               => $collect_contact,
            'collect_phone'                 => $collect_phone,
            'status'                        => 0,
            'creator'                       => get_current_login_name(),
        );

        try
        {
            $list_id = $this->input->post('list_id');
            if($list_id > 0)
            {
                $this->abroad_stock_model->update('cky_in_store_list', array('id' => $list_id), $data_to_database);
            }
            else
            {
                $list_id = $this->abroad_stock_model->save_in_store_apply($data_to_database);
            }

            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($list_id);
            
            $data_for_view = array(
                'list_id'           =>  $list_id,
                'list_info'         =>  $list_info,
                'boxes_info'        =>  $boxes_info,
                'products_info'     =>  $products_info,
            );

            $this->load->view('stock/abroad_stock/in_store_case_page', $data_for_view);
            
            return;            
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function in_store_product($case_id = NULL, $tag = NULL, $key =NULL)
    {
        if($case_id && $tag == 'product_back')
        {
            $products = $this->abroad_stock_model->fetch_product_info_by_case_id($case_id);
            
            $case_obj = $this->abroad_stock_model->fetch_case_info_by_id($case_id);
                       
            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($case_obj->list_id);
            
            $data_for_view = array(
                'products'          => $products,
                'list_id'           => $case_obj->list_id,
                'case_id'           => $case_id,
                'case_no'           => $case_obj->case_no,
                'list_info'         => $list_info,
                'boxes_info'        => $boxes_info,
                'products_info'     => $products_info,
            );

            $this->load->view('stock/abroad_stock/in_store_product_page', $data_for_view);

            return;
        }
                        
        $list_id = trim($this->input->post('list_id'));
        $case_id = trim($this->input->post('case_id'));

        $case_no = trim($this->input->post('case_no'));
        
        $tag = trim($this->input->post('tag'));

        $weight = trim($this->input->post('in_store_weight'));
        $packing = trim($this->input->post('packing'));
        
        $rules = array(
            array(
                'field' => 'case_no',
                'label' => lang('case_no'),
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'in_store_weight',
                'label' => lang('in_store_weight'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'packing',
                'label' => lang('packing'),
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if( ! positive_numeric($weight))
        {
            echo $this->create_json(0, lang('weight_not_natural'));
            return;
        }

        $packing_arr = explode('*', $packing);

        if(count($packing_arr) != 3)
        {
            echo $this->create_json(0, lang('packing_format_is_error'));
            return;
        }


        $data_to_database = array(
            'case_no'           => $case_no,
            'list_id'           => $list_id,
            'weight'            => $weight,
            'packing'           => $packing,
            'creator'           => get_current_login_name(),
        );

        try
        {
            if($case_id > 0)
            {
                $case_obj = $this->abroad_stock_model->fetch_case_info_by_id($case_id);

                if ($this->abroad_stock_model->check_exists('cky_in_store_case', array('list_id' => $list_id, 'case_no'=>$case_no)) && $case_obj->case_no != $case_no)
                {
                    echo $this->create_json(0, lang('case_no_nonentity'));
                    return;
                }
                
                $this->abroad_stock_model->update('cky_in_store_case', array('id' => $case_id), $data_to_database);
            }
            else
            {
                if ($this->abroad_stock_model->check_exists('cky_in_store_case', array('list_id' => $list_id, 'case_no'=>$case_no)))
                {
                    echo $this->create_json(0, lang('case_no_nonentity'));
                    return;
                }
                
                $case_id = $this->abroad_stock_model->save_in_store_case($data_to_database);
            }

            list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($list_id);
            
            $products = $this->abroad_stock_model->fetch_product_info_by_case_id($case_id);
            
            $data_for_view = array(
                'list_id'  => $list_id,
                'case_id'  => $case_id,
                'case_no'  => $case_no,
                'list_info'         =>  $list_info,
                'boxes_info'        =>  $boxes_info,
                'products_info'     =>  $products_info,
                'products'     =>  $products,
            );

            $this->load->view('stock/abroad_stock/in_store_product_page', $data_for_view);

            return;

        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function seccuss_or_failure()
    {
        $rules = array(
            array(
                'field' => 'case_id',
                'label' => lang('case_id'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'case_no',
                'label' => lang('case_no'),
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'declared_name[]',
                'label' => lang('declared_name'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'declared_price[]',
                'label' => lang('declared_price'),
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $sku_arr = $this->input->post('sku');
        $qty_arr = $this->input->post('qty');
        $declared_name_arr = $this->input->post('declared_name');
        $declared_price_arr = $this->input->post('declared_price');
        
        if(count($sku_arr) == count($qty_arr) && count($qty_arr) == count($declared_name_arr) && count($declared_name_arr) == count($declared_price_arr))
        {
            foreach ($sku_arr as $sku)
            {
                if ( ! $this->abroad_stock_model->check_exists('product_basic', array('sku' => $sku )))
                {
                    echo $this->create_json(0, lang('product_sku_nonentity'));
                    return;
                }
            }

            foreach ($qty_arr as $qty)
            {
                if ( ! is_positive($qty))
                {
                    echo $this->create_json(0, lang('qty_not_natural'));
                    return;
                }
            }
            
            $case_no = trim($this->input->post('case_no'));
            $case_id = trim($this->input->post('case_id'));
            
//            $order_sign = $this->abroad_stock_model->get_order_sign_by_case_id($case_id);
            $sku_count = count($sku_arr);
            
            $this->abroad_stock_model->delete('cky_in_store_product', array('case_id' => $case_id ));
            
            for($i=0; $i < $sku_count; $i++)
            {
                $data_to_database = array(
                    'case_id'               => $case_id,
                    'case_no'               => $case_no,
                    'title'                 => $sku_arr[$i],
                    'quantity'              => $qty_arr[$i],
                    'declared_name'         => $declared_name_arr[$i],
                    'declared_price'        => $declared_price_arr[$i],
                    'creator'               => get_current_login_name(),
                );

                try
                {
                    
                    $this->abroad_stock_model->save_in_store_product($data_to_database);
                }
                catch (Exception $e)
                {
                    echo lang('error_msg');
                    $this->ajax_failed();
                }
            }            
        }
        else
        {
            echo $this->create_json(0, lang('product_info_not_matching'));
            return ;
        }
                
        $list_id = trim($this->input->post('list_id'));

        list($list_info, $boxes_info, $products_info) = $this->in_store_list_all_detail($list_id);

        $data_for_view = array(
            'list_id'           => $list_id,
            'case_id'           => $case_id,
            'case_no'           => $case_no,
            'list_info'         =>  $list_info,
            'boxes_info'        =>  $boxes_info,
            'products_info'     =>  $products_info,
        );

        $this->load->view('stock/abroad_stock/seccuss_or_failure_page', $data_for_view);

        return;
    }

    public function in_store_list_all_detail($list_id)
    {
        $list_obj = $this->abroad_stock_model->fetch_apply_list_info_by_id($list_id);
        $boxes = $this->abroad_stock_model->fetch_case_info_by_list_id($list_id);

        if($boxes)
        {
            $product_arr = array();
            foreach ($boxes as $box)
            {
                $product_box = $this->abroad_stock_model->fetch_product_info_by_case_id($box->id);
                $product_arr["$box->id"] = $product_box;
            }
        }
        else
        {
            return array($list_obj, null, null);
        }
        return array($list_obj, $boxes, $product_arr);
    }
    
    public function update_apply_list_info()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        
        $currency_code = $this->fee_price_model->fetch_category_by_id($id);
        try
        {
            switch ($type)
            {
                case 'eshop_code' :
                    if ( $currency_code->eshop_code != $value)
                    {
                        if($this->fee_price_model->check_exists('eshop_category', array('eshop_code' =>$value, 'category'=>$currency_code->category)))
                        {
                            echo $this->create_json(0, lang('eshop_code_and_category_exists'), $value);
                            return;
                        }
                       
                    }
                    break;
                case 'category':
                    if ($currency_code->category != $value)
                    {
                        if($this->fee_price_model->check_exists('eshop_category', array('category' =>$value, 'eshop_code'=>$currency_code->eshop_code)))
                        {
                            echo $this->create_json(0, lang('eshop_code_and_category_exists'), $value);
                            return;
                        }

                    }
                     break;
            }


            $this->fee_price_model->update_exchange_category($id, $type, $value);
            if($type == 'eshop_code')
            {
                $value = $this->fee_price_model->get_one('eshop_code', 'name',array('code' => $value));
            }

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    private function sumbit_abroad_instock($list_id)
    {
        /*
         * Starting making request to ChuKouYi.
         */
        $apply_list = $this->abroad_stock_model->fetch_apply_list_info_by_id($list_id);
        if ($apply_list->status)
        {
            return;
        }
        $data = array();
        $data['LogType'] = $apply_list->log_type;
        $data['StorageCode'] = $apply_list->storage_code;
        $data['ArriveTime'] = $apply_list->arrive_time;
        $data['Locale'] = $apply_list->locale;
        $data['Remark'] = $apply_list->remark;
        $data['IsCollect'] = $apply_list->is_collect;
        $data['CollectTime'] = $apply_list->collect_time;
        $data['CollectAddress'] = $apply_list->collect_address;
        $data['CollectContact'] = $apply_list->collect_contact;
        $data['CollectPhone'] = $apply_list->collect_phone;
        
        $result = $this->cky_product->instore_add($data);
        if ($result)
        {
            if ($result['status'] == FALSE)
            {
                return $result['message'];
            }
        }
        else
        {
            return lang('error_msg');
        }

        /*
         * Update store list order sign
         */
        $order_sign = $result['order_sign'];
        $data = array('sign' => $order_sign);
        $this->abroad_stock_model->update('cky_in_store_list', array('id' => $list_id), $data);

        $case_list = $this->abroad_stock_model->fetch_case_info_by_list_id($list_id);
        foreach ($case_list as $case)
        {
            /*
             * Starting Adding case for ChuKouYi.
             */
            $case_id = $case->id;
            $case_no = $case->case_no;
            $order_sign = $this->abroad_stock_model->get_order_sign($list_id);
            $data = array();
            $data['CaseNo'] = $case->case_no;
            $data['Weight'] = $case->weight;
            $data['Packing'] = $case->packing;
            $data['OrderNo'] = $order_sign;
            
            $result = $this->cky_product->instore_case_add($data);
            if ($result)
            {
                if ($result['status'] == FALSE)
                {
                    return $result['message'];
                }
            }
            else
            {
                return $lang('error_msg');;
            }

            /*
             * Starting adding products to case
             */
            $product_list = $this->abroad_stock_model->fetch_product_info_by_case_id($case_id);
            $products = array();
            foreach ($product_list as $product)
            {
                $data = array();
                $data['CaseNo'] = $case_no;
                $data['Title'] = $product->title;
                $data['Quantity'] = $product->quantity;
                $data['DeclaredValue'] = $product->declared_price;
                $data['DeclaredName'] = $product->declared_name;
                $products[] = $data;
            }
            $result = $this->cky_product->instore_product_add($products, $order_sign);
            if ($result)
            {
                if ($result['status'] == FALSE)
                {
                    return $result['message'];
                }
            }
            else
            {
                return lang('error_msg');
            }
        }

        /*
         * Starting submitting request to ChuKouYi.
         */
        $data = array();
        $data['OrderNo'] = $order_sign;
        $result = $this->cky_product->instore_submit($data);
        
        if ($result)
        {
            return $result['message'];
        }
        else
        {

            return lang('error_msg');
        }
        $status = 1;       
        $this->abroad_stock_model->update_in_store_apply($list_id, $status);
    }
}

?>
