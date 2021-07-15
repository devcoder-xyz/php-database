<?php

namespace DevCoder\DB\Hydration;

class SimpleArrayHydrator extends AbstractHydrator
{
    public function getResult(\PDOStatement $statement): array
    {
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
