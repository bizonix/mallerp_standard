<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Finance extends Mallerp
{
    const NAME = 'finance';

    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>