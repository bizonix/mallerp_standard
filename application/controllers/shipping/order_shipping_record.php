<?php

require_once APPPATH . 'controllers/mallerp_no_key' . EXT;

class Order_shipping_record extends Mallerp_no_key {

    public function __construct() {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('shipping_code_model');
        $this->load->model('order_shipping_record_model');
    }

    public function save() {
        
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'save_order_shipping_record.php') === FALSE)
        {
            exit;
        }
        $pd_show = $this->order_shipping_record_model->print_or_deliver();
        $pd_show = empty($pd_show) ? 0 : $pd_show;
        $today = $this->order_shipping_record_model->print_or_deliver_today();
        $shiptoday = $this->order_shipping_record_model->print_or_deliver_shiptoday();
        $ship_confirm_day = $this->order_shipping_record_model->print_or_deliver_ship_confirm_day();
        $record_note = $this->order_shipping_record_model->fetch_order_record_remark();
        $ship_note = $record_note->shipping_note;
        $stock_note = $record_note->stock_note;
        $current_print_deliver = $this->order_shipping_record_model->save_print_or_deliver_shipping_record($pd_show, $today, $shiptoday, $ship_confirm_day, $stock_note, $ship_note);
    }
}
