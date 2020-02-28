<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;
use EmojiExperts\Core\DbRepository;
use EmojiExperts\Game\YesNoGame;
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
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StopCommand extends SystemCommand
{
    use Cacheable;
    /**
     * @var string
     */
    protected $name = 'stopgame';
    /**
     * @var string
     */
    protected $description = 'YesNo Answer command';
    /**
     * @var string
     */
    protected $usage = '/stopgame';
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
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();
        /** @var DbRepository $repo */
        $repo = App::get('repo');

        $true = json_decode($this->cache()->get("game_yes_no_{$userId}"), true);
        if ($true !== null) {
            $gameId = $true['gameId'];
            $game = $repo->getGameById($gameId);

            $score = intval($game['score']);
            $this->cache()->del(["game_yes_no_errors_{$userId}", "game_yes_no_{$userId}"]);
        }
        $score = $score ?? 0;
        $text = "✅ Awesome! Game stopped!\nYou score: $score\nPress Riddle button to start new game↘";
        $buttonsGame = ['Top results', 'Main', 'Riddle',];
        $keyboard = new Keyboard(
            $buttonsGame
        );
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
