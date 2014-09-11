<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Index extends Admin
{
    public function test()
    {
        $this->template->write_view('content', 'admin/test');
        $this->template->render();
    }
}

?>
