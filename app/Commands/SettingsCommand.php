<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use EmojiExperts\Core\App;
use EmojiExperts\Core\Connection;
use EmojiExperts\Currency\Api\Factory\CurrencyContentStaticFactory;
use EmojiExperts\Currency\Api\Providers\Minfin;
use EmojiExperts\Currency\CurrencyEntity;
use EmojiExperts\Traits\Cacheable;
use EmojiExperts\Traits\Translatable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use ReflectionException;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class SettingsCommand extends UserCommand
{
    use Cacheable;

    /**
     * @var string
     */
    protected $name = 'Settings';
    /**
     * @var string
     */
    protected $description = 'Settings command';
    /**
     * @var string
     */
    protected $usage = '/settings';
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
     * @throws GuzzleException
     * @throws TelegramException
     * @throws ReflectionException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        /** @var User $user */
        $keyboard = new Keyboard(
            ['1', '2'],
            ['3', '4'],
            ['Back']
        );
        $text = 'Text';
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
