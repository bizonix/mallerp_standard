<?php
require_once APPPATH.'controllers/qt/qt'.EXT;

class Recommend extends Qt
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('quality_testing_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
        $this->load->model('shipping_code_model');

        $this->load->library('form_validation');
    }

    public function search()
    {
        $data = array(
            'tag' => 'qt',
        );
        $this->template->write_view('content', 'qt/recommend/search', $data);
        $this->template->render();
    }

    public function add()
    {
        $currency = $this->order_model->fetch_currency();

        $income_types = $this->order_model->fetch_all_income_type();

        $option = array();
        foreach ($income_types as $income_type)
        {
            $option[$income_type->receipt_name] = $income_type->receipt_name;
        }

        $currency_arr = array();
        foreach ($currency as $v) {
            $currency_arr[$v->code] = $v->name_en ;
        }

        $data =array(
            'order'         => NULL,
            'currency_arr'  => $currency_arr,
            'action'        => 'edit',
            'income_type'   => $option,
        );

        $this->template->write_view('content', 'qt/recommend/add', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function save()
    {
        $rules = array(
            array(
                'field' => 'email_time',
                'label' => lang('email_time'),
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

        if( ! $this->input->post('recommend_no'))
        {
            echo $this->create_json(0, lang('recommend_no_is_null'));
            return;
        }

        $recommend_no = trim($this->input->post('recommend_no'));
        if ($this->quality_testing_model->check_exists('order_recommend_list', array('recommend_no' => $recommend_no)))
        {
            echo $this->create_json(0, lang('recommend_no_exists'));
            return;
        }

//        $skus = $this->input->post('sku');
//        $qtys = $this->input->post('qty');
//        $sku_str = '';
//        $qty_str = '';
//        if($skus && $qtys)
//        {
//            $sku_str = implode(',', $skus);
//            $qty_str = implode(',', $qtys);
//        }
//        else
//        {
//            echo $this->create_json(0, lang('order_is_not_product'));
//            return;
//        }

        $sku_arr = $this->input->post('sku');
        $qty_arr = $this->input->post('qty');

        $sku_str = '';
        $qty_str = '';
        foreach ($sku_arr as $sku)
        {
            if ( ! $this->product_model->check_exists('product_basic', array('sku' => $sku )))
            {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            }
            else
            {
                $sku_str = $sku_str. $sku . ',';
            }
        }

        foreach ($qty_arr as $qty)
        {
            if ( ! is_positive($qty))
            {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            }
            else
            {
                $qty_str = $qty_str . $qty . ',';
            }
        }

        $sku_str = substr($sku_str, 0,  strlen($sku_str)-1);
        $qty_str = substr($qty_str, 0,  strlen($qty_str)-1);


        $data = array(
            'recommend_id'                      => -1,
            'order_id'                          => -1,
            'qty_str'                           => $qty_str,
            'sku_str'                           => $sku_str,
            'recommend_no'                      => $recommend_no,
            'status'                            => trim($this->input->post('recommend_status')),
            'cause'                             => trim($this->input->post('recommend_cause')),
            'email_time'                        => trim($this->input->post('email_time')),
            'remark'                            => trim($this->input->post('recommend_remark')),
            'creator'                           => $this->get_current_login_name(),
            'created_date'                      => date('Y-m-d h:m:s'),
        );

        try
        {
            $recommend_id = $this->quality_testing_model->save_recommend($data);
            echo $this->create_json(1, lang('save_recommend_successed'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    

    public function recommend_list()
    {
        $search = trim($this->input->post('search'));
        $type = $this->input->post('type');
        $tag = $this->input->post('tag');

        $orders = $this->quality_testing_model->fetch_order_by_type($search, $type, $tag);
//        if( ! $orders)
//        {
            $orders_completed = $this->quality_testing_model->fetch_order_by_type_from_completed($search, $type, $tag);
//        }
            
            $orders = array_merge($orders, $orders_completed);
            
        $data = array(
            'orders' => $orders,
            'tag' => $tag,
        );
        $this->load->view('qt/recommend/recommend_list', $data);
    }

    public function add_edit_recommend($id = NULL,$tag)
    {
//        $order = $this->order_model->get_order_with_id($id);
//        if( ! $order)
//        {
//            $order = $this->order_model->get_order_with_id_from_completed($id);
//        }

        if($tag === 'order_table')
        {
            $order = $this->order_model->get_order_with_id($id);
        }
        else if($tag === 'completed_table')
        {
            $order = $this->move_order($id);
        }
        $recommend = NULL;
        $data = array(
            'order' => $order,
            'recommend'   => $recommend,
        );
        $this->template->write_view('content', 'qt/recommend/add_edit_recommend', $data);
        $this->template->add_js('static/js/ajax/qt.js');
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('recommend');
        $this->enable_sort('recommend');

        $this->render_list('qt/recommend/management', 'edit');
    }

    public function save_recommend()
    {
        $rules = array(
            array(
                'field' => 'email_time',
                'label' => lang('email_time'),
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

        if( ! $this->input->post('recommend_no'))
        {
            echo $this->create_json(0, lang('recommend_no_is_null'));
            return;
        }
        
        if ($this->quality_testing_model->check_exists('order_recommend_list', array('order_id' => $this->input->post('order_id'))))
        {
            echo $this->create_json(0, lang('recommend_order_exists'));
            return;
        }

        $recommend_no = trim($this->input->post('recommend_no'));
        if ($this->quality_testing_model->check_exists('order_recommend_list', array('recommend_no' => $recommend_no)))
        {
            echo $this->create_json(0, lang('recommend_no_exists'));
            return;
        }

        $skus = $this->input->post('sku');
        $qtys = $this->input->post('qty');
        $sku_str = '';
        $qty_str = '';
        if($skus && $qtys)
        {
            $sku_str = implode(',', $skus);
            $qty_str = implode(',', $qtys);
        }
        else
        {
            echo $this->create_json(0, lang('order_is_not_product'));
            return;
        }

        $data = array(
            'recommend_id'                      => $this->input->post('recommend_id'),
            'order_id'                          => trim($this->input->post('order_id')),
            'qty_str'                           => $qty_str,
            'sku_str'                           => $sku_str,
            'recommend_no'                      => $recommend_no,
            'status'                            => trim($this->input->post('recommend_status')),
            'cause'                             => trim($this->input->post('recommend_cause')),
            'email_time'                        => trim($this->input->post('email_time')),
            'remark'                            => trim($this->input->post('recommend_remark')),
            'creator'                           => $this->get_current_login_name(),
            'created_date'                      => date('Y-m-d h:m:s'),
        );

        try
        {
            $recommend_id = $this->quality_testing_model->save_recommend($data);
            echo $this->create_json(1, lang('save_recommend_successed'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_recommend()
    {
        $recommend_id = $this->input->post('id');
        $this->quality_testing_model->drop_recommend($recommend_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $recommends = $this->quality_testing_model->fetch_all_recommends();
        $data = array(
            'recommends' => $recommends,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function instant_save_recommend_order()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
            $user_name = get_current_login_name();
            
            $status = $this->quality_testing_model->get_one('order_recommend_list', 'status',array('id'=>$id));
            if($status === 'warehousing' && $type === 'status')
            {
                echo $this->create_json(0, lang('warehousing_not_again'));
                return;
            }

            if($type === 'recommend_no')
            {
                 $recommend_no = $this->quality_testing_model->get_one('order_recommend_list', 'recommend_no',array('id'=>$id));
                if ( $value != $recommend_no && $this->quality_testing_model->check_exists('order_recommend_list', array('recommend_no' => $value)))
                {
                    echo $this->create_json(0, lang('recommend_no_exists'));
                    return;
                }
            }

            $this->quality_testing_model->instant_save_order($id, $type, $value, $user_name);

            if($value === 'warehousing')
            {
                $sku_str = $this->quality_testing_model->get_one('order_recommend_list', 'sku_str',array('id'=>$id));
                $qty_str = $this->quality_testing_model->get_one('order_recommend_list', 'qty_str',array('id'=>$id));

                $skus_arr = explode(',', $sku_str);
                $qtys_arr = explode(',', $qty_str);
                $count = count($skus_arr);

                for ($i = 0; $i < $count; $i++)
                {
                    $p_id = $this->quality_testing_model->get_one('product_basic', 'id',array('sku'=>$skus_arr[$i]));
                    $report_id = $this->product_model->apply_product_instock($p_id, element($i, $qtys_arr));
                }
            }

            if($type == 'status' || $type == 'cause' )
            {
                $value = lang($value);
            }
            echo $this->create_json(1, lang('ok'),$value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }


    /*
     * 转移订单 ：把归档订单列表（order_list_completed）的订单转移到活跃订单列表（order_list）里去.
     * 参数 ： 归档订单的ID.
     * 返回 ： TURE or FALST .
     * **/
    public function move_order($id)
    {
        $order_object = $this->order_model->get_order_by_id_from_completed($id);

        unset ($order_object->order_id);
        unset ($order_object->id);

        try
        {
            $new_id = $this->order_model->add_order_to_order_list($order_object);
            if($new_id)
            {
                $this->order_model->delete_order_by_id_from_completed($id);

                $order_object->id = $new_id;
                return $order_object;
            }
            else
            {
                return FALSE;
            }
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }


    
}

?>
