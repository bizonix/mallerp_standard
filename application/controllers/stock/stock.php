<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Stock extends Mallerp
{
    const NAME = 'stock';

    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>