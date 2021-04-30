<?php

namespace DevCoder\DB\Repository;

use DevCoder\DB\Entity\EntityInterface;

interface RepositoryInterface
{
    public function find(int $id): ?object;

    public function findBy(array $arguments = []): array;

    public function findOneBy(array $arguments = []): ?object;

    public function findAll(): array;

    public function save(EntityInterface $entity): bool;

    public function remove(EntityInterface $entity): bool;

    public function count(array $arguments = []): int;
}
