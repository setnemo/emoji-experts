<?php

use Dotenv\Dotenv;
use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;

require __DIR__ . '/vendor/autoload.php';
$env = Dotenv::createImmutable(__DIR__);
$env->load();
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
    try {
        $repo->insertEmoji($value['char'], $value['codes'], $value['name'], $category, $subcategory);
    } catch (PDOException $e) {
        echo 'HERAK!';
    }
}
