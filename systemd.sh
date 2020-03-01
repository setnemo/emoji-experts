#!/bin/sh

while true ; do
    php /var/www/emoji/index.php 'cli'
done

#root@2lob:~# cat /lib/systemd/system/emoji.service
#[Unit]
#Description=Emoji telegram bot systemd service.
#Requires=mysql.service
#After=mysql.service
#
#[Service]
#Type=simple
#ExecStart=/bin/sh /var/www/emoji/systemd.sh
#Restart=always
#
#[Install]
#WantedBy=multi-user.target