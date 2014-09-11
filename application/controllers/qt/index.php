<?php
require_once APPPATH.'controllers/qt/qt'.EXT;

class Index extends Qt
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
