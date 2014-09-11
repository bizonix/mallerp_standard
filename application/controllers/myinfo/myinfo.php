<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Myinfo extends Mallerp
{
    const NAME = 'myinfo';
    
    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
