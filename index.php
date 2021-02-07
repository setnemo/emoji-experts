<?php

use EmojiExperts\Core\App;

require __DIR__ . '/vendor/autoload.php';

$cli = isset($argv[1]) && $argv[1] == 'cli';

if (!$cli) {
    echo json_encode(['check' => true]);
} else {
    App::run(__DIR__, false);
    $expectedTime = time() + 10 * 60;
    while (true) {
        App::get('tg')->runBot(true);
        if ($expectedTime <= time()) {
            die(0);
        }
    }
}
