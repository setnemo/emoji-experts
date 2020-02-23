<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
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

        if ($text == 'Top players') {
            return $this->telegram->executeCommand('Board');
        }

        if ($this->isStartNewGame($text)) {
            return $this->telegram->executeCommand($text);
        }


        if ($this->isGameYesNo($text)) {
            return $this->telegram->executeCommand('YesNoAnswer');
        }

        $conversation->stop();
        return Request::emptyResponse();
    }

    private function isStartNewGame(string $text): bool
    {
        return in_array($text, ['Riddle', 'YesNo']);
    }

    private function isGameYesNo(string $text): bool
    {
        return in_array($text, ['Yes', 'No']);
    }
}