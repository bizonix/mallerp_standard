<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Return_order extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
    }

    public function view_return_order($id)
    {
        $order_object = $this->order_model->get_order_by_id_from_completed($id);

        unset ($order_object->order_id);
        unset ($order_object->id);

        $new_id = $this->order_model->add_order_to_order_list($order_object);
        
        $this->order_model->delete_order_by_id_from_completed($id);
        
        $order_object->id = $new_id;
        
        $option = array(
            '0' =>lang('please_select'),
        );

        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_partial_refund');
        $option[$status_id] = lang('not_received_apply_for_partial_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_full_refund');
        $option[$status_id] = lang('not_received_apply_for_full_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_resending');
        $option[$status_id] = lang('not_received_apply_for_resending');

        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_partial_refund');
        $option[$status_id] = lang('received_apply_for_partial_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_full_refund');
        $option[$status_id] = lang('received_apply_for_full_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_resending');
        $option[$status_id] = lang('received_apply_for_resending');
        
 
        $status_id_nrar = $this->order_model->fetch_status_id('order_status','not_received_approved_resending');
        $status_id_rar = $this->order_model->fetch_status_id('order_status','received_approved_resending');
        if($order_object->order_status == $status_id_nrar || $order_object->order_status == $status_id_rar)
        {
            $status_id = $this->order_model->fetch_status_id('order_status','not_received_resended');
            $option[$status_id] = lang('not_received_resended');
            $status_id = $this->order_model->fetch_status_id('order_status','received_resended');
            $option[$status_id] = lang('received_resended');
        }
        
        $data = array(
            'order'         => $order_object,
            'options'       => $option,            
        );

        $this->template->write_view('content', 'order/special_order/view_order', $data);
        $this->template->render();
    }

}

?>
