<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Qt extends Mallerp
{
    const NAME = 'qt';

    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>