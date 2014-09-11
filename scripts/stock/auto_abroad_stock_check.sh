#!/bin/bash

stock_codes=(uk de au yb)

for stock_code in ${stock_codes[*]}    
do
   php /var/www/html/newerp/scripts/stock/auto_abroad_stock_check.php -s $stock_code
done

