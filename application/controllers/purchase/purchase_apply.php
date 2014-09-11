<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Purchase_apply extends Purchase
{
    public function __construct() {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->template->write_view('content', 'purchase/default');
        $this->template->render();
    }

    public function add()
    {
        $this->template->add_js('static/js/ajax/purchase.js');

        $this->template->write_view('content', 'purchase/purchase_apply/add');
        $this->template->render();
    }

    public function save($tag)
    {
        $rules = array();

        $rules[] = array(
            'field' => 'product_name',
            'label' => 'product name',
            'rules' => 'trim|required',
        );

        if($this->input->post('product_image_url'))
        {
            $rules[] = array(
                'field' => 'product_image_url',
                'label' => 'product image url',
                'rules' => 'trim|is_url',
            );
        }

        $rules[] = array(
            'field' => 'product_description',
            'label' => 'product description',
            'rules' => 'trim|required',
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if (trim($this->input->post('product_name')) !== $this->purchase_model->get_one('product_purchase_apply', 'product_name', array('id' => $this->input->post('apply_id'))))
        {
            if ($this->purchase_model->check_exists('product_purchase_apply', array('product_name' => $this->input->post('product_name'))))
            {
                echo $this->create_json(0, lang('product_name_exists'));
                return;
            }
        }

        $user = array();
        $user = $this->account->get_account();

        if($tag == 'approved' )
        {
            $status = 2 ;
        }
        else
        {
            $status = -1 ;
        }
        $data = array(
            'product_name'                      => trim($this->input->post('product_name')),
            'product_image_url'                 => trim($this->input->post('product_image_url')),
			'reference_links'                 => trim($this->input->post('reference_links')),
			'sales_strategy'                 => trim($this->input->post('sales_strategy')),
			'sales_statistics'                 => trim($this->input->post('sales_statistics')),
			'related_specifications'                 => trim($this->input->post('related_specifications')),
			'provider'                 => trim($this->input->post('provider')),
            'product_description'               => trim($this->input->post('product_description')),
            'develper_id'                      => $this->input->post('develper_id'),
            'apply_status'                      => $status,
            'review_user_id'                    => $user['id'],
        );

        try
        {
            $apply_id = $this->input->post('apply_id');

            $this->purchase_model->update_purchase_apply_by_id($apply_id, $data);

            echo $this->create_json(1, lang('purchase_apply_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function manage()
    {
        $this->enable_search('product_purchase_apply');
        $this->render_list('purchase/purchase_apply/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('product_purchase_apply');
        $this->render_list('purchase/purchase_apply/management', 'view');
    }
    
    public function view($id)
    {
        $this->template->add_js('static/js/ajax/purchase.js');

        $apply = $this->purchase_model->fetch_purchase_apply_by_id($id);
        
        $data = array(
            'apply'             => $apply,
            'action'            => 'view',
        );
        $this->template->write_view('content', 'purchase/purchase_apply/view_edit', $data);
        $this->template->render();
    }

    public function edit($id)
    {
        $this->template->add_js('static/js/ajax/purchase.js');
        
        $apply = $this->purchase_model->fetch_purchase_apply_by_id($id);

        $data = array(
            'apply'             => $apply,
            'action'            => 'edit',
        );
        $this->template->write_view('content', 'purchase/purchase_apply/view_edit', $data);
        $this->template->render();
    }

    public function save_edit()
    {
        $data = array();
        $this->template->write_view('content', 'purchase/purchase_apply/add_edit', $data);
        $this->template->render();
    }

    public function delete_apply()
    {
        $id = $this->input->post('id');
        $this->purchase_model->drop_apply($id);

        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $user = array();
        $user = $this->account->get_account();

        $applys = $this->purchase_model->fetch_all_purchase_apply();

        $data = array(
            'applys'    => $applys,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
