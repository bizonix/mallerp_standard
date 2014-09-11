<?php
require_once APPPATH.'controllers/finance/finance'.EXT;

class Index extends Finance
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
