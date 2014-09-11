<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Content_edit extends Seo
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('seo_model');
        $this->load->model('user_model');
        $this->load->model('seo_service_company_model');
        $this->load->library('form_validation');
        $this->template->add_js('static/js/sorttable.js');
    }
    
    public function add_edit($id = NULL)
    {
        $types = $this->seo_model->fetch_all_content_types();
        $catalogs = $this->seo_model->fetch_all_content_catalogs();
        $seo_users = $this->user_model->fetch_all_seo_users();
        $content_companys = $this->seo_service_company_model->fetch_all_service_companys();        
        $content = NULL;
        $catalog_ids = NULL;
        $permissions = NULL;
        $company_permissions = NULL;
        if ($id)
        {
            $content = $this->seo_model->fetch_content($id);
            $catalog_ids = $this->seo_model->fetch_saved_catalog_ids($id);
            $permissions = $this->seo_model->fetch_content_permissions($id);
            $company_permissions = $this->seo_service_company_model->fetch_content_company_permissions($id);
        }
        
        $data = array(
            'types'                 => $types,
            'catalogs'              => $catalogs,
            'content'               => $content,
            'catalog_ids'           => $catalog_ids,
            'seo_users'             => $seo_users,
            'permissions'           => $permissions,
            'content_companys'      => $content_companys,
            'company_permissions'   => $company_permissions,
        );
        $this->template->write_view('content', 'seo/content_edit/add_edit', $data);
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('seo_content');
        $this->enable_sort('seo_content');

        $this->render_list('seo/content_edit/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('seo_content');
        $this->enable_sort('seo_content');
        $this->render_list('seo/content_edit/management', 'view');
    }

    public function view()
    {
        $seg = $this->uri->segment(4);
        $popup = false;
        if ($seg == 'popup')
        {
            $this->template->set_template('popup');
            $id = $this->uri->segment(5);
            $popup = true;
        }
        else
        {
            $id = $seg;
        }
        $types = $this->seo_model->fetch_all_content_types();
        $catalogs = $this->seo_model->fetch_all_content_catalogs();
        $seo_users = $this->user_model->fetch_all_seo_users();
        $content = $this->seo_model->fetch_content($id);
        $catalog_ids = $this->seo_model->fetch_saved_catalog_ids($id);
        $permissions = $this->seo_model->fetch_content_permissions($id);      
        $data = array(
            'types'         => $types,
            'catalogs'      => $catalogs,
            'content'       => $content,
            'catalog_ids'   => $catalog_ids,
            'seo_users'     => $seo_users,
            'permissions'   => $permissions,
            'popup'         => $popup,            
        );
        $this->template->write_view('content', 'seo/content_edit/view_detail', $data);      
        $this->template->render();
    }

    public function content_detail($id=NULL, $company_id = -1)
    {
        $this->enable_sort('content_detail');
        
        if ($this->input->is_post())
        {
           $id = $this->input->post('content_id');
        }
        $seo_resources = $this->seo_model->fetch_all_release_resource_by_user($id);
        $resource_ids =  array();
        foreach($seo_resources as $seo_resource)
        {
             $resource_ids[] = $seo_resource->resource_id;
        }       
        $types = $this->seo_model->fetch_all_content_types();
        $catalogs = $this->seo_model->fetch_all_content_catalogs();
        $seo_users = $this->user_model->fetch_all_seo_users();
        $content = $this->seo_model->fetch_content($id);
        $catalog_ids = $this->seo_model->fetch_saved_catalog_ids($id);
        $permissions = $this->seo_model->fetch_content_permissions($id);
        $contents = $this->seo_model->fetch_all_contents();
        $resources = $this->seo_model->fetch_no_release_resources_by_user($this->get_current_user_id(), $resource_ids, $id, $company_id,$content->language);
        $data = array(
            'types'         => $types,
            'catalogs'      => $catalogs,
            'content'       => $content,
            'catalog_ids'   => $catalog_ids,
            'seo_users'     => $seo_users,
            'permissions'   => $permissions,
            'contents'      => $contents,
            'resources'     => $resources,
            'company_id'    => $company_id,

        );

       $this->template->write_view('content', 'seo/content_edit/content_detail', $data);
       $this->template->add_js('static/js/ajax/seo.js');
       $this->template->add_js('static/js/ajax/purchase.js');
       $this->template->render();
    }

    public function add_save()
    {
        $rules = array(
            array(
                'field' => 'title',
                'lable' => 'content title',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'content_area',
                'label' => 'content textarea',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'catalogs',
                'label' => 'catalog',
                'rules' => 'required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        // check if content exists ?
        if ($this->input->post('content_id') < 0 &&
            $this->seo_model->check_exists('seo_content', array('content' => $this->input->post('content_area'))))
        {
            echo $this->create_json(0, lang('content_exists'));

            return;
        }
        $data = array(
            'content_id'    => $this->input->post('content_id'),
            'language'      => $this->input->post('language'),
            'title'         => trim($this->input->post('title')),
            'type'          => $this->input->post('type'),
            'content'       => trim($this->input->post('content_area')),
        );
        $account = $this->account->get_account();
        $data['owner_id'] = $account['id'];

        try
        {
            $content_id = $this->seo_model->save_content($data);
            $catalogs = $this->input->post('catalogs');
            $this->seo_model->save_content_catalogs($content_id, $catalogs);

            $user_ids = $this->input->post('permissions') ;           
            if (empty($user_ids))
            {
                $user_ids = array();
            }
            $this->seo_model->save_content_permissions($content_id, $user_ids);

            $company_ids = $this->input->post('company_permissions') ;
            if (empty($company_ids))
            {
                $company_ids = array();
            }
            $this->seo_service_company_model->save_content_company_permissions($content_id, $company_ids);
            
            echo $this->create_json(1, lang('content_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function edit_save()
    {
        $rules = array(
            array(
                'field' => 'title',
                'lable' => 'content title',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'content_area',
                'label' => 'content textarea',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'catalogs',
                'label' => 'catalog',
                'rules' => 'required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        // check if content exists ?
        if ($this->input->post('content_id') < 0 &&
            $this->seo_model->check_exists('seo_content', array('content' => $this->input->post('content_area'))))
        {
            echo $this->create_json(0, lang('content_exists'));

            return;
        }
        $data = array(
            'content_id'    => $this->input->post('content_id'),
            'language'      => $this->input->post('language'),
            'title'         => trim($this->input->post('title')),
            'type'          => $this->input->post('type'),
            'content'       => trim($this->input->post('content_area')),
        );
        $account = $this->account->get_account();
        $data['owner_id'] = $account['id'];

        try
        {
            $content_id = $this->seo_model->save_content($data);
            $catalogs = $this->input->post('catalogs');
            $this->seo_model->save_content_catalogs($content_id, $catalogs);

            $user_ids = $this->input->post('permissions') ;
            if (empty($user_ids))
            {
                $user_ids = array();
            }
            $this->seo_model->save_content_permissions($content_id, $user_ids);

            $company_ids = $this->input->post('company_permissions') ;
            if (empty($company_ids))
            {
                $company_ids = array();
            }
            $this->seo_service_company_model->save_content_company_permissions($content_id, $company_ids);

            $integral = trim($this->input->post('integral'));
            if($content_id > 0)
            {
                $content = $this->seo_model->fetch_content_integral($content_id);
                $reviewer_id = get_current_user_id();
                $data = array(
                    'user_id'           => $content->owner_id,
                     'type'             => 'content',
                    'content_id'        => $content_id,
                    'integral'          => $integral,
                    'reviewer_id'       => $reviewer_id,
                );
                if($this->seo_model->check_exists('user_integral', array('content_id' => $content_id, 'type' => 'content')))
                {
                    $this->seo_model->verify_integral($content_id, $data, 'content');
                }
                else
                {
                    $this->seo_model->add_integral($data);
                }
                $this->verify_content_review($content_id);
            }
            echo $this->create_json(1, lang('content_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }


    public function drop_content()
    {
        $content_id = $this->input->post('id');
        $this->seo_model->drop_content($content_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }
    
    private function render_list($url, $action)
    {    
        $categorys = $this->seo_model->fetch_all_content_type();

        $options = array(''=>lang('all'));

        foreach ($categorys as $category)
        {
            $options[$category->id] = $category->name;
        }

//        $contents = $this->seo_model->fetch_all_contents();
        $contents = $this->seo_model->fetch_all_contents_by_pricrity();
        
        $data = array(
            'contents'  => $contents,
            'options'   => $options,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function content_catalog()
    {    
        $this->content_catalog_list('seo/content_edit/content_catalog', 'edit');
    }

    public function view_content_catalog()
    {
        $this->content_catalog_list('seo/content_edit/content_catalog', 'view');
    }
    
    public function content_catalog_list($url, $action)
    {
        $content_catalogs = $this->seo_model->fetch_all_content_catalogs();
        $data = array(
                'content_catalogs'    => $content_catalogs,
                'action'              => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }


    public function drop_content_catalog()
    {
        $id = $this->input->post('id');
        $this->seo_model->drop_content_catalog($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verify_content_catalog()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $content_catalog = $this->seo_model->fetch_content_catalog($id);
        try
        {
            switch ($type)
            {
                case 'integral' :
                    if ( ! is_numeric($value) ||  $value < 0)
                        {
                            echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                            return;
                        }
                    break;
                case 'name':
                    if ($this->seo_model->check_exists('seo_content_catalog', array('name' => $value)) && $value != $content_catalog->name )
                    {
                       echo $this->create_json(0, lang('content_catalog_exists'),  $content_catalog->name);
                       return;
                    }
                    break;
            }

            $user_name = get_current_user_name();
            $this->seo_model->verify_content_catalog($id, $type, $value, $user_name);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function batch_add_edit_content_integral()
    {
        $content_count = $this->input->post('content_count');        
        $type = $this->input->post('type');
        $user_id = get_current_user_id();
        for ($i = 0; $i < $content_count; $i++) 
        {
            $content_id = $this->input->post('content_id_' . $i);
            $content = $this->seo_model->fetch_content_integral($content_id);
            $integral = trim($this->input->post('integral_' . $i));
            if ($integral < 0)
            {
                continue;
            }
            try
            {
                $data = array(
                    'content_id'        => $content_id,
                    'integral'          => $integral,
                    'user_id'           => $content->owner_id,
                    'type'              => $type,
                    'reviewer_id'       => $user_id,
                );
                
                if($this->seo_model->check_exists('user_integral', array('content_id' => $content_id, 'type' => $type)))
                {
                    $this->seo_model->verify_integral($content_id, $data, $type);
                }
                else
                {
                    $this->seo_model->add_integral($data);
                }
                
                $this->verify_content_review($content_id);
                
            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function add_content_catalog()
    {
        $user_name = get_current_user_name();
        $data = array(
            'name'            => '[edit]',
            'creator'         => $user_name,
        );
        try
        {
            $this->seo_model->add_content_catalog($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function content_integral()
    {
        $this->enable_search('content_integral');
        $this->enable_sort('content_integral');

        $contents = $this->seo_model->fetch_all_content_pending_review();
        $data = array(
                'contents'    => $contents,
        );
        $this->template->write_view('content','seo/content_edit/content_review', $data);
        $this->template->render();
    }

    public function drop_content_review()
    {       
        $id = $this->input->post('id');
        $data = array(
            'integral_state'    => '-1'
        );
        $this->seo_model->drop_content_review($id, $data);
        echo $this->create_json(1, lang('configuration_accepted'));
        
    }

    public function add_edit_content_integral($integral_type)
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $content = $this->seo_model->fetch_content_integral($id);
        try
        {
            switch ($type)
            {
                case 'integral' :
                    if ( ! is_numeric($value) ||  $value < 0)
                    {
                        echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                        return;
                    }
                    break;
            }
            $reviewer_id = get_current_user_id();
            $data = array(
                'user_id'           => $content->owner_id,
                'type'              => $integral_type,
                'content_id'        => $id,
                'integral'          => $value,
                'reviewer_id'       => $reviewer_id,
            );
            if($this->seo_model->check_exists('user_integral', array('content_id' => $id, 'type' => $integral_type)))
            {               
                $this->seo_model->verify_integral($id, $data, $integral_type);
            }
            else
            {
                $this->seo_model->add_integral($data);
            }
            $this->verify_content_review($id);           
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function verify_content_review($id)
    {      
        $data = array(
            'integral_state'    => '1'
        );
        $this->seo_model->drop_content_review($id, $data);      
    }

    public function content_type()
    {
        $this->content_type_list('seo/content_edit/content_type', 'edit');
    }

    public function view_content_type()
    {
        $this->content_type_list('seo/content_edit/content_type', 'view');
    }

    public function content_type_list($url, $action)
    {
        $content_types = $this->seo_model->fetch_all_content_types();
        $data = array(
                'content_types'    => $content_types,
                'action'              => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function verify_content_type()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $content_catalog = $this->seo_model->fetch_content_type($id);
        try
        {
            switch ($type)
            {
                case 'integral' :
                    if ( ! is_numeric($value) ||  $value < 0)
                        {
                            echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                            return;
                        }
                    break;
                case 'name':
                    if ($this->seo_model->check_exists('seo_content_type', array('name' => $value)) && $value != $content_catalog->name )
                    {
                       echo $this->create_json(0, lang('content_catalog_exists'),  $content_catalog->name);
                       return;
                    }
                    break;
            }

            $user_name = get_current_user_name();
            $this->seo_model->verify_content_type($id, $type, $value, $user_name);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_content_type()
    {
        $user_name = get_current_user_name();
        $data = array(
            'name'            => '[edit]',
            'creator'         => $user_name,
        );
        try
        {
            $this->seo_model->add_content_type($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_content_type()
    {
        $id = $this->input->post('id');
        $this->seo_model->drop_content_type($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }
    

    
}

?>
