<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Index extends Order
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
