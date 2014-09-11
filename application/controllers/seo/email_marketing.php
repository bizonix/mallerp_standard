<?php
require_once APPPATH.'controllers/seo/seo'.EXT;
class Email_marketing extends Seo
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('edm_email_model');
        $this->load->library('form_validation');
        $this->load->helper('seo');
        $this->template->add_js('static/js/ajax/seo.js');
    }

    public function email_marketing_manage()
    {
        $this->enable_search('email_template');
        $this->enable_sort('email_template');
        $email_tps = $this->edm_email_model->fetch_eamil_template_infos();
        $data = array(
            'email_tps'  => $email_tps,
        );        
        $this->template->write_view('content', 'seo/email_marketing_management',$data);
        $this->template->render();
    }


    public function edit($id)
    {
        $email_infos = $this->edm_email_model->fetch_email_infos($id);
        $data = array(
            'email_infos'   => $email_infos,
        );
        $this->template->write_view('content', 'seo/email_tp_edit',$data);
        $this->template->render();
    }

    public function drop_edm_email()
    {
        $id = $this->input->post('id');
        $this->edm_email_model->delete_edm_email($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function edit_save()
    {
        $rules = array(
            array(
                'field' => 'title',
                'lable' => 'email title',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'content',
                'label' => 'email content',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'code',
                'label' => 'email code',
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
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $code = $this->input->post('code');
        $content = $this->input->post('content');
        $name = get_current_user_name();
        $update_time = get_current_time();
        $remark = $this->edm_email_model->fetch_email_remark($id);
        $remark .= $name.lang('on').$update_time.lang('edit_template')."<br>";
        $data = array(
            'title'     => $title,
            'code'      => $code,
            'content'   => $content,
            'remark'    => $remark,
        );

        try
        {
            $this->edm_email_model->update_email_tp($data,$id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_new_email_template()
    {
        $user_id = get_current_user_id();
        $update_time = get_current_time();
        $name = get_current_user_name();
        $created_date = get_current_time();
        $remark = $name.lang('on').$update_time.lang('create_template')."<br>";
        $data = array(
            'title'          => '',
            'content'        => '',
            'code'           => '',
            'creator'        => $user_id,
            'remark'         => $remark,
            'created_date '  => $created_date,
        );
        try
        {
            $this->edm_email_model->add_edm_email($data);
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
