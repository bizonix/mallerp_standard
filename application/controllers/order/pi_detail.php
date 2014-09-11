<?php

require_once APPPATH . 'controllers/mallerp_no_key' . EXT;

class Pi_detail extends Mallerp_no_key {

    public function __construct() {
        parent::__construct();
    }

    public function pi_detail_list($id) {
        $contact = '/var/www/html/mallerp/static/after_order_pi/';
        $path = $contact . $id;
        echo file_get_contents($path);
    }

    public function pi_detail_list_before($id) {
        $contact = '/var/www/html/mallerp/static/before_order_pi/';
        $path = $contact . $id;
        echo file_get_contents($path);
    }
}