<?php

namespace EmojiExperts\Core;

use EmojiExperts\Currency\Api\Factory\CurrencyContentStaticFactory;
use Slim\PDO\Database;

class DbRepository
{
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
     * @param string|null $lang
     * @return array
     */
    public function getConfigByIdOrCreate(int $id, ?string $lang): array
    {
        $selectStatement = $this->connection->select([
            'user_id', 'lang', 'buttons', 'inline'
        ])
            ->from('user_config')
            ->where('user_id', '=', $id);
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();

        if (0 === $stmt->rowCount()) {
            $result = $this->insertNewConfig($id, $lang);
        }

        return $result[0];
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
            ->into('user_config')
            ->values($result);
        $insertStatement->execute(false);

        return [$result];
    }

    /**
     * @param int $id
     * @param array $apis
     */
    public function updateApiFromConfig(int $id, array $apis)
    {
        $selectStatement = $this->connection->select(['inline'])
            ->from('user_config')
            ->where('user_id', '=', $id);
        $stmt = $selectStatement->execute();
        $fetch = $stmt->fetchAll();

        $result = $fetch[0] ?? [];
        $newDataString = $result['inline'] ?? '{}';
        $newData = json_decode($newDataString, true);
        $newData['available_api'] = $apis;
        $updateStatement = $this->connection->update(['inline' => \GuzzleHttp\json_encode($newData)])
            ->table('user_config')
            ->where('user_id', '=', $id);
        $affectedRows = $updateStatement->execute();
    }
}