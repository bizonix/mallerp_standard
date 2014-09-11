<?php
require_once APPPATH.'controllers/qt/qt'.EXT;

class Wait_for_product_list extends Qt
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model');
    }

    public function view_list($id = NULL)
    {
        $purchaser_id = -1;
        if (isset($id) && isset($key))
        {
            $purchaser_id = $id;
        }
        $this->load->model('mixture_model');
        $purchase_list = $this->purchase_model->fetch_purchase_list($purchaser_id, TRUE);
        $purchasers = $this->purchase_model->fetch_all_purchasers();
        $data = array(
            'purchase_list'             => $purchase_list,
            'fetch_dueout_update_time'  => $this->mixture_model->fetch_dueout_update_time(),
            'show_purchaser_filter'     => TRUE,
            'purchasers'                => $purchasers,
            'purchaser_id'              => $purchaser_id,
        );
        $this->template->write_view('content', 'qt/wait_for_product_list', $data);
        $this->template->add_css('static/css/purchase.css');
        $this->template->add_js('static/js/sorttable.js');
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }

    public function view_list_pages()
    {
        $this->enable_search('purchase_list');
        $this->enable_sort('purchase_list');

        $purchase_list = $this->purchase_model->fetch_list_by_dueout_count();

        $data = array(
            'purchase_list'             => $purchase_list,
        );
        $this->template->write_view('content', 'qt/product_list_by_dueout_count', $data);
        $this->template->add_css('static/css/purchase.css');
        $this->template->add_js('static/js/sorttable.js');
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }
}
?>
