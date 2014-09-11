<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Shipping_company extends Shipping
{
    private $subareas = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('shipping_company_model');
        $this->load->model('shipping_function_model');
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_subarea_group_model');
        $this->load->model('shipping_type_model');
        $this->load->helper('shipping_helper');
    }

    public function add_edit($id = NULL)
    {
        //Get all types .
        $this->load->model('shipping_type_model');
        $types= $this->shipping_type_model->fetch_all_types_no_limit();

        $type_all = array() ;
        foreach($types as $type )
        {
            $type_all[$type->id] = $type->type_name ;
        }

        //Get possession types .
        $possession_type = NULL;

        if ($id)
        {
            $company = $this->shipping_company_model->fetch_company($id);
            $possession_type = $this->shipping_company_model->fetch_current_company_type($id);
            $data = array(
                'company'               => $company,
                'type_all'              => $type_all,
                'possession_type'       => $possession_type,


            );

            $this->template->write_view('content', 'shipping/company_edit',$data);
            $this->template->render();

            return ;
        }
        else
        {
            $data = array(
                'type_all'              => $type_all,
            );

            $this->template->write_view('content', 'shipping/company_add', $data);
            $this->template->render();
        }
    }

    public function manage()
    {
        $this->enable_search('shipping_company');
        $this->render_list('shipping/company_management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('shipping_company');
        $this->render_list('shipping/company_management', 'view');
    }

    public function view($id)
    {

        //Get all types .
        $this->load->model('shipping_type_model');
        $types= $this->shipping_type_model->fetch_all_types_no_limit();

        $type_all = array() ;
        foreach($types as $type )
        {
            $type_all[$type->id] = $type->type_name ;
        }

        $possession_type = $this->shipping_company_model->fetch_company_type($id);

        $company = $this->shipping_company_model->fetch_company($id);
        $data = array(
            'company'                   => $company,
            'action'                    => 'view',
            'type_all'                  => $type_all,
            'possession_type'           => $possession_type,
            'subarea_group_all'         => $subarea_group_all,
        );
        $this->template->write_view('content', 'shipping/company_edit', $data);
        $this->template->render();
    }

    public function save_new()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'name',
                'label' => 'company name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'telephone',
                'label' => 'telephone',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'contact_person',
                'label' => 'contact person',
                'rules' => 'trim|required',
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if ($this->shipping_company_model->check_exists('shipping_company', array('name' => $this->input->post('name'))))
        {
            echo $this->create_json(0, lang('company_name_exists'));
            return;
        }

        $data = array(
            'company_id'                => $this->input->post('company_id'),
            'name'                      => trim($this->input->post('name')),
            'telephone'                 => trim($this->input->post('telephone')),
            'contact_person'            => trim($this->input->post('contact_person')),
            'remark'                    => trim($this->input->post('remark')),
        );

        try
        {
            //Save company info .
            $this->shipping_company_model->save_company($data);

            echo $this->create_json(1, lang('shipping_company_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function save_edit()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'name',
                'label' => 'company name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'telephone',
                'label' => 'telephone',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'contact_person',
                'label' => 'contact person',
                'rules' => 'trim|required',
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if (trim($this->input->post('name')) !== $this->shipping_company_model->get_one('shipping_company', 'name', array('id' => $this->input->post('company_id'))))
        {
            if ($this->shipping_company_model->check_exists('shipping_company', array('name' => $this->input->post('name'))))
            {
                echo $this->create_json(0, lang('company_name_exists'));
                return;
            }
        }

        $data = array(
            'company_id'                => $this->input->post('company_id'),
            'name'                      => trim($this->input->post('name')),
            'telephone'                 => trim($this->input->post('telephone')),
            'contact_person'            => trim($this->input->post('contact_person')),
            'remark'                    => trim($this->input->post('remark')),
        );

        try
        {
            $company_id = $this->shipping_company_model->save_company($data);

            $type_ids = $this->input->post('type');
            if (empty($type_ids))
            {
                $type_ids = array();
            }
            $this->shipping_company_model->save_company_types($company_id, $type_ids);

            echo $this->create_json(1, lang('shipping_company_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_company()
    {
        try
        {
            $company_id = $this->input->post('id');
            $this->shipping_company_model->drop_company($company_id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function set_rule($company_id, $type_id)
    {
        $subarea_group_id = $this->shipping_type_model->get_group_id($type_id);
        if (isset($this->subareas[$subarea_group_id]))
        {
            $subareas = $this->subareas[$subarea_group_id];
        }
        else
        {
            $subareas = $this->shipping_subarea_model->fetch_subareas_by_group_id($subarea_group_id);
            //uasort($subareas, array($this, 'compare_by_subarea_name'));
            $this->subareas[$subarea_group_id] = $subareas;
        }
        $company = $this->shipping_company_model->fetch_company($company_id);
        $shipping_type = $this->shipping_type_model->fetch_type($type_id);
        $subarea_group = $this->shipping_subarea_group_model->fetch_subarea_group($subarea_group_id);
        $company_type_id = $this->shipping_company_model->fetch_company_type_id($company_id, $type_id);
        $all_weights = $this->shipping_function_model->fetch_all_weights_by_company($company_type_id);

        if ($company_type_id === NULL)
        {
            die(lang('go_back_and_save_type_first'));
        }
        $global_rule = $this->shipping_function_model->fetch_global_rule($company_type_id);
        
        $data = array(
            'subareas'          => $subareas,
            'company'           => $company,
            'shipping_type'     => $shipping_type,
            'subarea_group'     => $subarea_group,
            'all_weights'       => $all_weights,
            'company_type_id'   => $company_type_id,
            'global_rule'       => $global_rule,
        );

        $this->template->write_view('content', 'shipping/set_rules', $data);
        $this->template->render();
    }

    public function add_rule()
    {
        $company_type_id = $this->input->post('company_type_id');
        $subarea_group_id = $this->input->post('subarea_group_id');
        $start_weight = -0.02;
        $end_weight = -0.01;
        $check_where = array(
            'start_weight'      => $start_weight,
            'company_type_id'   => $company_type_id
        );
        if ($this->shipping_function_model->check_exists($check_where))
        {
            return;
        }
        $check_where = array(
            'end_weight'      => $end_weight,
            'company_type_id'   => $company_type_id
        );
        if ($this->shipping_function_model->check_exists($check_where))
        {
            return;
        }
        if (isset($this->subareas[$subarea_group_id]))
        {
            $subareas = $this->subareas[$subarea_group_id];
        }
        else
        {
            $subareas = $this->shipping_subarea_model->fetch_subareas_by_group_id($subarea_group_id);
            uasort($subareas, array($this, 'compare_by_subarea_name'));
            $this->subareas[$subarea_group_id] = $subareas;
        }
        $data = array(
            'start_weight'      => $start_weight,
            'end_weight'        => $end_weight,
            'company_type_id'   => $company_type_id,
            'rule'              => '',
            'weight_rule'       => '',
        );
        try
        {
            foreach ($subareas as $sub)
            {
                $data['subarea_id'] = $sub->id;
                $this->shipping_function_model->add_new_rule($data);
            }
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function save_rule()
    {
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $rule_id = $this->input->post('rule_id');
        if ($value === '')
        {
            echo $this->create_json(0, lang('value_should_not_be_empty'), '[edit]');
            return;
        }
        
        try
        {
            switch ($type)
            {
                case 'subarea':
                    $subarea_id = $this->input->post('subarea_id'); 
                    $where = array('id' => $rule_id);
                    try 
                    {

                        $l = $k = $h = $q = $p = $w = $weight = $price = 1;
                        eval("\$price = $value;");
                    } 
                    catch (Exception $e)
                    {
                        return;
                    }
                    $this->shipping_function_model->save_rule($where, array('rule' => $value));
                    break;
                case 'subarea_meaning':
                    $subarea_id = $this->input->post('subarea_id');
                    $where = array('id' => $rule_id);
                    $this->shipping_function_model->save_rule($where, array('rule_meaning' => $value));
                    break;
                case 'start_weight':
                    $start_weight = $this->input->post('start_weight');
                    $company_type_id = $this->input->post('company_type_id');
                    $where = array(
                        'start_weight'      => $start_weight,
                        'company_type_id'   => $company_type_id,
                    );
                    if ($this->shipping_function_model->check_exists(array('start_weight' => $value, 'company_type_id' => $company_type_id)))
                    {
                        echo $this->create_json(0, lang('start_weight_exists'), $value);
                        return;
                    }
                    $this->shipping_function_model->save_rule($where, array('start_weight' => $value));
                    break;
                case 'end_weight':
                    $end_weight = $this->input->post('end_weight');
                    $company_type_id = $this->input->post('company_type_id');
                    $where = array(
                        'end_weight'      => $end_weight,
                        'company_type_id'   => $company_type_id,
                    );
                    if ($this->shipping_function_model->check_exists(array('end_weight' => $value, 'company_type_id' => $company_type_id)))
                    {
                        echo $this->create_json(0, lang('end_weight_exists'), $value);
                        return;
                    }
                    $this->shipping_function_model->save_rule($where, array('end_weight' => $value));
                    break;
                case 'global_rule':
                    try 
                    {
                        $l = $k = $h = $q = $p = $w = $weight = $price = 1;
                        eval("\$price = $value;");
                    } 
                    catch (Exception $e)
                    {
                        return;
                    }
                    $company_type_id = $this->input->post('company_type_id');
                    $this->shipping_function_model->save_global_rule($value, $company_type_id);
                    break;
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_rule()
    {
        $start_weight = $this->input->post('start_weight');
        $end_weight = $this->input->post('end_weight');
        $company_type_id = $this->input->post('company_type_id');
        try
        {
            $this->shipping_function_model->drop_rule($start_weight, $end_weight, $company_type_id);
            echo $this->create_json(1, lang('ok'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function calculate_shipping_price()
    {
        $country = $this->input->post('country');
        $weight = $this->input->post('weight');
        $accepted_result = shipping_accepted_result($country, $weight);
        
        $data = array(
            'accepted_result'   => $accepted_result,
            'country'           => $country,
            'weight'            => $weight,
        );

        $this->template->write_view('content', 'shipping/calculate_shipping_price', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    protected function push_rules(&$rules, $cond)
    {
        $field = $this->input->post($cond['field']);

        if ($field !== FALSE)
        {
            $rules[] = $cond;
        }
    }

    private function compare_by_subarea_name($a, $b)
    {
        return (intval($a->subarea_name) < intval($b->subarea_name)) ? -1 : 1;
    }

    private function render_list($url, $action)
    {
        $companys = $this->shipping_company_model->fetch_all_company();

        $data = array(
            'companys'  => $companys,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
