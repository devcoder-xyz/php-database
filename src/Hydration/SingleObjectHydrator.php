<?php

namespace DevCoder\DB\Hydration;

use DevCoder\DB\Entity\EntityInterface;

final class SingleObjectHydrator extends AbstractObjectHydrator
{
    public function getResult(\PDOStatement $statement): ?EntityInterface
    {
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        return $data === false ? null :  $this->hydrate($data);
    }
}
