<?php
require_once APPPATH.'controllers/myinfo/myinfo'.EXT;

class Myaccount extends Myinfo
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('role_level_model');
        $this->load->model('document_catalog_model');
        $this->load->model('solr/solr_base_model');
        $this->load->model('solr/solr_statistics_model');
        $this->load->model('seo_model');
        $this->load->model('order_model');
        $this->load->model('ebay_model');
        $this->load->model('group_model');
        $this->load->model('myinfo_model');
        $this->load->model('work_rewards_model');
        $this->load->model('mixture_model');
        $this->load->helper('seo');
        $this->load->helper('solr');
        $this->load->library('excel');
        $this->load->model('user_model');
    }

    public function view_account()
    {
        $data = array(
            'user'  => $this->user_model->fetch_user_info(get_current_login_name()),
        );
        $this->set_2column('sidebar_account');
        $this->template->write_view('content', 'myinfo/view_account', $data);
        $this->template->render();
    }

    public function proccess_update_account()
    {
        $rules = array(
            array(
                'field' => 'myname',
                'label' => lang('myname'),
                'rules' => 'trim|required|min_length[2]|max_length[20]',
            )
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $name                            = trim($this->input->post('myname'));
        $name_en                         = trim($this->input->post('name_en'));
        $phone                           = trim($this->input->post('phone'));
        $msn                             = trim($this->input->post('msn'));
        $skype                           = trim($this->input->post('skype'));
        $skype_pwd                       = trim($this->input->post('skype_pwd'));
        $msn_pwd                         = trim($this->input->post('msn_pwd'));
        $QQ                              = trim($this->input->post('QQ'));
        $QQ_pwd                          = trim($this->input->post('QQ_pwd'));
        $birthday                        = trim($this->input->post('birthday'));
        $contrct_time                    = trim($this->input->post('contrct_time'));
        $taobao_username                 = trim($this->input->post('taobao_username'));
        $taobao_pwd                      = trim($this->input->post('taobao_pwd'));
        $fileserv_username               = trim($this->input->post('fileserv_username'));
        $fileserv_pwd                    = trim($this->input->post('fileserv_pwd'));
        $RTX                             = trim($this->input->post('RTX'));
        $RTX_pwd                         = trim($this->input->post('RTX_pwd'));
        $platform1                       = trim($this->input->post('platform1'));
        $platform2                       = trim($this->input->post('platform2'));
        $email                           = $this->input->post('email');
        $email_pwd                       = $this->input->post('email_pwd');
        $trial_end_time                  = $this->input->post('trial_end_time');
        $data = array(
            'name'              => $name,
            'name_en'           => $name_en,
            'phone'             => $phone,
            'msn'               => $msn,
            'skype'             => $skype,
            'platform1'         => $platform1,
            'platform2'         => $platform2,
            'email'             => no_space($email),
            'email_pwd'         => $email_pwd,         
            'skype_pwd'         => $skype_pwd,
            'msn_pwd'           => $msn_pwd,
            'QQ'                => $QQ,
            'QQ_pwd'            => $QQ_pwd,
            'birthday'          => $birthday,
            'contrct_time'      => $contrct_time,
            'taobao_username'   => $taobao_username,
            'taobao_pwd'        => $taobao_pwd,
            'fileserv_username' => $fileserv_username,
            'fileserv_pwd'      => $fileserv_pwd,
            'RTX'               => $RTX,
            'RTX_pwd'           => $RTX_pwd,
            'trial_end_time'    => $trial_end_time,
        );

        $user_id = get_current_user_id();
        try
        {
            $this->user_model->update_user($user_id, $data);
        }
        catch (Exception $exc) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        
        echo $this->create_json(1, lang('ok'));
    }

    public function proccess_update_password()
    {
        $old_password = $this->input->post('old_password');

        $is_old_password_correct = $this->user_model->verify_user(get_current_login_name(), $old_password);

        if ( ! $is_old_password_correct)
        {
            echo $this->create_json(0, lang('old_password_incorrect'));

            return;
        }
        $rules = array(
            array(
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'trim|required|matches[confirm_password]|min_length[8]',
            ),
            array(
                'field' => 'confirm_password',
                'label' => lang('confirm_password'),
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

        $password  = md5(trim($this->input->post('password')));
        $user_id = get_current_user_id();
        try
        {
            $this->user_model->update_user($user_id, 'password', $password);
        }
        catch (Exception $exc) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function manage()
    {
        $this->enable_search('user_log');
        $this->enable_sort('user_log');

        $this->render_list('myinfo/management', 'edit');
    }
    public function staff_manage()
    {
        $this->staff_render_list('myinfo/staff_management', 'edit');
    }
    public function staff_render_list($url,$action)
    {
        $this->enable_search('user');
        $this->enable_sort('user');
        $users = $this->user_model->fetch_all_users();
        $data = array(
            'users'  => $users,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
    private function render_list($url, $action)
    {
        $user_logs = $this->mixture_model->fetch_all_user_logs();

        $data = array(
            'user_logs' => $user_logs,
            'action'    => $action,
        );
        $this->set_2column('sidebar_account');
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
    public function edit($id = NULL)
    {
       $this->edit_view_user('myinfo/edit_user','edit',$id);
    }
    private function edit_view_user($url, $action, $id)
    {
        if ($id)
        {
            $user = $this->user_model->fetch_user_by_id($id);
        }
        $data = array(
            'user'          => $user,
            'action'         => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
    public function edit_save($id)
    {
        $rules = array(
            array(
                'field' => 'name',
                'label' => 'name',
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
            'name'                 => trim($this->input->post('name')),
            'name_en'              => trim($this->input->post('name_en')),
            'QQ'                   => trim($this->input->post('QQ')),
            'QQ_pwd'               => trim($this->input->post('QQ_pwd')),
            'RTX'                  => trim($this->input->post('RTX')),
            'RTX_pwd'              => trim($this->input->post('RTX_pwd')),
            'fileserv_username'    => trim($this->input->post('fileserv_username')),
            'fileserv_pwd'         => trim($this->input->post('fileserv_pwd')),
            'email'                => trim($this->input->post('email')),
            'email_pwd'            => trim($this->input->post('email_pwd')),
            'msn'                  => trim($this->input->post('msn')),
            'msn_pwd'              => trim($this->input->post('msn_pwd')),
            'skype'                => trim($this->input->post('skype')),
            'skype_pwd'            => trim($this->input->post('skype_pwd')),
            'taobao_username'      => trim($this->input->post('taobao_username')),
            'taobao_pwd'           => trim($this->input->post('taobao_pwd')),
            'birthday'             => trim($this->input->post('birthday')),
            'contrct_time'         => trim($this->input->post('contrct_time')),
            'trial_end_time'       => trim($this->input->post('trial_end_time')),
        );
        try
        {
            $this->user_model->update_user($id, $data);
            echo $this->create_json(1, lang('user_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    public function drop_user()
    {
        $user_id = $this->input->post('id');
        $this->user_model->drop_user($user_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function add_expire_day()
    {
        $day_info = $this->user_model->fetch_expire_day_info();
        $data = array(
            'day_info' => $day_info,
        );
        $this->template->write_view('content', 'myinfo/staff_expire_add',$data);
        $this->template->render();        
    }
    public function save_expire_day()
    {
        $rules = array(
            array(
                'field' => 'contract_time',
                'label' => lang('contract_notice_day'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'probation_time',
                'label' => lang('probation_notice_day'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'birthday',
                'label' => lang('birthday_notice_day'),
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
            'contract_time'       => trim($this->input->post('contract_time')),
            'probation_time'      => trim($this->input->post('probation_time')),
            'birthday'            => trim($this->input->post('birthday')),
            'create_date'         => trim($this->input->post('create_date')),
        );
        try
        {
            $this->user_model->save_expire_day($data);
            echo $this->create_json(1, lang('expire_day_saved'));
        }
        catch (Exceprion $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    public function expire_day_manage()
    {
        $this->expire_render_list('myinfo/expire_day_management', 'edit');
    }
    public function expire_render_list($url,$action)
    {
        $this->enable_search('user');
        $this->enable_sort('user');
        $day_infos = $this->user_model->fetch_expire_day_info();
        $users = $this->user_model->fetch_expire_user_info();
        $data = array(
            'users'  => $users,
            'day_infos' =>$day_infos,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function work_rewards_error()
    {
        
        $this->enable_search('work_rewards');
        $this->enable_sort('work_rewards');

        $data = array(
            'work_rewards_errors'  => $this->work_rewards_model->fetch_all_work_rewards_error(),
            'department_arr'  => $this->work_rewards_model->fetch_department_arr(),
        );
        
        $this->template->write_view('content', 'myinfo/work_rewards_error', $data);
        $this->template->render();
    }

    public function work_rewards_error_edit($id = null)
    {
        if($id) {
            $work_id_arr = $this->work_rewards_model->fetch_worker_by_id($id);
            $worker = array();
            foreach($work_id_arr as $work_id) {
                $worker[] = fetch_user_name_by_id($work_id['worker_id']);
            }
            $worker_id = implode(',', $worker);
            $work_rewards_error = $this->work_rewards_model->fetch_work_rewards_by_id($id);
        } else {
            $worker_id = null;
            $work_rewards_error = null;
        }
        
        $parent_catalog = $this->document_catalog_model->fetch_all_department_catalog_for_make_tree();
        $parent_catalog = $this->_make_tree($parent_catalog);
        $data = array(
            'department' => $parent_catalog,
            'id'                => $id,
            'work_rewards_error'  => $work_rewards_error,
            'worker_id'             => $worker_id,
        );
        
        $this->template->write_view('content', 'myinfo/work_rewards_edit', $data);
        $this->template->render();
    }

    public function work_rewards_error_save($id = null) {

        $rules = array(
            array(
                'field' => 'type',
                'label' => lang('type'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'worker_name',
                'label' => lang('work_rewards_error_person'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'content_item',
                'label' => lang('content'),
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE) {
            $error = validation_errors();
            echo $this->create_json(0, $error);
            return;
        }

        $status_type = trim($this->input->post('result')) ? 'completed' : 'wait_for_proccess';
        $data = array(
            'type' => trim($this->input->post('type')),
            'order_no' => trim($this->input->post('order_no')),
            'content_item' => trim($this->input->post('content_item')),
            'author' => trim($this->input->post('author')),
            'department' => trim($this->input->post('department')),
            'result' => trim($this->input->post('result')),
            'status' => $status_type,
        );
        $set_type = trim($this->input->post('type'));
        if ($set_type == 'please_select') {
            echo $this->create_json(0, lang('please_select_type'));
            return;
        }
        $set_dep = trim($this->input->post('department'));
        if ($set_dep == '32') {
            echo $this->create_json(0, lang('please_select_dept'));
            return;
        }

        $worker_name = explode(",", trim($this->input->post('worker_name')));
        $worker_id = array();
        foreach ($worker_name as $name) {
            $work = $this->user_model->fetch_user_id_by_name($name);
            if (!$work) {
                echo $this->create_json(0, $name . lang('worker_not_exist'));
                return;
            }
            $worker_id[] = $work;
        }

        $id = trim($this->input->post('contend_id'));
        try {
            $this->work_rewards_model->work_rewards_error_save($data, $worker_id, $id);
            echo $this->create_json(1, lang('work_rewards_error_successed'));
        } catch (Exceprion $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_work_rewards_error() {
        try {
            $id = $this->input->post('id');
            $this->work_rewards_model->drop_work_rewards_error($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    private function _make_tree($parent_catalogs) {
        $tree = array();
        $names = array();
        foreach ($parent_catalogs as $cat) {
            $path = $cat->path;
            $names[$cat->id] = $cat->name;
            $items = explode('>', $path);
            $item_names = array();
            $space_counter = 0;

            foreach ($items as $item) {
                $name = str_replace('制度', '', element($item, $names));
                $tree[$item] = repeater('&nbsp;&nbsp;', $space_counter) . $name;
                $space_counter++;
            }
            //flat_to_multi($item_names, $tree);
        }

        return $tree;
    }

    public function csv_upload() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'myinfo/csv_upload', $data);
        $this->template->render();
    }

    function do_upload() {
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '100';
        $config['max_width'] = '1024';
        $config['max_height'] = '768';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('myinfo/csv_upload', $error);
        } else {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();
            foreach ($before_file_arr as $row) {
                $output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();

                if (count($row) < 2) {
                    $output_data["$number"] .= lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                } else {
                    if ($row[0]) {
                        $data['type'] = $row[0];
                    } else {
                        $output_data["$number"] .= lang('failure_and_not_find_content_title_for_id');
                    }

                    if ($row[1]) {
                        $this->db->select('id');
                        $this->db->from('document_catalog');
                        $this->db->like('name', $row[1]);
                        $dept_id = $this->db->get();
                        $dep = $dept_id->row();
                        $data['department'] = $dep->id;
                    } else {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }

                    if ($row[2]) {
                        if(strstr($row[2], ',')) {
                            $worker_name = explode(",", $row[2]);
                            $worker_id = array();
                            foreach ($worker_name as $name) {
                                $work = $this->work_rewards_model->fetch_user_id_by_name($name);
                                if (!$work) {
                                    continue;
                                }
                                $worker_id[] = $work;
                            }
                        } else {
                            $work_id = $row[2];
                        }
                    } else {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }

                    if ($row[3]) {
                        $data['order_no'] = $row[3];
                    }
                    if ($row[4]) {
                        $data['content_item'] = $row[4];
                    } else {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }
                    if ($row[5]) {
                        $data['result'] = $row[5];
                        $data['status'] = 'completed';
                    } else {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }
                    if ($row[6]) {
                        $data['author'] = $row[6];
                    } else {
                        $output_data["$number"] .= lang('not_write_resource_url');
                        $failure_counts++;
                        $number++;
                        continue;
                    }
                    $this->work_rewards_model->work_rewards_error_save($data, $worker_id);
                }
            }

            $number--;
            $output_data["total"] = sprintf(lang('total_count_result'), $number, $sueecss_counts, $failure_counts, $blank_counts);

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'myinfo/success', $data_page);
            $this->template->render();
        }
    }

    public function return_cost_statistic() {
        $this->set_2column('sidebar_duty_statistics');
        $result = $this->solr_statistics_model->fetch_refund_resend_by_duty('refund_duties');
        $query = $result['query'];
        $data = array(
            'query' => $query,
        );
        $this->template->write_view('content', 'myinfo/return_cost_statistic', $data);
        $this->template->render();
    }

    public function bad_comment_statistic() {
        $this->set_2column('sidebar_duty_statistics');
        $user = get_current_user_name();
        $rows = $this->ebay_model->feedback_statistics_all($user);
        $data = array(
            'rows' => $rows,
        );
        $this->template->write_view('content', 'myinfo/bad_comment_statistic', $data);
        $this->template->render();
    }

    public function important_message_management() {
        $this->enable_search('messages');
        $this->enable_sort('messages');
        
        $rows = $this->myinfo_model->fetch_all_important_messages();
        $data = array(
            'rows' => $rows,
        );
        $this->template->write_view('content', 'myinfo/important_message_view', $data);
        $this->template->add_js('static/js/ajax/myinfo.js');
        $this->template->render();
    }

    public function drop_message($id) {
        try {
            $id = $this->input->post('id');
            $this->myinfo_model->delete('important_message_group', array('id' => $id));
            echo $this->create_json(1, lang('configuration_accepted'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function checkbox_read_edit() {
        $message_id = trim($this->input->post('message_id'));
        $read = trim($this->input->post('read'));
        $group_name = trim($this->input->post('group_name'));
        try {
            $this->myinfo_model->checkbox_read_edit($message_id, $read, $group_name);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

      public function by_type()
    {
        $duty_user = NULL;
        if(!$this->input->is_post())
        {
             $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
             $end_time = date("Y-m-d H:i:s");
        }
        else
        {
             $begin_time = $this->input->post('begin_time');
             $end_time = $this->input->post('end_time');
        }

        $refund_type_obj = $this->order_model->fetch_all_bad_comment_type();
        $refund_types = array();
        foreach ($refund_type_obj as $item)
        {
            $refund_types[$item->id] = $item->type;
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
            'refund_verify_type',
            $begin_time,
            $end_time,
            $duty_user
        );
        $refunds = (array)$result['facet'];

        $query = $result['query'];
        $refund_duties = array();
        $item_no = array();
        if ( ! empty($query->docs))
        {
            foreach ($query->docs as $row)
            { 
                foreach ($row->refund_duties as $duty)
                {
                    $refund_duties[] = $duty;
                    $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                    $item_no[$the_type] = isset($item_no[$the_type]) ? $item_no[$the_type] : null;
                    $item_no[$the_type] .= lang('order_number').'：'.$row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('content').'：'.$row->refund_verify_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('refund_verify_type').'：'.$the_type.br().'----------'.br();
                }
            }
        }
        $refund_duties = array_unique($refund_duties);

        $current_dept = $this->default_system();
        $feed_types = $this->order_model->get_result('order_bad_comment_type', 'id', array('department' => $current_dept));
        $my_types = array();
        foreach($feed_types as $type)
        {
            $my_types[] = $type->id;
        }
        $feedback_obj = $this->ebay_model->feedback_statistics_count('item_no', $my_types, $begin_time, $end_time);
        $feedback_obj_count = $this->ebay_model->feedback_statistics('verify_type', $my_types, $begin_time, $end_time);
        $count = 1;
        foreach($feedback_obj_count as $feed_count)
        {
            if(empty($row_count[$feed_count->verify_type])) {
                $row_count[$feed_count->verify_type] = $count;
            } else {
                $row_count[$feed_count->verify_type] += $count;
            }
        }
        $feedbacks = array();
        $feed_feedback_content = array();
        foreach ($feedback_obj as $row)
        {
            $feedbacks[$row->verify_type] = $row_count[$row->verify_type];
            $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->verify_type));
            $feed_feedback_content[$the_type] = isset($feed_feedback_content[$the_type]) ? $feed_feedback_content[$the_type] : null;
            $feed_feedback_content[$the_type] .= lang('order_number').'：'. $row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                    lang('content').'：'.$row->feedback_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                    lang('order_bad_comment_type').'：'.$the_type.br().'----------'.br();
        }

        
        $all_types = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));

        $current_dept = $this->default_system();
        $my_dept = array();
        foreach($all_types as $type)
        {
            $type_dept = $this->order_model->get_one('order_bad_comment_type', 'department', array('id' => $type));
            if($type_dept == $current_dept)
            {
                $my_dept[] = $type;
            } else {
                continue;
            }
        }

        $data = array(
            'begin_time'            => $begin_time,
            'end_time'              => $end_time,
            'refund_types'          => $refund_types,
            'order_count'           => $order_count,
            'refunds'               => $refunds,
            'feedbacks'             => $feedbacks,
            'item_no'               => $item_no,
            'all_types'             => $my_dept,
            'refund_duties'         => $refund_duties,
            'refund_feedback_count' => $refund_feedback_count,
            'feed_feedback_content' => $feed_feedback_content,
        );
        $this->set_2column('sidebar_department_statistics');
        $this->template->write_view('content', 'order/refund_resend/by_type', $data);
        $this->template->render();
    }

    public function by_sku() {
        $duty_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }

        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
                        'refund_skus',
                        $begin_time,
                        $end_time,
                        $duty_user
        );

        $refunds = (array) $result['facet'];
        $current_dept = $this->default_system();
        $item_no = array();

        $query = $result['query'];
        $refund_duties = array();
        if (!empty($query->docs)) {
            foreach ($query->docs as $row) {
                foreach ($row->refund_skus as $sku) {
                    if ($sku) {
                        $item_content = $this->order_model->get_one('myebay_feedback', 'feedback_content', array('item_no' => $row->item_no));
                        $item_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type, 'department' => $current_dept));
                        if ($item_type) {
                            $item_no[$sku] = isset($item_no[$sku]) ? $item_no[$sku] : null;
                            $item_no[$sku] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' . lang('content') . '：' . $item_content . '&nbsp;&nbsp;&nbsp;&nbsp;' . lang('refund_verify_type') . '：' . $item_type . br() . '----------' . br();
                        } else {
                            $refunds[$sku]--;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }

        $sku_result = $this->solr_statistics_model->fetch_field_count('skus', $begin_time, $end_time);
        $all_skus = (array) $sku_result['facet'];
        $feed_types = $this->order_model->get_result('order_bad_comment_type', 'id', array('department' => $current_dept));
        $my_types = array();
        foreach ($feed_types as $type) {
            $my_types[] = $type->id;
        }

        $feedback_obj = $this->ebay_model->feedback_statistics('feedback_sku_str', $my_types, $begin_time, $end_time);
        $feedbacks = array();
        $feed_feedback_content = array();
        $count = 1;
        foreach ($feedback_obj as $row) {
            $skus = explode(',', $row->feedback_sku_str);
            foreach ($skus as $sku) {
                if (empty($feedbacks[$sku])) {
                    $feedbacks[$sku] = $count;
                } else {
                    $feedbacks[$sku] += $count;
                }

                if ($row->type) {
                    $feed_feedback_content[$sku] = isset($feed_feedback_content[$sku]) ? $feed_feedback_content[$sku] : null;
                    $feed_feedback_content[$sku] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                            lang('content') . '：' . $row->feedback_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                            lang('order_bad_comment_type') . '：' . $row->type . br() . '----------' . br();
                } else {
                    $feedbacks[$sku]--;
                }
            }
        }
        $refund_feedback_skus = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));

        $data = array(
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'order_count' => $order_count,
            'refunds' => $refunds,
            'feedbacks' => $feedbacks,
            'refund_feedback_skus' => $refund_feedback_skus,
            'all_skus' => $all_skus,
            'refund_feedback_count' => $refund_feedback_count,
            'feed_feedback_content' => $feed_feedback_content,
            'item_no' => $item_no,
        );
        $this->set_2column('sidebar_department_statistics');
        $this->template->write_view('content', 'order/refund_resend/by_sku', $data);
        $this->template->render();
    }

    public function by_duty() {
        $duty_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
                        'refund_duties',
                        $begin_time,
                        $end_time,
                        $duty_user
        );
        $refunds = (array) $result['facet'];
        $query = $result['query'];

        $refund_duties = array();
        $item_no = array();
        $refund_verify_content = array();
        $refund_verify_type = array();
        $current_dept = $this->default_system();
        if (!empty($query->docs)) {
            foreach ($query->docs as $row) {
                foreach ($row->refund_duties as $duty) {
                    $refund_duties[] = $duty;
                    $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type, 'department' => $current_dept));
                    if($the_type)
                    {
                        $item_no[$duty] = isset($item_no[$duty]) ? $item_no[$duty] : null;
                        $item_no[$duty] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                lang('content') . '：' . $row->refund_verify_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                                lang('refund_verify_type') . '：' . $the_type . br() . '----------' . br();
                    } else {
                        $refunds[$duty] = isset($refunds[$duty]) ? $refunds[$duty] : null;
                        $refunds[$duty]--;
                    }
                }
            }
        }
        $refund_duties = array_unique($refund_duties);

        $feed_item_no = array();
        $feed_feedback_content = array();
        $feed_verify_type = array();

        $current_dept = $this->default_system();
        $feed_types = $this->order_model->get_result('order_bad_comment_type', 'id', array('department' => $current_dept));
        $my_types = array();
        foreach ($feed_types as $type) {
            $my_types[] = $type->id;
        }

        if($my_types == NULL){
            $feedback_obj = array();
            $rows = array();
        } else {
            $feedback_obj = $this->ebay_model->feedback_statistics('feedback_duty', $my_types, $begin_time, $end_time);
            $rows = $this->ebay_model->feedback_statistics_dept($my_types);
        }

        foreach ($rows as $row) {
            if ($row->feedback_duty) {
                $duty = $row->feedback_duty;
                $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->verify_type));
                $feed_feedback_content[$duty] = isset($feed_feedback_content[$duty]) ? $feed_feedback_content[$duty] : null;
                $feed_feedback_content[$duty] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                        lang('content') . '：' . $row->feedback_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('order_bad_comment_type') . '：' . $the_type . br() . '----------' . br();
            }
        }

        $feedbacks = array();
        $count = 1;
        foreach ($feedback_obj as $row) {
            $duties = explode(',', $row->feedback_duty);
            foreach ($duties as $duty) {
                if (empty($feedbacks[$duty])) {
                    $feedbacks[$duty] = $count;
                } else {
                    $feedbacks[$duty] += $count;
                }
            }
        }

        $refund_feedback_duties = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));
        $data = array(
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'order_count' => $order_count,
            'refunds' => $refunds,
            'feedbacks' => $feedbacks,
            'refund_feedback_duties' => $refund_feedback_duties,
            'refund_duties' => $refund_duties,
            'refund_feedback_count' => $refund_feedback_count,
            'item_no' => $item_no,
            'refund_verify_content' => $refund_verify_content,
            'refund_verify_type' => $refund_verify_type,
            'feed_feedback_content' => $feed_feedback_content,
            'feed_verify_type' => $feed_verify_type,
        );

        $this->set_2column('sidebar_department_statistics');
        $this->template->write_view('content', 'order/refund_resend/by_duty', $data);
        $this->template->render();
    }
}

?>