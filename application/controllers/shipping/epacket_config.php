<?php

require_once APPPATH . 'controllers/shipping/shipping' . EXT;

class Epacket_config extends Shipping {

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('script');
        $this->load->model('mixture_model');
        $this->load->model('epacket_model');
        $this->load->model('user_model');
        $this->load->model('product_model');
        $this->load->model('order_shipping_record_model');
        $this->load->model('shipping_code_model');
        $this->load->helper('shipping_helper');
		$this->load->model('shipping_code_model');
    }

    public function manage() {
		$all_epacket_config=$this->epacket_model->get_all_epacket_config();
		$all_stock_user=$this->user_model->fetch_all_stock_users();
		$data=array(
			'epacket_configs'=>$all_epacket_config,
			'stock_users'=>$all_stock_user,
			);

        $this->template->write_view('content', 'shipping/epacket_config/management', $data);
        $this->template->render();
    }
	public function add_epacket_config(){
		$all_stock_user=$this->user_model->fetch_all_stock_users();
		$stock_codes=$this->shipping_code_model->fetch_stock_codes_status();
		$data=array(
			'stock_users'=>$all_stock_user,
			'stock_codes'=>$stock_codes,
			);
        $this->template->write_view('content', 'shipping/epacket_config/add', $data);
        $this->template->render();
	}
	public function add_edit($id){
		$all_stock_user=$this->user_model->fetch_all_stock_users();
		$stock_codes=$this->shipping_code_model->fetch_stock_codes_status();
		$epacket_config=$this->epacket_model->get_epacket_config_by_id($id);
		$data=array(
			'stock_users'=>$all_stock_user,
			'stock_codes'=>$stock_codes,
			'epacket_config'=>$epacket_config,
			);
        $this->template->write_view('content', 'shipping/epacket_config/view_edit', $data);
        $this->template->render();
	}
	public function add_save(){
		if ($this->input->is_post())
        {
			$user_id=$this->input->post('user_id');
            $stock_code = $this->input->post('stock_code');
			$is_register= $this->input->post('is_register');
            $pickupaddress_company = $this->input->post('pickupaddress_company');
			$pickupaddress_contact = $this->input->post('pickupaddress_contact');
			$pickupaddress_email = $this->input->post('pickupaddress_email');
			$pickupaddress_mobile = $this->input->post('pickupaddress_mobile');
			$pickupaddress_phone = $this->input->post('pickupaddress_phone');
			$pickupaddress_postcode = $this->input->post('pickupaddress_postcode');
			$pickupaddress_country = $this->input->post('pickupaddress_country');
			$pickupaddress_province = $this->input->post('pickupaddress_province');
			$pickupaddress_city = $this->input->post('pickupaddress_city');
			$pickupaddress_district = $this->input->post('pickupaddress_district');
			$pickupaddress_street = $this->input->post('pickupaddress_street');
			
			
			$shipfromaddress_company = $this->input->post('shipfromaddress_company');
			$shipfromaddress_contact = $this->input->post('shipfromaddress_contact');
			$shipfromaddress_email = $this->input->post('shipfromaddress_email');
			$shipfromaddress_mobile = $this->input->post('shipfromaddress_mobile');
			$shipfromaddress_postcode = $this->input->post('shipfromaddress_postcode');
			$shipfromaddress_country = $this->input->post('shipfromaddress_country');
			$shipfromaddress_province = $this->input->post('shipfromaddress_province');
			$shipfromaddress_city = $this->input->post('shipfromaddress_city');
			$shipfromaddress_district = $this->input->post('shipfromaddress_district');
			$shipfromaddress_street = $this->input->post('shipfromaddress_street');


			$returntoaddress_company = $this->input->post('returnaddress_company');
			$returntoaddress_contact = $this->input->post('returnaddress_contact');
			$returntoaddress_postcode = $this->input->post('returnaddress_postcode');
			$returntoaddress_country = $this->input->post('returnaddress_country');
			$returntoaddress_province = $this->input->post('returnaddress_province');
			$returntoaddress_city = $this->input->post('returnaddress_city');
			$returntoaddress_district = $this->input->post('returnaddress_district');
			$returntoaddress_street = $this->input->post('returnaddress_street');
			$pagesize = $this->input->post('pagesize');
			$emspickuptype = $this->input->post('emspickuptype');
			$update_date = date('Y-m-d H:i:s');
			 
        }
		if($this->epacket_model->epacket_config_user_id_exists($user_id)){
			echo $this->create_json(0, lang('username_exists'));
			return;
		}
		$data=array(
					'user_id'=>$user_id,
					'stock_code'=>$stock_code,
					'is_register'=>$is_register,
					'pickupaddress_company'=>$pickupaddress_company,
					'pickupaddress_contact'=>$pickupaddress_contact,
					'pickupaddress_email'=>$pickupaddress_email,
					'pickupaddress_mobile'=>$pickupaddress_mobile,
					'pickupaddress_phone'=>$pickupaddress_phone,
					'pickupaddress_postcode'=>$pickupaddress_postcode,
					'pickupaddress_country'=>$pickupaddress_country,
					'pickupaddress_province'=>$pickupaddress_province,
					'pickupaddress_city'=>$pickupaddress_city,
					'pickupaddress_district'=>$pickupaddress_district,
					'pickupaddress_street'=>$pickupaddress_street,
					'shipfromaddress_company'=>$shipfromaddress_company,
					'shipfromaddress_contact'=>$shipfromaddress_contact,
					'shipfromaddress_email'=>$shipfromaddress_email,
					'shipfromaddress_mobile'=>$shipfromaddress_mobile,
					'shipfromaddress_postcode'=>$shipfromaddress_postcode,
					'shipfromaddress_country'=>$shipfromaddress_country,
					'shipfromaddress_province'=>$shipfromaddress_province,
					'shipfromaddress_city'=>$shipfromaddress_city,
					'shipfromaddress_district'=>$shipfromaddress_district,
					'shipfromaddress_street'=>$shipfromaddress_street,
					'returntoaddress_company'=>$returntoaddress_company,
					'returntoaddress_contact'=>$returntoaddress_contact,
					'returntoaddress_postcode'=>$returntoaddress_postcode,
					'returntoaddress_country'=>$returntoaddress_country,
					'returntoaddress_province'=>$returntoaddress_province,
					'returntoaddress_city'=>$returntoaddress_city,
					'returntoaddress_district'=>$returntoaddress_district,
					'returntoaddress_street'=>$returntoaddress_street,
					'pagesize'=>$pagesize,
					'emspickuptype'=>$emspickuptype,
					'update_date'=>$update_date,
					);
		$this->epacket_model->add_epacket_config($data);
		echo $this->create_json(1, lang('ok'));
	}
	public function edit_save(){
		if ($this->input->is_post())
        {
			$id=$this->input->post('id');
			$user_id=$this->input->post('user_id');
            $stock_code = $this->input->post('stock_code');
			$is_register= $this->input->post('is_register');
            $pickupaddress_company = $this->input->post('pickupaddress_company');
			$pickupaddress_contact = $this->input->post('pickupaddress_contact');
			$pickupaddress_email = $this->input->post('pickupaddress_email');
			$pickupaddress_mobile = $this->input->post('pickupaddress_mobile');
			$pickupaddress_phone = $this->input->post('pickupaddress_phone');
			$pickupaddress_postcode = $this->input->post('pickupaddress_postcode');
			$pickupaddress_country = $this->input->post('pickupaddress_country');
			$pickupaddress_province = $this->input->post('pickupaddress_province');
			$pickupaddress_city = $this->input->post('pickupaddress_city');
			$pickupaddress_district = $this->input->post('pickupaddress_district');
			$pickupaddress_street = $this->input->post('pickupaddress_street');
			
			
			$shipfromaddress_company = $this->input->post('shipfromaddress_company');
			$shipfromaddress_contact = $this->input->post('shipfromaddress_contact');
			$shipfromaddress_email = $this->input->post('shipfromaddress_email');
			$shipfromaddress_mobile = $this->input->post('shipfromaddress_mobile');
			$shipfromaddress_postcode = $this->input->post('shipfromaddress_postcode');
			$shipfromaddress_country = $this->input->post('shipfromaddress_country');
			$shipfromaddress_province = $this->input->post('shipfromaddress_province');
			$shipfromaddress_city = $this->input->post('shipfromaddress_city');
			$shipfromaddress_district = $this->input->post('shipfromaddress_district');
			$shipfromaddress_street = $this->input->post('shipfromaddress_street');

			$returntoaddress_company = $this->input->post('returnaddress_company');
			$returntoaddress_contact = $this->input->post('returnaddress_contact');
			$returntoaddress_postcode = $this->input->post('returnaddress_postcode');
			$returntoaddress_country = $this->input->post('returnaddress_country');
			$returntoaddress_province = $this->input->post('returnaddress_province');
			$returntoaddress_city = $this->input->post('returnaddress_city');
			$returntoaddress_district = $this->input->post('returnaddress_district');
			$returntoaddress_street = $this->input->post('returnaddress_street');
			$pagesize = $this->input->post('pagesize');
			$emspickuptype = $this->input->post('emspickuptype');
			$update_date = date('Y-m-d H:i:s');
			 
        }
		
		$data=array(
					'user_id'=>$user_id,
					'stock_code'=>$stock_code,
					'is_register'=>$is_register,
					'pickupaddress_company'=>$pickupaddress_company,
					'pickupaddress_contact'=>$pickupaddress_contact,
					'pickupaddress_email'=>$pickupaddress_email,
					'pickupaddress_mobile'=>$pickupaddress_mobile,
					'pickupaddress_phone'=>$pickupaddress_phone,
					'pickupaddress_postcode'=>$pickupaddress_postcode,
					'pickupaddress_country'=>$pickupaddress_country,
					'pickupaddress_province'=>$pickupaddress_province,
					'pickupaddress_city'=>$pickupaddress_city,
					'pickupaddress_district'=>$pickupaddress_district,
					'pickupaddress_street'=>$pickupaddress_street,
					'shipfromaddress_company'=>$shipfromaddress_company,
					'shipfromaddress_contact'=>$shipfromaddress_contact,
					'shipfromaddress_email'=>$shipfromaddress_email,
					'shipfromaddress_mobile'=>$shipfromaddress_mobile,
					'shipfromaddress_postcode'=>$shipfromaddress_postcode,
					'shipfromaddress_country'=>$shipfromaddress_country,
					'shipfromaddress_province'=>$shipfromaddress_province,
					'shipfromaddress_city'=>$shipfromaddress_city,
					'shipfromaddress_district'=>$shipfromaddress_district,
					'shipfromaddress_street'=>$shipfromaddress_street,
					'returntoaddress_company'=>$returntoaddress_company,
					'returntoaddress_contact'=>$returntoaddress_contact,
					'returntoaddress_postcode'=>$returntoaddress_postcode,
					'returntoaddress_country'=>$returntoaddress_country,
					'returntoaddress_province'=>$returntoaddress_province,
					'returntoaddress_city'=>$returntoaddress_city,
					'returntoaddress_district'=>$returntoaddress_district,
					'returntoaddress_street'=>$returntoaddress_street,
					'pagesize'=>$pagesize,
					'emspickuptype'=>$emspickuptype,
					'update_date'=>$update_date,
					);
		$this->epacket_model->update_epacket_config($id,$data);
		echo $this->create_json(1, lang('ok'));
	}
	public function drop_config_by_id($id = NULL)
	{
		$id = $this->input->post('id');
		$this->epacket_model->delete_epacket_config($id);
		echo $this->create_json(1, lang('ok'));
	}
	
}
?>
