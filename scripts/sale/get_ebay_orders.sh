#!/bin/bash
ebay_ids=(ebay1 ebay2)
for ebay_id in ${ebay_ids[*]}
do
php /var/www/html/mallerp/scripts/sale/get_ebay_orders.php -i $ebay_id
done