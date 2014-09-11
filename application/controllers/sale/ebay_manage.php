<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/sale/sale'.EXT;

class Ebay_manage extends Sale
{
    private $ebay_id_list;
    
	public function  __construct() {
		parent::__construct();
        if (!session_id()) {
            session_start();
        }
        $this->load->model('ebay_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
        $this->load->model('sale_order_model');
        $this->load->config('config_ebay');
	}

	public function index() {
        $data['ebay_id_list'] = $this->ebay_id_list;

		$this->load->view('sale/ebay/online_ebay_items', $data);
	}

    public function upload() {
        $data['title'] = 'Add a new ebay item';
        $data['action_type'] = 'upload';
        $data['item_id'] = null;
        $data['site'] = null;
        $data['ebay_id'] = null;
        $data['ebay_id_list'] = $this->ebay_id_list;
        
        $this->load->view('dashboard', $data);
    }

    public function copy($site, $item_id, $ebay_id=0) {
        $data['title'] = 'Copy a ebay item';
        $data['item_id'] = $item_id;
        $data['action_type'] = 'copy';
        $data['site'] = $site;
        $data['ebay_id'] = $ebay_id;
        $data['ebay_id_list'] = $this->ebay_id_list;

        $this->load->view('sale/ebay/dashboard', $data);
    }

    public function myebay_manage()
    {
        $this->enable_search('myebay_list');
        $this->enable_sort('myebay_list');

        $this->render_list('sale/ebay_manage/management', 'edit');
    }

    private function render_list($url, $action)
    {
        $currency_code = $this->order_model->fetch_currency();
        
        $ebay_id_str = $this->sale_order_model->get_one('saler_ebay_id_map', 'ebay_id_str', array('saler_id'=>  get_current_user_id()));
        $ebay_ids = explode(',', $ebay_id_str);

        $ebay_products = $this->ebay_model->fetch_all_ebay_product($ebay_ids);

        if ($this->is_super_user())
        {
            $configs = $this->config->item('ebay_id');
            $ebay_ids = array_values($configs);
        }

        $sale_statuses = fetch_statuses('sale_status');
        $sale_statuses[0] = 'unknown';
        
        $data = array(
            'ebay_products'         => $ebay_products,
            'action'                => $action,
            'currency_code'         => $currency_code,
            'ebay_ids'              => $ebay_ids,
            'sale_statuses'         => $sale_statuses,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function saler_ebay_id_setting()
    {
        $ebays = $this->ebay_model->fetch_all_saler_ebay_id();
        $salers = $this->sale_order_model->fetch_all_salers_input_user_map();
        $data = array(
                'ebays'    => $ebays,
                'salers'    => $salers,
        );
        $this->template->write_view('content','sale/ebay_manage/saler_ebay_id_setting', $data);
        $this->template->render();
    }

    public function drop_saler_ebay_id_by_id()
    {
        $id = $this->input->post('id');
        $this->ebay_model->drop_saler_ebay_id_by_id($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_saler_ebay_id()
    {

        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        $value = no_space($value);

        $currency_code = $this->ebay_model->fetch_saler_ebay_id_by_id($id);
        if ($type == 'saler_id' && $this->ebay_model->check_exists('saler_ebay_id_map', array('saler_id' => $value)) && $value != $currency_code->saler_id )
        {
           echo $this->create_json(0, lang('saler_exists'));
           return;
        }

        try
        {
            $this->ebay_model->update_exchange_saler_ebay_id($id, $type, $value);
            if($type == 'saler_id')
            {
                $value = fetch_user_name_by_id($value);
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_saler_ebay_id()
    {
        $data = array(
            'saler_id'            => 0,
            'ebay_id_str'         => '[edit]',
        );
        try
        {
            $this->ebay_model->add_saler_ebay_id($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function auction_profit_statistics()
    {
        $this->enable_search('auction_statistics');
        $this->enable_sort('auction_statistics');

        $rows = $this->ebay_model->fetch_all_auction_products();
        $data = array(
                'rows'    => $rows,
        );
        $this->template->write_view('content','sale/ebay_manage/auction_profit_statistics', $data);
        $this->template->render();
    }
}

/* End of file ebay.php */
/* Location: ./system/application/controllers/ebay.php */
