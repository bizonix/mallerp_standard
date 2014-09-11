<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Sale extends Mallerp
{
    const NAME = 'sale';

    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>