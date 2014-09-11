<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Void extends Mallerp
{
    const NAME = 'void';
    
    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
