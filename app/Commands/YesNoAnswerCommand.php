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
class YesNoAnswerCommand extends SystemCommand
{
    use Cacheable;
    /**
     * @var string
     */
    protected $name = 'yesnoanswer';
    /**
     * @var string
     */
    protected $description = 'YesNo Answer command';
    /**
     * @var string
     */
    protected $usage = '/yesnoanswer';
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

            $input = $message->getText();
            $score = intval($game['score']);
            if ($true['true'] == $input) {
                ++$score;
                $text = "✅ Awesome! You guessed!✅\nYou score: $score";
                $repo->updateGame($userId, $gameId, $score, DbRepository::YES_NO_GAME_MODE);
            } elseif ("Don't know" === $input) {
                // skip
                $thatWas = $true['true'] == 'Yes' ? 'true' : 'not true';
                $text = "That was <code>{$thatWas}</code>\nYou score: {$score}";
            } else {
                $this->cache()->incr("game_yes_no_errors_{$userId}");
                $text = "❌ Incorrect!❌\nYou score: {$score}";
            }

            $errors = $this->cache()->get("game_yes_no_errors_{$userId}");
            if ($errors == 3) {
                $this->clearCache($userId);
                $buttonsGame = ['Top results', 'Main', 'Riddle',];
                $text = "⛔️GAME OVER⛔️\n⛔️SCORE: {$score}⛔️";
            } else {
                $em = (new YesNoGame($userId, $gameId))->getEmojiForYesNo($userId, $gameId);

                $emoji = trim($em['emoji']);
                $name = $em['name'];
                $text .= "\n\n{$emoji}{$emoji}{$emoji}\n\n Does this emoji mean <code>{$name}</code>?";
                $buttonsGame = ['No', "Don't know", 'Yes'];
                if (mt_rand(0, 9) < 5) {
                    $buttonsGame = ['Yes', "Don't know", 'No'];
                }
            }
            $buttons = ['Top results', 'Stop'];
            $keyboard = new Keyboard(
                $buttons,
                $buttonsGame
            );
        } else {
            $this->clearCache($userId);
            $text = 'Sorry, game not found in cache. Press <code>Riddle</code> button to start new game↘';
            $keyboard = new Keyboard(['Top results', 'Main', 'Riddle']);
        }
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

    /**
     * @param int $userId
     * @throws \Exception
     */
    private function clearCache(int $userId): void
    {
        $this->cache()->del(["game_yes_no_errors_{$userId}"]);
        $this->cache()->del(["game_yes_no_{$userId}"]);
    }
}
