<?php

namespace DevCoder\DB\Repository;

use DevCoder\DB\Entity\EntityInterface;

interface RepositoryInterface
{
    public function findPk(int $pk): ?EntityInterface;

    /**
     * @param array $arguments
     * @return array<EntityInterface>
     */
    public function findBy(array $arguments = []): array;

    public function findOneBy(array $arguments = []): ?EntityInterface;

    /**
     * @return array<EntityInterface>
     */
    public function findAll(): array;

    public function save(EntityInterface $entity): bool;

    public function remove(EntityInterface $entity): bool;

    public function count(array $arguments = []): int;
}
