#!/bin/bash
ebay_ids=(ebay1 ebay2)
for ebay_id in ${ebay_ids[*]}
do
   php /var/www/html/mallerp/scripts/sale/add_ebay_message.php -i $ebay_id
done

