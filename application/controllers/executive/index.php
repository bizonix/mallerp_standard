<?php
require_once APPPATH.'controllers/executive/executive'.EXT;

class Index extends Executive
{
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Write to $content

        $this->template->write_view('content', 'cs/test');

        // Render the template
        $this->template->render();

    }
}

?>
