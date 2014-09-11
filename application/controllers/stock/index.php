<?php
require_once APPPATH.'controllers/stock/stock'.EXT;

class Index extends Shipping
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $this->template->write_view('content', 'default/welcome');
        $this->template->render();
    }
}

?>
