<?php

namespace DevCoder\DB\Repository;

use DevCoder\DB\Manager;
use Fad\Entity\EntityInterface;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function find(int $id): ?object
    {
        $db = $this->manager->getPDOStatement('', []);

    }

    /**
     * @param array $arguments
     * @return array
     */
    public function findBy(array $arguments = []): array
    {

    }

    public function findOneBy(array $arguments = []): ?object
    {

    }

    public function findAll(): array
    {

    }

    public function save(EntityInterface $entity): bool
    {

    }


    public function remove(EntityInterface $entity): bool
    {

    }

    public function count(array $arguments = []): int
    {

    }

    abstract protected static function getTableName(): string;

    abstract protected static function getEntity(): string;
}
