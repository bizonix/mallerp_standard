<?php
require_once APPPATH.'controllers/finance/finance'.EXT;
class Receipt_way extends Finance
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('receipt_way_model');
    }

    public function receipt_way_list()
    {
        $receipt_ways = $this->receipt_way_model->fetch_all_receipt_ways();
        $data = array(
                'receipt_ways'    => $receipt_ways,
        );
        $this->template->write_view('content','finance/receipt_way_list', $data);
        $this->template->render();
    }

    public function drop_receipt_way($id)
    {
        $id = $this->input->post('id');
        $this->receipt_way_model->drop_receipt_way($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verify_receipt_way()
    {      
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $receipt_way = $this->receipt_way_model->fetch_receipt_way($id);       
        try
        {
            switch ($type)
            {            
               case 'receipt_name':
                    if ($this->receipt_way_model->check_exists('receipt_way_list', array('receipt_name' => $value)) && $value != $receipt_way->receipt_name)
                    {
                       echo $this->create_json(0, lang('receipt_name_exists'), $receipt_way->receipt_name);
                       return;
                    }
                     break;
            }           
            $user_id = get_current_user_id();           
            $this->receipt_way_model->verify_receipt_way($id, $type, $value, $user_id);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
        
    }

    public function add_receipt_way()
    {
        $user_id = get_current_user_id();
        $data = array(
            'receipt_name'        => '[edit]',
            'creator_id'          => $user_id,           
       
        );
        try
        {
            $this->receipt_way_model->add_receipt_way($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}
?>
