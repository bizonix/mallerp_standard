<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Shipping extends Order
{
    const NAME = 'shipping';

    public function __construct() {
        parent::__construct();
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
