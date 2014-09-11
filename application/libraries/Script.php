<?php

class script
{
    /*
     * notify the customer that the order has been shipped.
     */
    public function notify_comuster($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/email/notify_single_customer.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }

    /*
     * fetch epacket track number.
     */
    public function fetch_epacket_track_number($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/epacket/fetch_epacket_track_number.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
	
	public function fetch_specification_epacket_track_number($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/epacket/fetch_specification_epacket_track_number.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }

    /*
     * wait version - fetch epacket track number.
     */
    public function fetch_epacket_track_number_wait($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/epacket/fetch_epacket_track_number.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null";
        exec($command);
    }

    public function verify_single_release($args)
    {
        if (!isset ($args['release_id']))
        {
            return;
        }
        $release_id = $args['release_id'];
        $script = '/var/www/html/mallerp/scripts/seo/seo_verify_release.php';
        $command = "/usr/bin/php $script -i $release_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }

    public function get_auction_listing_fee($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/sale/get_auction_listing_fee.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
    
    public function get_item_transaction($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/epacket/get_item_transaction.php';
        $command = "/usr/bin/php $script -o $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }  
	public function complete_ebay_sale($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/sale/complete_ebay_sale.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
	public function complete_zencart_sale($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/sale/complete_zencart_sale.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
	public function complete_wish_sale($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/sale/complete_wish_sale.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
	public function complete_aliexpress_sale($args)
    {
        if (!isset ($args['order_id']))
        {
            return;
        }
        $order_id = $args['order_id'];
        $script = '/var/www/html/mallerp/scripts/sale/complete_aliexpress_sale.php';
        $command = "/usr/bin/php $script -i $order_id 2>/dev/null 1>/dev/null &";
        exec($command);
    }
}

?>
