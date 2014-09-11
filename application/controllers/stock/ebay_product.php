<?php

require_once APPPATH . 'controllers/stock/stock' . EXT;

class Ebay_product extends Stock {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('product_model');
        $this->load->model('ebay_model');
        $this->load->model('order_model');
        $this->load->model('paypal_model');
        $this->load->model('user_model');
        $this->config->load('config_ebay');
    }

    public function ebay_comment_list()
    {
        $this->enable_search('comments');
        $this->enable_sort('comments');

        $role = $this->user_model->fetch_user_priority_by_system_code('sale');
        if ($role > 1 || $this->is_super_user()) {
            $configs = $this->config->item('ebay_id');
            $ebay_ids = array_values($configs);
        }
        else {
            $user_id = get_current_user_id();
            $ebay_ids = $this->paypal_model->fetch_ebay_ids_by_user_id($user_id);
        }
        $ebay_select = array();
        foreach ($ebay_ids as $ebay_id) {
            $ebay_select[''] = lang('please_select');
            $ebay_select[$ebay_id] = $ebay_id;
        }

        $stock_type = array('bad_comments_wait_for_commit', 'bad_comments_commited');
        $feedbacks = $this->ebay_model->fetch_ebay_feedback($ebay_ids, $stock_type);
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type(true);
        $data = array(
            'ebay_ids' => $ebay_select,
            'feedbacks' => $feedbacks,
            'bad_comment_types' => $bad_comment_types,
            'stock_type'          => 'bad_comments_commited'
        );

        $this->template->write_view('content', 'order/regular_order/ebay_comments_list', $data);
        $this->template->add_js('static/js/ajax/mytaobao.js');
        $this->template->render();
    }
}