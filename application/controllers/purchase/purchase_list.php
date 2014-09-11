<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Purchase_list extends Purchase
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('shipping_code_model');
    }

    public function index()
    {
        $this->template->write_view('content', 'purchase/default');
        $this->template->render();
    }

    public function view_list($id = NULL)
    {
        $purchaser_id = -1;
        if (isset($id))
        {
            $purchaser_id = $id;
        }
        $this->load->model('mixture_model');
        $purchase_list = $this->purchase_model->fetch_purchase_list($purchaser_id);
        $purchasers = $this->purchase_model->fetch_all_purchasers();
        $data = array(
            'purchase_list'             => $purchase_list,
            'fetch_dueout_update_time'  => $this->mixture_model->fetch_dueout_update_time(),
            'show_purchaser_filter'     => $this->purchase_model->show_purchaser_filter(),
            'purchasers'                => $purchasers,
            'purchaser_id'              => $purchaser_id,
            'stock_code'                => $this->shipping_code_model->fetch_stock_codes_status(),
        );
        $this->template->write_view('content', 'purchase/purchase_list', $data);
        $this->template->add_css('static/css/purchase.css');
        $this->template->add_js('static/js/sorttable.js');
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }
}

?>
