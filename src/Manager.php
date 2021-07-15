<?php

namespace DevCoder\DB;

use DevCoder\DB\Helper\EntityHelper;
use DevCoder\DB\Query\Parameter;
use DevCoder\DB\Repository\Repository;
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;

class Manager
{
    /**
     * 
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Manager constructor.
     * @param ContainerInterface $container
     * @param PDO $pdo
     */
    public function __construct(ContainerInterface $container, PDO $pdo)
    {
        $this->container = $container;
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }

    /**
     * @param string $query
     * @param array<Parameter> $params
     * @return PDOStatement
     */
    public function executeQuery(string $query, array $params = []): PDOStatement
    {
        $db = $this->pdo->prepare($query);
        foreach ($params as $parameter) {
            $db->bindValue($parameter->getName(), $parameter->getValue(), $parameter->getType());
        }
        $db->execute();
        return $db;
    }

    public function fetch(string $query, array $params = []): ?array
    {
        $db = $this->executeQuery($query, $params);
        $data = $db->fetch(PDO::FETCH_ASSOC);
        return $data === false ? null : $data;
    }

    public function fetchAll(string $query, array $params = []): ?array
    {
        $db = $this->executeQuery($query, $params);
        return $db->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getRepository(string $entityName): Repository
    {
        $reflection = EntityHelper::getReflection($entityName);
        $repositoryClassName = $reflection->getMethod('getRepository')->invoke(null);

        return $this->container->get($repositoryClassName);
    }
}
