<?php
require_once APPPATH.'controllers/edu/edu'.EXT;

class Index extends Edu
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
