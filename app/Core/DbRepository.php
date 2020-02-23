<?php

namespace EmojiExperts\Core;

use EmojiExperts\Currency\Api\Factory\CurrencyContentStaticFactory;
use Slim\PDO\Database;

class DbRepository
{
    const YES_NO_GAME_MODE = 0;

    const RIDDLE_GAME_MODE = 1;

    const STATUS_GAME_OVER = 0;

    const STATUS_IN_PROGRESS = 1;
    /**
     * @var Database
     */
    private $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database;
    }

    /**
     * @param int $id
     * @param string $lang
     */
    public function updateLanguageCode(int $id, string $lang)
    {
        $updateStatement = $this->connection->update(['lang' => $lang])
            ->table('user_config')
            ->where('user_id', '=', $id);
        $affectedRows = $updateStatement->execute();
    }


    /**
     * @param int $id
     * @return array
     */
    public function getEmoji(int $id): array
    {
        $selectStatement = $this->connection->select([
            'emoji',
            'name',
            'category',
            'subcategory',
        ])
            ->from('emoji')
            ->where('id', '=', $id);
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        return $fetch[0] ?? [];
    }

    public function getGame(int $id, int $mode): array
    {
        $selectStatement = $this->connection->select([
            'score',
        ])
            ->from('games')
            ->where('user_id', '=', $id)
            ->where('status', '=', self::STATUS_IN_PROGRESS)
            ->where('mode', '=', $mode);
        $selectStatement;
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        $result = $fetch[0] ?? [];

        App::get('logger')->error('DB', [$stmt->queryString]);

        if (empty($result)) {
            $result = $this->startNewGame($id, $mode);
        }

        return $result;
    }

    /**
     * @param string $emoji
     * @param string $codes
     * @param string $name
     * @param string $category
     * @param string $subcategory
     * @return array
     */
    public function insertEmoji(
        string $emoji,
        string $codes,
        string $name,
        string $category,
        string $subcategory
    ): array {
        $result = [
            $emoji,
            $codes,
            $name,
            $category,
            $subcategory
        ];
        $insertStatement = $this->connection->insert([
            'emoji',
            'codes',
            'name',
            'category',
            'subcategory',
        ])
            ->into('emoji')
            ->values($result);
        $insertStatement->execute(false);

        return [$result];
    }

    protected function startNewGame(int $id, int $mode): array
    {
        $insertStatement = $this->connection->insert([
            'user_id',
            'mode',
        ])
            ->into('games')
            ->values([
                $id,
                $mode
            ]);

        $insertStatement->execute(false);

        return ['score' => 0];
    }
}