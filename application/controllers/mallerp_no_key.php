<?php
class Mallerp_no_key extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set(DEFAULT_TIMEZONE);

        $this->lang->load('mallerp', DEFAULT_LANGUAGE);

        $this->load->driver('cache', array('backup' => 'file'));
    }
}

?>
