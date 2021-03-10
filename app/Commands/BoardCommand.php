<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;
use EmojiExperts\Core\DbRepository;
use EmojiExperts\Traits\Cacheable;
use EmojiExperts\Traits\Translatable;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Slim\PDO\Database;

/**
 * Board command
 *
 */
class BoardCommand extends SystemCommand
{
    use Cacheable;
    /**
     * @var string
     */
    protected $name = 'board';
    /**
     * @var string
     */
    protected $description = 'Board command';
    /**
     * @var string
     */
    protected $usage = '/board';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();

        /** @var DbRepository $repo */
        $repo = App::get('repo');
        $text = '';
        foreach ($repo->getLeaders() as $index => $player) {
            $playerName = $player['username'] ? '@' . $player['username'] : $player['first_name'] . ' ' . $player['last_name'];
            $score = $player['score'];
            $place = $index + 1;
            $text .= "{$place}. $playerName {$score} correct answers\n";
        }

        $true = json_decode($this->cache()->get("game_yes_no_{$userId}"), true);
        $buttons = ['Top results', 'Main', 'Riddle',];

        $keyboard = new Keyboard(
            $buttons
        );

        if ($true !== null) {
            $keyboard = new Keyboard(
                ['Top results', 'Stop'], ['No', "Don't know", 'Yes']
            );
        }
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' =>  $text,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
