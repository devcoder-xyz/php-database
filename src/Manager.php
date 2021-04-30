<?php

namespace DevCoder\DB;

use DevCoder\DB\Query\Parameter;
use Fad\QueryBuilder;

final class Manager
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo = $pdo;
    }

    /**
     * @param QueryBuilder $query
     * @param array<Parameter> $params
     * @return \PDOStatement
     */
    public function getPDOStatement(QueryBuilder $query, array $params = []) : \PDOStatement
    {
        $db = $this->pdo->prepare($query);
        foreach ($params as $parameter) {
            $value = $parameter->getValue();
            $db->bindParam($parameter->getName(), $value, $parameter->getType());
        }
        return $db;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}
