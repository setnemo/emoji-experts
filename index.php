<?php

use EmojiExperts\Core\App;

require __DIR__ . '/vendor/autoload.php';

App::run(__DIR__, false);
App::get('tg')->runBot(false);

echo json_encode(['emoji' => 'ok']);
