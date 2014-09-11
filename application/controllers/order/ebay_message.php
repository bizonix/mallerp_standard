<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Ebay_message extends Order
{
    public function  __construct() {
        parent::__construct();
        $this->load->model('sale_order_model');
        $this->load->model('order_model');
		$this->load->model('ebay_model');
        $this->load->model('system_model');
		$this->load->config('config_ebay');
        $this->load->library('form_validation');
        $this->load->helper('validation_helper');
		
    }
    
    public function catalog()
    {
        $catalogs = $this->ebay_model->fetch_ebay_message_catalog();

        $data = array(
            'catalogs' => $catalogs,
        );
        $this->template->write_view('content', 'order/ebay_message/catalog', $data);
        $this->template->render();
    }
	public function add_new_catalog()
    {
        $user_id = get_current_user_id();
        $created_date = get_current_time();
        $data = array(
            'category_name'          => '[edit]',
            'ebay_note'        => '[edit]',
            'user'           => $user_id,
            'category_keywords'        => '[edit]',
            'created_date '  => $created_date,
        );
        try
        {
            $this->ebay_model->add_ebay_message_catalog($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
	public function update_catalog()
	{
		$id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        //$value = no_space($value);
		try
        {
            $this->ebay_model->update_catalog($id, $type, $value);

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
	}
	public function template()
    {
        $templates = $this->ebay_model->fetch_ebay_message_template();

        $data = array(
            'templates' => $templates,
        );
        $this->template->write_view('content', 'order/ebay_message/template', $data);
        $this->template->render();
    }
	public function add_new_template()
    {
        $user_id = get_current_user_id();
        $created_date = get_current_time();
        $data = array(
            'template_name'          => '[edit]',
            'template_content'        => '[edit]',
            'user'           => $user_id,
            'template_subject'        => '[edit]',
            'created_date '  => $created_date,
        );
        try
        {
            $this->ebay_model->add_ebay_message_template($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
	public function drop_template_by_id()
	{
		$id = $this->input->post('id');
		try
        {
            $this->ebay_model->drop_ebay_message_template_by_id($id);
            echo $this->create_json(1, lang('ok'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
	}
	public function update_template()
	{
		$id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        //$value = no_space($value);
		try
        {
            $this->ebay_model->update_template($id, $type, $value);

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
	}
	public function manage()
	{
		$this->enable_search('ebay_message');
		$this->enable_sort('ebay_message');
        $this->render_list('order/ebay_message/manage', 'view', 'fetch_all_ebay_message');
	}
	private function render_list($url, $action, $method)
    {
        $messages = $this->ebay_model->$method();

        $data = array(
            'messages'    => $messages,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
	public function ebay_message_reply($id,$key)
	{
		$message = $this->ebay_model->fetch_ebay_message_by_id($id);
		$templates = $this->ebay_model->fetch_ebay_message_template();
		$orders = $this->order_model->fetch_order_list_by_buyerid($message->sendid);
		$img_url= $this->ebay_model->get_ebay_image_url_by_item_id($message->itemid);
        $data = array(
            'message' => $message,
			'templates' => $templates,
			'orders' =>$orders,
			'img_url'=>$img_url,
        );
        $this->template->write_view('content', 'order/ebay_message/ebay_message_reply', $data);
        $this->template->render();
	}
	public function ebay_message_reply_save(){
		$id = $this->input->post('id');
		$replaycontent = $this->input->post('replaycontent');
		$user_id = $this->get_current_user_id();
		
		try
        {
			$data = array(
					  'replaycontent' =>$replaycontent,
					  'replyuser' =>$user_id,
					  'update_ebay'=>0,
					  'reply_time'=>date('Y-m-d H:i:s'),
					  'status'=>2,
					  );
            $this->ebay_model->update_ebay_message_by_id($id,$data);

            echo $this->create_json(1, lang('ok') );
        }
        catch(Exception $e)
        {
			echo lang('error_msg');
            $this->ajax_failed();
            
        }
		//echo $id."<br>".$replaycontent."<br>".$user_id;
	}
	
	public function message_status_hold(){
		if (!$this->input->is_post()) {
            return;
        }

        $post_keys = array_keys($_POST);
        $message_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $message_ids[] = $_POST[$key];
            }
        }
		try
        {
			foreach($message_ids as $message_id){
				$data = array(
							  'status'=>3,
							  );
				$this->ebay_model->update_ebay_message_by_id($message_id,$data);
			}
		}
		catch(Exception $e)
        {
			echo lang('error_msg');
            $this->ajax_failed();
            
        }
	}
	public function message_status_needless(){
		if (!$this->input->is_post()) {
            return;
        }

        $post_keys = array_keys($_POST);
        $message_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $message_ids[] = $_POST[$key];
            }
        }
		try
        {
			foreach($message_ids as $message_id){
				$data = array(
							  'status'=>4,
							  );
				$this->ebay_model->update_ebay_message_by_id($message_id,$data);
			}
		}
		catch(Exception $e)
        {
			echo lang('error_msg');
            $this->ajax_failed();
            
        }
	}
	
	
	public function maillist(){

	}
   
}

?>
