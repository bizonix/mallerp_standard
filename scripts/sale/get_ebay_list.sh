#!/bin/bash
ebay_ids=(ebay1 ebay2)
for ebay_id in ${ebay_ids[*]}
do
   php /var/www/html/mallerp/scripts/sale/get_ebay_list.php -i $ebay_id -t buy_now
   php /var/www/html/mallerp/scripts/sale/get_ebay_list.php -i $ebay_id -t auction
done