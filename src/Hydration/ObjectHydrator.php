<?php

namespace DevCoder\DB\Hydration;

use DevCoder\DB\Entity\EntityInterface;

class ObjectHydrator extends AbstractObjectHydrator
{
    /**
     * @param \PDOStatement $statement
     * @return array<EntityInterface>
     */
    public function getResult(\PDOStatement $statement): array
    {
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $entities = [];
        foreach ($data as $entityArray) {
            $entities[] = $this->hydrate($entityArray);
        }
        return $entities;
    }
}
