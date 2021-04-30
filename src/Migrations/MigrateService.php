<?php

namespace Fad\Migrations;

/**
 * Class MigrateService
 * @package Fad\Migrations
 */
class MigrateService
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $params;

    /***
     * @var array
     */
    private $success = [];

    /**
     * MigrateService constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->pdo = $params['connection'];
        $defaultParams = [
            'table_name' => 'migration_versions',
        ];
        $this->params = $defaultParams + $params;
    }

    /**
     * @param string|null $model
     */
    public function generateMigration(?string $model = null): void
    {
        $file = date('YmdHis') . '.sql';
        file_put_contents($this->params['migrations_directory'] . DIRECTORY_SEPARATOR . $file, '');
    }

    /**
     */
    public function migrate(): void
    {
        $this->createVersion();
        $this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        $stmt = $this->pdo->prepare('SELECT version FROM ' . $this->params['table_name']);
        $stmt->execute();
        $versions = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($this->getMigrations() as $version => $migration) {

            if (in_array($version, $versions)) {
                continue;
            }

            $this->pdo->query(file_get_contents($migration));
            $this->pdo->prepare('INSERT INTO ' . $this->params['table_name'] . ' (`version`) VALUES (:version)')
                ->execute(['version' => $version]);

            $this->success[] = $version;
        }

    }

    /**
     * @return void
     */
    public function createVersion(): void
    {
        $this->pdo->query('CREATE TABLE IF NOT EXISTS ' . $this->params['table_name'] . ' (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, version varchar(255) NOT NULL)');
    }

    /**
     * @return array
     */
    private function getMigrations(): array
    {
        $migrations = [];
        foreach (new \DirectoryIterator($this->params['migrations_directory']) as $file) {
            if ($file->getExtension() !== 'sql') {
                continue;
            }
            $version = pathinfo($file->getBasename(), PATHINFO_FILENAME);
            $migrations[$version] = $file->getPathname();
        }
        ksort($migrations);
        return $migrations;
    }

    /**
     * @return array
     */
    public function getSuccess(): array
    {
        return $this->success;
    }
}
