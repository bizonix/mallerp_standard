<?php
require_once APPPATH.'controllers/pi/product'.EXT;

class Index extends Product
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
