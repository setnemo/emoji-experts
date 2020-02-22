<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use EmojiExperts\Traits\CurrencyConvertable;
use EmojiExperts\Traits\Translatable;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    public function execute()
    {
        $text = trim($this->getMessage()->getText(true));

        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId(),
            $this->getName()
        );

        if ($this->isGame($text)) {
            return $this->telegram->executeCommand('Answer');
        }

        $conversation->stop();
        return Request::emptyResponse();
    }
}