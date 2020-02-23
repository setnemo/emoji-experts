<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;
use EmojiExperts\Core\DbRepository;
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
class YesNoCommand extends SystemCommand
{
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
        /** @var DbRepository $repo */
        $repo = App::get('repo');

        $repo->getGame($message->getFrom()->getId(), DbRepository::YES_NO_GAME_MODE);
        $keyboard = new Keyboard(
            ['1', '2']
        );
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => 'yes no text',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
