#!/bin/bash
paypal_users=(101)
for paypal_user in ${paypal_users[*]}
do
php /var/www/html/mallerp/scripts/order/paypal_order.php -a $paypal_user
done
