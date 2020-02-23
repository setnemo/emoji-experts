<?php

namespace EmojiExperts\Core;

use Carbon\Carbon;
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
     * @param string $category
     * @param string $subcategory
     * @return array
     */
    public function getEmoji(string $category, string $subcategory): array
    {
        $selectStatement = $this->connection->select([
            'emoji',
            'name',
        ])
            ->from('emoji')
            ->where('category', '=', $category)
            ->where('subcategory', '=', $subcategory)
        ;
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        return $fetch ?? [];
    }

    public function getLeaders(): array
    {
        $selectStatement = $this->connection->select([
            'u.username',
            'u.first_name',
            'u.last_name',
            'g.score',
            'g.status',
        ])
            ->from('games g')
            ->join('user u', 'u.id', '=', 'g.user_id')
            ->orderBy('g.score', 'DESC')
            ->orderBy('g.created_at', 'DESC')
            ->limit(20)
        ;
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();
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


    public function getCategories()
    {
        $selectStatement = $this->connection->select([
            'category', 'subcategory'
        ])
            ->from('emoji')
            ->distinct()
            ->whereNotLike('category', 'Flags')
            ->whereNotLike('category', 'Symbols')
        ;
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        return $fetch ?? [];

    }

    public function startNewGame(int $id, int $mode): array
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

        $newId = $insertStatement->execute();
        return ['id' => $newId, 'score' => 0];
    }

    public function updateGame(int $userId, int $id, int $score)
    {
        $updateStatement = $this->connection->update([
            'score' => $score,
            'updated_at' => Carbon::now()
        ])
            ->table('games')
            ->where('user_id', '=', $userId)
            ->where('id', '=', $id)
        ;
        $affectedRows = $updateStatement->execute();
    }

    public function getGameById($gameId)
    {
        $selectStatement = $this->connection->select([
            'id',
            'score',
            'created_at',
            'updated_at',
        ])
            ->from('games')
            ->where('id', '=', $gameId)
        ;
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        return $fetch[0] ?? [];
    }
}