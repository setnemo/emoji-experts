<?php

use EmojiExperts\Core\App;

require __DIR__ . '/vendor/autoload.php';

$cli = isset($argv[1]) && $argv[1] == 'cli';

App::run(__DIR__, false);

if (!$cli) {
    echo json_encode(['check' => true]);
}
