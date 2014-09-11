#!/bin/bash
for i in 1 2 3 4 5 6 7 8 9 10
do
curl http://192.168.1.107/index.php/shipping/epacket/add_order/$i &
done