<?php

use EmojiExperts\Core\App;

require __DIR__ . '/vendor/autoload.php';

$cli = isset($argv[1]) && $argv[1] == 'cli';

App::run(__DIR__, $cli);

if (!$cli) {
    echo json_encode(['check' => true]);
}

/**
root@2lob:~# cat /lib/systemd/system/emoji.service
[Unit]
Description=Emoji telegram bot systemd service.
Requires=mysql.service
After=mysql.service

[Service]
Type=simple
ExecStart=/usr/bin/php7.4 /var/www/emoji/index.php cli
Restart=always

[Install]
WantedBy=multi-user.target
 *

 */
