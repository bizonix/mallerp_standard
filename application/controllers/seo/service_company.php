<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Service_company extends Seo
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('seo_model');
        $this->load->model('seo_service_company_model');
        $this->load->library('form_validation');
        $this->load->model('permission_copy_model');
    }

    public function manage()
    {
        $service_companys = $this->seo_service_company_model->fetch_all_service_companys();
        $data = array(
            'service_companys'          => $service_companys,
        );
        $this->template->write_view('content','seo/service_company/management', $data);
        $this->template->render();
    }

    public function add()
    {
        $user_id = get_current_user_id();
        $data = array(
            'name'               => '[edit]',
            'website'            => '[edit]',
            'description'        => '[edit]',
            'creator_id'         => $user_id ,
        );
        try
        {
            $this->seo_service_company_model->add($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop()
    {
        $id = $this->input->post('id');
        $this->seo_service_company_model->drop($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verify()
    {     
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));       
        $service_company = $this->seo_service_company_model->fetch_service_company($id);       
        try
        {
            switch ($type)
            {            
                case 'name':
                    if ($this->seo_service_company_model->check_exists('seo_service_company', array('name' => $value)) && $value != $service_company->name)
                    {
                       echo $this->create_json(0, lang('service_company_exists'),  $service_company->name);
                       return;
                    }
                     break;
                case 'website':
                    if ($this->seo_service_company_model->check_exists('seo_service_company', array('website' => $value)) && $value != $service_company->website)
                    {
                       echo $this->create_json(0, lang('website_exists'), $service_company->website);
                       return;
                    }
                     break;
               case 'description':
                    if ($this->seo_service_company_model->check_exists('seo_service_company', array('description' => $value)) && $value != $service_company->description)
                    {
                       echo $this->create_json(0, lang('description_exists'), $service_company->description);
                       return;
                    }
                    break;
            }
            $user_id = get_current_user_id();
            $this->seo_service_company_model->verify($id, $type, $value, $user_id);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }      
    }



}
?>
