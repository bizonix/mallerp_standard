<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Pi extends Mallerp
{
    const NAME = 'pi';
    
    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }    
}

?>