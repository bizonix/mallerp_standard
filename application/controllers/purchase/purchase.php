<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Purchase extends Mallerp
{
    const NAME = 'purchase';
    
    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
