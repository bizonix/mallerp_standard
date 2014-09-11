#!/bin/bash

accounts=(101 005 006 011 010 001)

for account in ${accounts[*]}    
do
   php /var/www/html/newerp/scripts/order/auto_assign_message_catalog.php -a $account
done