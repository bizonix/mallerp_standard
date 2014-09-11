<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Index extends Sale
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
