<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Resource extends Seo
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('seo_model');
        $this->load->model('seo_service_company_model');
        $this->load->library('form_validation');
        $this->load->helper('validation_helper');
        $this->load->library('excel');
    }
    public function add_edit($id = NULL)
    {
        $categories = $this->seo_model->fetch_all_resource_categories();
        $seo_users = $this->user_model->fetch_all_seo_users();

        $resource_companys = $this->seo_service_company_model->fetch_all_service_companys();       
        $resource = NULL;
        $permissions = NULL;       
        $company_permissions = NULL;
        if ($id)
        {
            $resource = $this->seo_model->fetch_resource($id);
            $permissions = $this->seo_model->fetch_resource_permissions($id);
            $company_permissions = $this->seo_service_company_model->fetch_recource_company_permissions($id);
        }

        $data = array(
            'categories'                     => $categories,
            'resource'                       => $resource,
            'seo_users'                      => $seo_users,
            'permissions'                    => $permissions,
            'resource_companys'              => $resource_companys,
            'company_permissions'            => $company_permissions,
        );
        $this->template->write_view('content', 'seo/resource/add_edit', $data);
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('seo_resource');
        $this->enable_sort('seo_resource');

        $this->render_list('seo/resource/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('seo_resource');
        $this->enable_sort('seo_resource');

        $this->render_list('seo/resource/management', 'view');
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
        $resource = $this->seo_model->fetch_resource($id);
        $permissions = $this->seo_model->fetch_resource_permissions($id);
        $seo_users = $this->user_model->fetch_all_seo_users();
        
        $data = array(
            'resource'   => $resource,
            'permissions'=> $permissions,
            'seo_users'  => $seo_users,
            'popup'      => $popup,
        );
        $this->template->write_view('content', 'seo/resource/view_detail', $data);
        $this->template->render();
    }

    public function add_save()
    {
        $rules = array(
            array(
                'field' => 'url',
                'label' => 'resource url',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'root_pr',
                'label' => 'root pr',
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'current_pr',
                'label' => 'current pr',
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'language',
                'label' => 'language',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'can_post_message',
                'label' => 'post message',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'do_follow',
                'label' => 'do follow',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'export_links',
                'label' => 'export links',
                'rules' => 'trim|is_natural',
            ),
            array(
                'field' => 'category',
                'label' => 'category',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'username',
                'label' => 'username',
                'rules' => 'trim',
            ),
            array(
                'field' => 'password',
                'label' => 'password',
                'rules' => 'trim',
            ),
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|valid_email',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        // check if resource url exists ?
        if ($this->input->post('resource_id') < 0 && $this->seo_model->check_exists('seo_resource', array('url' => $this->input->post('url'))))
        {
            echo $this->create_json(0, lang('resource_url_exists'));

            return;
        }

        $category_id = $this->input->post('category');
        $release_left = $this->seo_model->get_release_left_by_category($category_id);
//        $release_wholelife = $this->seo_model->get_release_wholelife_by_category($category_id);
        $data = array(
            'resource_id'       => $this->input->post('resource_id'),
            'url'               => trim($this->input->post('url')),
            'root_pr'           => trim($this->input->post('root_pr')),
            'current_pr'        => trim($this->input->post('current_pr')),
            'language'          => $this->input->post('language'),
            'can_post_message'  => $this->input->post('can_post_message'),
            'do_follow'         => $this->input->post('do_follow'),
            'export_links'      => trim($this->input->post('export_links')),
            'category'          => $category_id,
            'release_left'      => $release_left,
//            'release_left_wholelife' => $release_wholelife
            'username'          => trim($this->input->post('username')),
            'password'          => trim($this->input->post('password')),
            'email'             => trim($this->input->post('email')),
            'note'              => $this->input->post('note', TRUE),
        );
        
        $account = $this->account->get_account();
        $data['owner_id'] = $account['id'];

        try
        {
            $resource_id = $this->seo_model->save_resource($data);
            $user_ids = $this->input->post('permissions') ;
            if (empty($user_ids))
            {
                $user_ids = array();
            }
            $this->seo_model->save_resource_permissions($resource_id, $user_ids);

            $company_ids = $this->input->post('company_permissions') ;
            if (empty($company_ids))
            {
                $company_ids = array();
            }
            $this->seo_service_company_model->save_resource_company_permissions($resource_id, $company_ids);
       
            echo $this->create_json(1, lang('resource_saved'));
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
                'field' => 'url',
                'label' => 'resource url',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'root_pr',
                'label' => 'root pr',
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'current_pr',
                'label' => 'current pr',
                'rules' => 'trim|required|is_natural',
            ),
            array(
                'field' => 'language',
                'label' => 'language',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'can_post_message',
                'label' => 'post message',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'do_follow',
                'label' => 'do follow',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'export_links',
                'label' => 'export links',
                'rules' => 'trim|is_natural',
            ),
            array(
                'field' => 'category',
                'label' => 'category',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'username',
                'label' => 'username',
                'rules' => 'trim',
            ),
            array(
                'field' => 'password',
                'label' => 'password',
                'rules' => 'trim',
            ),
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|valid_email',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        // check if resource url exists ?
        if ($this->input->post('resource_id') < 0 &&
            $this->seo_model->check_exists('seo_resource', array('url' => $this->input->post('url'))))
        {
            echo $this->create_json(0, lang('resource_url_exists'));

            return;
        }

        $category_id = $this->input->post('category');
        $release_left = $this->seo_model->get_release_left_by_category($category_id);

        $data = array(
            'resource_id'       => $this->input->post('resource_id'),
            'url'               => trim($this->input->post('url')),
            'root_pr'           => trim($this->input->post('root_pr')),
            'current_pr'        => trim($this->input->post('current_pr')),
            'language'          => $this->input->post('language'),
            'can_post_message'  => $this->input->post('can_post_message'),
            'do_follow'         => $this->input->post('do_follow'),
            'export_links'      => trim($this->input->post('export_links')),
            'category'          => $this->input->post('category'),
            'username'          => trim($this->input->post('username')),
            'password'          => trim($this->input->post('password')),
            'email'             => trim($this->input->post('email')),
            'note'              => $this->input->post('note', TRUE),
        );

        $account = $this->account->get_account();
        $data['owner_id'] = $account['id'];

        try
        {
            $resource_id = $this->seo_model->save_resource($data);
            $user_ids = $this->input->post('permissions') ;
            if (empty($user_ids))
            {
                $user_ids = array();
            }
            $this->seo_model->save_resource_permissions($resource_id, $user_ids);

            $company_ids = $this->input->post('company_permissions') ;
            if (empty($company_ids))
            {
                $company_ids = array();
            }
            $this->seo_service_company_model->save_resource_company_permissions($resource_id, $company_ids);
            $integral = trim($this->input->post('integral'));
            if($resource_id > 0)
            {
                $resource = $this->seo_model->fetch_resource_integral($resource_id);
                $reviewer_id = get_current_user_id();
                $data = array(
                    'user_id'           => $resource->owner_id,
                     'type'             => 'resource',
                    'content_id'        => $resource_id,
                    'integral'          => $integral,
                    'reviewer_id'       => $reviewer_id,
                );
                if($this->seo_model->check_exists('user_integral', array('content_id' => $resource_id, 'type' => 'resource')))
                {
                    $this->seo_model->verify_integral($resource_id, $data, 'resource');
                }
                else
                {
                    $this->seo_model->add_integral($data);
                }
                $this->verify_resource_review($resource_id);
            }

            echo $this->create_json(1, lang('resource_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_resource()
    {
        $resource_id = $this->input->post('id');
        $this->seo_model->drop_resource($resource_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $categorys = $this->seo_model->fetch_all_resource_category();

        $options = array(''=>lang('all'));

        foreach ($categorys as $category)
        {
            $options[$category->id] = $category->name;
        }

        $resources = $this->seo_model->fetch_all_resources();

        $data = array(
            'options'   => $options,
            'resources' => $resources,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function resource_catalog()
    {
        $this->resource_catalog_list('seo/resource/resource_catalog', 'edit');
    }

    public function view_resource_catalog()
    {
        $this->resource_catalog_list('seo/resource/resource_catalog', 'view');
    }

    public function resource_catalog_list($url, $action)
    {
        $resource_categories = $this->seo_model->fetch_all_resource_categories();
        $data = array(
                'resource_categories'    => $resource_categories,
                'action'                 => $action,
        );
        $this->template->write_view('content',$url, $data);
        $this->template->render();
    }

    public function drop_resource_category()
    {
        $id = $this->input->post('id');
        $this->seo_model->drop_resource_category($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verify_resource_category()
    {       
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        
        $resource_category = $this->seo_model->fetch_resource_category($id);    
        try
        {
            switch ($type)
            {
                case 'integral' :
                    if ( ! is_natural_no_zero($value))
                        {
                            echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                            return;
                        }
                    break;

                  case 'release_limit':
                      if( ! preg_match("/^[1-9][0-9]*$/",$value))
                      {
                          echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                          return;
                       }
                       break;

                case 'name':
                    if ($this->seo_model->check_exists('seo_resource_category', array('name' => $value)) && $value != $resource_category->name )
                    {
                       echo $this->create_json(0, lang('resource_category_exists'),  $resource_category->name);
                       return;
                    }
                     break;       
            }
                      
            $user_name = get_current_user_name();       
            $this->seo_model->verify_resource_category($id, $type, $value, $user_name);             
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }



    public function add_resource_category()
    {
        $user_name = get_current_user_name();
        $data = array(           
            'name'            => '[edit]',                 
            'creator'         => $user_name,
        );
        try
        {
            $this->seo_model->add_resource_category($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function resource_integral()
    {
        $this->enable_search('resource_integral');
        $this->enable_sort('resource_integral');

        $resources = $this->seo_model->fetch_all_resource_pending_review();
        $data = array(
                'resources'    => $resources,
        );
        $this->template->write_view('content','seo/resource/resource_review', $data);
        $this->template->render();
    }

    public function drop_resource_review()
    {
        $id = $this->input->post('id');
        $data = array(
            'integral_state'    => '-1'
        );
        $this->seo_model->drop_resource_review($id, $data);
        echo $this->create_json(1, lang('configuration_accepted'));

    }

    public function add_edit_resource_integral($integral_type)
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $resource = $this->seo_model->fetch_resource_integral($id);
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
                'user_id'           => $resource->owner_id,
                 'type'             => $integral_type,
                'content_id'        => $id,
                'integral'          => $value,
                'reviewer_id'       => $reviewer_id,
            );
            if($this->seo_model->check_exists('user_integral', array('content_id' => $id, 'type' => $integral_type )))
            {
                $this->seo_model->verify_integral($id, $data, $integral_type);
            }
            else
            {
                $this->seo_model->add_integral($data);
            }
            $this->verify_resource_review($id);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function batch_add_edit_resource_integral()
    {
        $resource_count = $this->input->post('resource_count');
        $type = $this->input->post('type');
        $user_id = get_current_user_id();
        for ($i = 0; $i < $resource_count; $i++)
        {
            $resource_id = $this->input->post('resource_id_' . $i);
            $resource = $this->seo_model->fetch_resource_integral($resource_id);
            $integral = trim($this->input->post('integral_' . $i));
            if ($integral < 0)
            {
                continue;
            }
            try
            {
                $data = array(
                    'content_id'        => $resource_id,
                    'integral'          => $integral,
                    'user_id'           => $resource->owner_id,
                    'type'              => $type,
                    'reviewer_id'       => $user_id,
                );

                if($this->seo_model->check_exists('user_integral', array('content_id' => $resource_id, 'type' => $type)))
                {
                    $this->seo_model->verify_integral($resource_id, $data, $type);
                }
                else
                {
                    $this->seo_model->add_integral($data);
                }

                $this->verify_resource_review($resource_id);

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function verify_resource_review($id)
    {
        $data = array(
            'integral_state'    => '1'
        );
        $this->seo_model->drop_resource_review($id, $data);       
    }

    function check_int($num)
    {
      if (($num>0)&&is_int($num))
         return true;
      else
      return false;
   }

   public function csv_upload()
    {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'seo/resource/csv_upload', $data);
        $this->template->render();
    }
    
    function do_upload()
    {
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '100';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('seo/resource/csv_upload', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            unset ($before_file_arr[0]);

            $sueecss_counts = 0;
            $failure_counts = 0;
            $number = 1;
            $output_data = array();

            $company_ids = array();
            $resource_permission_ids = array();
            $category_id = '';

            foreach ($before_file_arr as $row)
            {
                $output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();

                /*
                 *service company。
                 * **/
                if($row[0])
                {
                    $company_ids_temp = array();
                    
                    $company_names = explode(',', $row[0]);

                    foreach ($company_names as $name)
                    {
                        $company_ids_temp[] = $this->seo_model->get_one('seo_service_company', 'id', array('name' => $name));
                    }

                    $company_ids = $company_ids_temp;
                }

                /**
                 * URL
                 * **/
                if ($row[1])
                {
                    if($this->seo_model->check_exists('seo_resource', array('url' => $row[1])))
                    {
                        $output_data["$number"] .= lang('resource_url_exists').'<br><br>';
                        $failure_counts++;
                        $number++;
                        continue;
                    }
                    else
                    {
                        $data['url'] = $row[1];
                    }

                }
                else
                {
                    $output_data["$number"] .= lang('resource_url_is_null').'<br><br>';
                    $failure_counts++;
                    $number++;
                    continue;
                }

                /**
                 * Root PR
                 * **/
                if($row[2])
                {
                    $data['root_pr'] = $row[2];
                }
                else
                {
                    $output_data["$number"] .= lang('root_pr_is_null').'<br><br>';
                    $failure_counts++;
                    $number++;
                    continue;
                }

                if ($row[3])
                {
                    $data['current_pr'] = $row[3];
                }
                else
                {
                    $output_data["$number"] .= lang('current_pr_is_null').'<br><br>';
                    $failure_counts++;
                    $number++;
                    continue;
                }


                /**
                 * Language.
                 * **/
                if($row[4])
                {
                    $data['language'] = $row[4];
                }

                /**
                 * Can post message?
                 * **/
                if($row[5])
                {
                    $data['can_post_message'] = $row[5];
                }

                /**
                 * Do follow/No follow?
                 * **/
                if($row[6])
                {
                    $data['do_follow'] = $row[6];
                }

                /**
                 * Export links.
                 * **/
                if($row[7])
                {
                    $data['export_links'] = $row[7];
                }

                /**
                 * Category.
                 * **/
                if($row[8])
                {
                    $category_id = $this->seo_model->get_one('seo_resource_category', 'id', array('name' => $row[8]));
                    $data['category'] = $category_id;
                }

                /**
                 * username.
                 * **/
                if($row[9])
                {
                    $data['username'] = $row[9];
                }

                /**
                 * password.
                 * **/
                if($row[10])
                {
                    $data['password'] = $row[10];
                }

                /**
                 * email.
                 * **/
                if($row[11])
                {
                    $data['email'] = $row[11];
                }

                /**
                 * resource permission.
                 * **/
                if($row[12])
                {
                    $resource_permission_ids_temp = array();

                    $resource_permission_names = explode(',', $row[12]);

                    foreach ($resource_permission_names as $name)
                    {
                        $resource_permission_ids_temp[] = $this->seo_model->get_one('user', 'id', array('login_name' => $name));
                    }

                    $resource_permission_ids = $resource_permission_ids_temp;
                }

                /**
                 * 备注.
                 * **/
                if($row[13])
                {
                    $data['note'] = $row[13];
                }
                
                /**
                 * 积分审核.
                 * **/
                if($row[13])
                {
                    $data['note'] = $row[13];
                }

                $data['owner_id'] = get_current_user_id();

                $resource_id = $this->seo_model->create_resource($data);

                if (empty($resource_permission_ids))
                {
                    $resource_permission_ids = array();
                }
                $this->seo_model->save_resource_permissions($resource_id, $resource_permission_ids);

                if (empty($company_ids))
                {
                    $company_ids = array();
                }
                $this->seo_service_company_model->save_resource_company_permissions($resource_id, $company_ids);


                if($resource_id)
                {
                    $output_data["$number"] .= lang('success').'<br><br>';
                    $sueecss_counts++;
                    $number++;
                }
            }

            $number--;
            $output_data["total"] = '<br><br>'.sprintf(lang('total_count_result'), $number, $sueecss_counts, $failure_counts);

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'seo/resource/success', $data_page);
            $this->template->render();
        }
    }

    public function download_model()
    {
        $head = array(
            "service company *",
            "URL *",
            "Root PR *",
            "Current PR *",
            "Language *",
            "Can post message? *",
            "Do follow/No follow? *",
            "Export links",
            "Category *",
            "username",
            "password",
            "email",
            "resource permission *",
            "remark",
        );

        $data[] = array(
            'mallerp',
            'http://mallerp.com',
            '8',
            '6',
            'cn',
            'Yes',
            'Do follow',
            '1',
            'comment',
            'username',
            'password',
            'john@mallerp.com',
            '191,193',
            'remark',
        );
        

        $this->excel->array_to_excel($data, $head, 'resource_model' . date('Y-m-d'));
    }


}

?>
