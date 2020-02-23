<?php

use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;

require __DIR__ . '/vendor/autoload.php';

$e = json_decode(file_get_contents('emoji.json'), true);
$result = [];
$repo = Connection::getRepository();
foreach ($e as $index => $value) {
    $cat = explode('(', $value['category']);
    $category = trim($cat[0]);
    $subcategory = trim(explode(')', $cat[1])[0]);
//    $items = [
//        'emoji' => $value['char'],
//        'codes' => $value['codes'],
//        'name' => $value['name'],
//        'category' => $category,
//        'subcategory' => $subcategory,
//    ];
//    pp($items);
//    break ;
    $repo->insertEmoji($value['char'], $value['codes'], $value['name'], $category, $subcategory);
}

//pp(array_unique($result));
function pp($item) {
    echo PHP_EOL;
    var_export($item);
    echo PHP_EOL;
}


//App::run(__DIR__);
//
//echo json_encode(['check' => true]);
