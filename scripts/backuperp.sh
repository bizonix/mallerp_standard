#!/bin/sh
SqlBakName=newerpsql_$(date +%y%m%d).sql
cd /etc/rc.d/init.d/
mysqldump -umallerp -ppassword newerp> /backup/$SqlBakName
cd /backup
ftp -nv 192.168.0.35 << EOF
user mallerp mallerpftp
put $SqlBakName
quit
EOF
rm -f $SqlBakName
echo "done"
