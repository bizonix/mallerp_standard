<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Release extends Seo
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('seo_model');
        $this->load->model('seo_service_company_model');
        $this->load->helper('seo');
        $this->load->library('form_validation');
        $this->load->library('excel');
        $this->template->add_js('static/js/sorttable.js');
    }
    public function manage($id = false)
    {
        $this->enable_search('seo_content');
        $this->enable_sort('seo_content');

        $contents = $this->seo_model->fetch_all_contents();
        $resources = $this->seo_model->fetch_all_resources_by_user($this->get_current_user_id());
        $data = array(
            'contents'  => $contents,
            'resources' => $resources,
            'selected_id' => $id,
        );
        
        $this->template->write_view('content', 'seo/release/management', $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function save_verifying()
    {
        try
        {
            $resource_id = $this->input->post('resource_id');
            $content_id = $this->input->post('content_id');
            $tag = $this->input->post('tag');
            $url = strtolower(trim($this->input->post('validate_url')));
            $url = strpos($url, 'http://') === 0 ? $url : 'http://' . $url;

            if ($tag == 'save' && empty($url))
            {
                echo $this->create_json(0, lang('varification_url_is_required'));
                return;
            }
            if ($this->seo_model->check_verify_url_exists($content_id, $url))
            {
                echo $this->create_json(0, lang('varification_url_exists'));
                return;
            }

            $owner_id = $this->get_current_user_id();
            $data =array(
                'owner_id'    => $owner_id,
                'content_id'  => $content_id,
                'resource_id' => $resource_id,
            );
            $release_id = $this->seo_model->add_seo_release($data);

            if ($tag == 'save')
            {
                $data =array(
                    'validate_url'      => $url,
                    'status'            => 0,
                );

                $this->seo_model->update_release($release_id, $data);

                // seo release verification
                $this->events->trigger(
                    'submit_seo_release_after',
                    array(
                        'release_id' => $release_id,
                    )
                );
            }

            if ($tag == 'drop')
            {
                $data =array(
                    'status'            => -2,
                );

                $this->seo_model->update_release($release_id, $data);
            }

            echo $this->create_json(1, lang('release_saved_into_veifying_list'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        
        
    }

    public function search_content()
    {
        $content_catalogs = $this->seo_model->fetch_all_content_types();
        $resource_categories = $this->seo_model->fetch_all_resource_categories();
        $data = array(
            'content_catalogs'          => $content_catalogs,
            'resource_categories'       => $resource_categories,
        );
        $this->template->write_view('content', 'seo/content_edit/search_content', $data);
        $this->template->render();
    }

    public function resource_categoried_search()
    {
        $content_catalog_id = $this->input->post('content_catalog_id');
        $resource_category_id = $this->input->post('resource_category_id');
        $data = array(
            'content_catalog_id'    => $content_catalog_id,
            'resource_category_id'  => $resource_category_id,
        );
        if($this->seo_model->check_exists('seo_content_resource_category_map', $data))
        {
            $this->seo_model->drop_content_resource_map($data);
        }
        else
        {
            $this->seo_model->add_content_resource_map($data);
        }
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function integral_info()
    {
        $this->enable_search('seo_integral');
        $this->enable_sort('seo_integral');

        $user_id = get_current_user_id();
        $priority = $this->user_model->fetch_user_priority_by_system_code('seo');
        $integrals =  $this->seo_model->fetch_user_integral_info($user_id, $priority);
        $data = array(
            'integrals'  => $integrals,
        );
        $this->template->write_view('content', 'seo/integral_info', $data);
        $this->template->render();
    }

    public function integral_statistics()
    {
        if ( ! $this->input->is_post())
        {           
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {          
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }
        $seo_users = $this->user_model->fetch_users_by_system_code('seo');
        $users_integral = array();
        foreach($seo_users as $seo_user)
        {
           $user_integral = $this->seo_model->fetch_user_all_integrals_by_date($seo_user->u_id, $begin_time, $end_time);
           $users_integral[$seo_user->u_id] = 0;
           foreach ($user_integral as $integral)
           {
               $users_integral[$seo_user->u_id] += isset($integral->integral) ? $integral->integral : '0';
           }           
        }       
        $data = array(            
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'seo_users'           => $seo_users,
            'users_integral'      => $users_integral,
        );
        $this->template->write_view('content', 'seo/integral_statistics', $data);
        $this->template->render();
    }

    public function personal_released_management()
    {
        $this->enable_search('seo_release');
        $this->enable_sort('seo_release');
        $release_resources = $this->seo_model->fetch_all_release_resources($tag = 'personal');
        $data = array(
            'release_resources'        => $release_resources,
            'tag'                      => $tag,
        );
        $this->template->write_view('content', 'seo/release/released_management', $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function department_released_management()
    {
        $this->enable_search('seo_release');
        $this->enable_sort('seo_release');
        $release_resources = $this->seo_model->fetch_all_release_resources($tag = 'department');
        $data = array(
            'release_resources'        => $release_resources,
            'tag'                      => $tag,
        );
        $this->template->write_view('content', 'seo/release/released_management', $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function render_list($url,  $action)
    {
        $release_resources = $this->seo_model->fetch_all_release_resources();
        $data = array(
            'release_resources'        => $release_resources,
            'action'                    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->add_js('static/js/ajax/seo.js');
        $this->template->render();
    }

    public function update_validate_url($id)
    {
        $release = $this->seo_model->fetch_release_by_id($id);
        $data = array(
            'release'   => $release,
        );
        $this->template->write_view('content', 'seo/release/update_url', $data);
        $this->template->render();
    

    }

    public function released_save()
    {
        $id = $this->input->post('release_id');
        $url = trim($this->input->post('validate_url'));

//        prep_url($url);

        $rules = array(
            array(
                'field' => 'validate_url',
                'label' => lang('validate_url'),
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
        

        $data = array(
            'validate_url'  => $url,
        );

        try
        {
            $release = $this->seo_model->update_release($id, $data);
            echo $this->create_json(1, 'ok');
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    
    public function released_validate($tag)
    {
        $id = $this->input->post('resource_id');

        if($tag == 'successful')
        {
            $data = array(
                'status'  => 1,
            );
        }
        else if($tag == 'failure')
        {
            $data = array(
                'status'  => -3,
            );
        }
        else
        {
            echo $this->create_json(0, 'false');
            return;
        }

        try
        {
            $release = $this->seo_model->update_release($id, $data);
            echo $this->create_json(1, 'ok');
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function verify_content_resource_catalog_integral()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));    
        try
        {
            switch ($type)
            {
                case 'integral' :
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    break;                                        
            }           
            $this->seo_model->verify_content_resource_catalog_integral($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function csv_upload()
    {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'seo/release/csv_upload', $data);
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

            $this->load->view('seo/release/csv_upload', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            
            $content_id = '';
            $user_id = '';
            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();

            foreach ($before_file_arr as $row)
            {
                $output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
                
                if(count($row) < 2)
                {
                    $output_data["$number"] .= lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                }
                else
                {
                    if ($row[0])
                    {
                        $data['content_id'] = $this->seo_model->get_one('seo_content', 'id', array('title'=>$row[0]));
                        $content_id = $data['content_id'];
                    }
                    else
                    {
                        $data['content_id'] = $content_id;
                    }

                    if( ! $data['content_id'])
                    {
                        $output_data["$number"] .= lang('failure_and_not_find_content_title_for_id');
                        $failure_counts++;
                        $number++;
                        continue;
                    }

                    if ($row[1])
                    {
                        $data['owner_id'] = $this->seo_model->get_one('user', 'id', array('login_name'=>$row[1]));
                        $user_id = $data['owner_id'];
                    }
                    else
                    {
                        $data['owner_id'] = $user_id;
                    }

                    if($row[2])
                    {
                        $data['resource_id'] = $this->seo_model->get_one('seo_resource', 'id', array('url'=>$row[2]));
                    }
                    else
                    {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }

                    if( ! $data['resource_id'])
                    {
                        $output_data["$number"] .= lang('failure_and_not_find_resource_url_for_id');
                        $failure_counts++;
                        $number++;
                        continue;
                    }

                    if($this->seo_model->check_exists('seo_release', array('content_id'=>$data['content_id'],'resource_id'=>$data['resource_id'], 'owner_id'=>$data['owner_id'] )))
                    {
                        $output_data["$number"] .= lang('failure_this_data_not_exist');
                        $failure_counts++;
                        $number++;
                        continue;
                    }
                    
                    $data['status'] = 1;
                    $data['validate_url'] = 'http://www.mallerp.com';

                    $id = $this->seo_model->add_seo_release($data);

                    if($id)
                    {
                        $output_data["$number"] .= lang('success');
                        $sueecss_counts++;
                        $number++;
                    }
                }
            }

            $number--;
            $output_data["total"] = sprintf(lang('total_count_result'), $number, $sueecss_counts, $failure_counts, $blank_counts);

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'seo/release/success', $data_page);
            $this->template->render();
        }
    }
   
}

?>
