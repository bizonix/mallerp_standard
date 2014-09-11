<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once APPPATH . 'controllers/admin/admin' . EXT;

class Crontab extends Admin {

    public function __construct() {
        parent::__construct();
        $this->load->model('crontab_model');
    }

    public function admin_crontab() {

        $crontabresult = $this->crontab_model->crontab_all_sql();

        $data = array(
            'crontabresult' => $crontabresult,
        );

        $this->template->write_view('content', 'order/admin/crontab_view', $data);
        $this->template->render();
    }

    public function verify_crontab() {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        $result_fetch = $this->crontab_model->fetch_crontab($id);

        try {
            switch ($type) {
                case 'on':
                    $exptype = explode(" ", $value);
                    //echo count($exptype);
                    
                    $show_math = preg_match('/^[0-9\/,\s\*]*$/', $value);

                    if (count($exptype)!== 5 || ! $show_math) {
                        echo $this->create_json(0, lang('admin_crontab_error'), $value);
                        return;
                    }
                    break;

                case 'job':
                    if ($this->crontab_model->check_exists('system_crontab', array('job' => $value)) && $value != $result_fetch->job) {
                        echo $this->create_json(0, lang('admin_crontab_joberror'), $result_fetch->job);
                        return;
                    }
                    break;
            }

            $user_id = get_current_user_id();
            $this->crontab_model->verify_crontab($id, $type, $value, $user_id);
            echo $this->create_json(1, lang('ok'), empty($value) ? '[edit]' : $value);
        } catch (Exception $e) {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function crontab_add_row() {
        $user_id = get_current_user_id();

        $data = array(
            'on' => '[edit]',
            'job' => '[edit]',
            'description' => '[edit]',
            'creator' => $user_id,
        );

        try {
            $this->crontab_model->crontab_add_row($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        } catch (Exception $e) {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function crontab_delete() {
        $id = $this->input->post('id');
        $this->crontab_model->crontab_delete($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

}