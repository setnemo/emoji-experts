<?php

namespace EmojiExperts\Game;

use EmojiExperts\Core\App;
use EmojiExperts\Core\DbRepository;
use EmojiExperts\Traits\Cacheable;

class YesNoGame
{
    use Cacheable;

    protected $userId;
    protected $gameId;

    public function __construct(int $userId, int $gameId)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
    }

    public function getEmojiForYesNo(int $userId, int $gameId)
    {
        /** @var DbRepository $repo */
        $repo = App::get('repo');
        $categories = $repo->getCategories();
        shuffle($categories);
        $choise = $categories[0];

        $emojies = $repo->getEmoji($choise['category'], $choise['subcategory']);

        $item['emoji'] = $emojies[0]['emoji'];
        $isTrue = 'Yes';
        if (mt_rand(0, 9) < 5) {
            unset($emojies[0]);
            $isTrue = 'No';
            shuffle($emojies);
        }
        $item['name'] = $emojies[0]['name'];
        $save = json_encode(['true' => $isTrue, 'gameId' => $gameId]);
        $this->cache()->set("game_yes_no_{$userId}", $save, 'EX', 300);
        return $item;
    }
}