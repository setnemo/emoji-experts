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
class RiddleCommand extends SystemCommand
{
    use Cacheable;
    /**
     * @var string
     */
    protected $name = 'yesno';
    /**
     * @var string
     */
    protected $description = 'YesNo command';
    /**
     * @var string
     */
    protected $usage = '/yesno';
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
        $repo = Connection::getRepository();

        $game = $repo->startNewGame($message->getFrom()->getId(), DbRepository::YES_NO_GAME_MODE);
        $gameId = $game['id'];
        $em = (new YesNoGame($userId, $game['id']))->getEmojiForYesNo($userId, $gameId);
        $this->cache()->set("game_yes_no_errors_{$userId}", 0, 'EX', 300);
        $emoji = trim($em['emoji']);
        $name = $em['name'];
        $text = "{$emoji}{$emoji}{$emoji}\n\n Does this emoji mean <code>{$name}</code>?";
        $buttons = ['No', "Don't know", 'Yes'];
        $keyboard = new Keyboard(
            $buttons
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
