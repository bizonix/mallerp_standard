<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Index extends Seo
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $this->template->write_view('content', 'seo/index');
        $this->template->render();
    }
}

?>
