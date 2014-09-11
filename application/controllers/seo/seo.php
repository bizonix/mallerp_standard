<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Seo extends Mallerp
{
    const NAME = 'seo';
    
    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
