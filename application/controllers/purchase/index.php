<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Index extends Purchase
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $this->template->write_view('content', 'purchase/default');
        $this->template->render();
    }
}

?>
