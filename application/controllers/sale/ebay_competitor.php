<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/sale/sale'.EXT;

class Ebay_competitor extends Sale
{
	public function  __construct() {
		parent::__construct();
        
        $this->load->model('ebay_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
        $this->load->model('sale_order_model');
	}
    
    public function edit_competitor($item_id)
    {
        $competitors = $this->ebay_model->fetch_competitor_by_item_id($item_id);

        $data = array(
                'competitors'    => $competitors,
                'item_id'    => $item_id,
        );
        $this->template->write_view('content','sale/competitor', $data);
        $this->template->render();
    }
    
    public function update_competitor()
    {

        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        $value = no_space($value);

        $currency_code = $this->ebay_model->fetch_competitor_by_id($id);

        if ($type == 'seller_id' && $value !== $currency_code->seller_id && $this->ebay_model->check_exists('myebay_list_competitor', array('seller_id' => $value, 'item_id'=>$currency_code->item_id, 'url'=>$currency_code->url )))
        {
           echo $this->create_json(0, lang('competitor_exists'));
           return;
        }
        
        if($type == 'allowed_difference' && (!is_numeric($value)))
        {
            echo $this->create_json(0, lang('your_input_is_not_range_numeric'));
            return;
        }
        
        if ($type == 'url' && $value !== $currency_code->url && $this->ebay_model->check_exists('myebay_list_competitor', array('url' => $value, 'item_id'=>$currency_code->item_id, 'seller_id'=>$currency_code->seller_id )))
        {
           echo $this->create_json(0, lang('competitor_exists'));
           return;
        }

        try
        {
            $this->ebay_model->update_competitor($id, $type, $value);

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    
        
    public function add_competitor()
    {
        $data = array(
            'item_id'            => $this->input->post('item_id'),
            'seller_id'          => '[edit]',
            'url'                => '[edit]',
            'allowed_difference' => 0,
        );
        try
        {
            $this->ebay_model->add_competitor($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    
        
    public function drop_competitor_by_id()
    {
        $id = $this->input->post('id');
        $this->ebay_model->drop_competitor_by_id($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

}
