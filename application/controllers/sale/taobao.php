<?php

require_once APPPATH . 'controllers/sale/sale' . EXT;

class Taobao extends Sale {

    public function __construct() {
        parent::__construct();
        $this->load->helper('taobao');
        $this->load->model('taobao_model');
        $this->load->model('order_model');
        $this->load->library('form_validation');
    }

    public function comments() {
        $this->enable_search('rate');
        $this->enable_sort('rate');
        $this->render_list('sale/taobao/trade_rate');
    }

    public function bad_type_confirm() {
        $rate_id = $this->input->post('rate_id');
        $bad_type = $this->input->post('bad_type');

        $data = array(
            'bad_type' => $bad_type,
        );

        $this->db->update('taobao_trade_rate', $data, array('id' => $rate_id));

        echo $this->create_json(1, lang('ok'), $remark);
    }

    public function confirm_review() {
        $rate_id = $this->input->post('rate_id');

        $result = $this->taobao_model->get_comment_results($rate_id);
        try {
            switch ($result) {
                case 'good':
                    $verify_result = $result . "_" . "verify";
                    break;
                case 'bad':
                    $verify_result = $result . "_" . "verify";
                    break;
                case 'neutral':
                    $verify_result = $result . "_" . "verify";
                    break;
                default :
                    $verify_result = $result;
            }
        } catch (Exception $ex) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        $remark = $this->input->post('remark');
        $bad_type = $this->input->post('bad_type');
        
        $role = $this->user_model->fetch_user_priority_by_system_code('sale');
        if($role > 1 || $this->is_super_user()){
            $data = array(
                'review' => $remark,
                'result' => $verify_result,
                'bad_type' => $bad_type
            );
        }else{
            $data = array(
                'review' => $remark,
                'bad_type' => $bad_type
            );
        }

        $this->db->update('taobao_trade_rate', $data, array('id' => $rate_id));

        echo $this->create_json(1, lang('ok'), $remark);
    }

    private function render_list($url) {
        $rates = $this->taobao_model->fetch_trade_rate();
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();
        $data = array(
            'rate' => $rates,
            'bad_comment_types' => $bad_comment_types
        );

        $this->template->write_view('content', $url, $data);
        $this->template->add_js('static/js/ajax/mytaobao.js');
        $this->template->render();
    }

}

?>