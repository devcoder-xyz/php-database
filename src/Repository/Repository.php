<?php

namespace DevCoder\DB\Repository;

use DevCoder\DB\Entity\EntityInterface;
use DevCoder\DB\Helper\EntityHelper;
use DevCoder\DB\Hydration\AbstractHydrator;
use DevCoder\DB\Hydration\ObjectHydrator;
use DevCoder\DB\Hydration\SingleObjectHydrator;
use DevCoder\DB\Manager;
use DevCoder\DB\Mapping\MappingInterface;
use DevCoder\DB\Mapping\OneToOne;
use DevCoder\DB\Query\Parameter;
use DevCoder\QueryBuilder;
use DevCoder\Select;
use InvalidArgumentException;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Repository constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    abstract public static function getEntity(): string;
    abstract public static function getTable(): string;

    public function findPk(int $pk): ?EntityInterface
    {
        $query = $this->createQueryBuilder()->where($this->getTable() . '.id = :id');

        $pdoStatement = $this->manager->executeQuery($query, [
            new Parameter('id', $pk)
        ]);

        return $this->getHydrator(SingleObjectHydrator::class)->getResult($pdoStatement);
    }

    public function findOneBy(array $arguments = []): ?EntityInterface
    {
        $data = $this->findBy($arguments, 1);
        return reset($data) ?? null;
    }

    public function findBy(array $arguments = [], ?int $limit = null, string $hydrator = ObjectHydrator::class): array
    {
        $query = $this->createQueryBuilder();
        if (is_int($limit)) {
            $query->limit($limit);
        }

        $arguments = $this->resolveArguments($arguments);
        foreach (array_keys($arguments) as $name) {
            $query->where(sprintf('%s = :%s', $name, $name));
        }

        $pdoStatement = $this->manager->executeQuery($query, Parameter::createCollection($arguments));
        return $this->getHydrator($hydrator)->getResult($pdoStatement);
    }


    public function findAll(?int $limit = null, string $hydrator = ObjectHydrator::class): array
    {
        return $this->findBy([], $limit, $hydrator);
    }

    public function save(EntityInterface $entity): bool
    {
        if (get_class($entity) !== $this->getEntity()) {
            throw new InvalidArgumentException('You use ' . $this->getEntity() . ' Repository. Argument passed is : ' . get_class($entity));
        }
        $data = EntityHelper::extract($entity);
        $columns = array_keys($data);

        if ($entity->getId() === null) {
            $this->manager->executeQuery(
                QueryBuilder::insert($this->getTable())->columns(...$columns),
                Parameter::createCollection($data)
            );
            $id = $this->manager->getPdo()->lastInsertId();
            EntityHelper::setId((int)$id, $entity);
            return true;
        }
        
        $this->manager->executeQuery(
            QueryBuilder::update($this->getTable())
                ->where('id = :id')
                ->set(...$columns),
            Parameter::createCollection(array_merge($data, ['id' => $entity->getId()]))
        );

        return true;
    }

    public function remove(EntityInterface $entity): bool
    {
        if ($entity->getId() === null) {
            throw new \InvalidArgumentException(get_class($entity) . 'Id cannot be null');
        }

        if (get_class($entity) !== $this->getEntity()) {
            throw new \InvalidArgumentException('You use ' . $this->getEntity() . ' Repository. Argument passed is : ' . get_class($entity));
        }

        $db = $this->manager->executeQuery(
            QueryBuilder::delete($this->getTable())->where('id = :id'),
            [new Parameter('id', $entity->getId())]
        );
        return $db->rowCount() > 0;
    }

    public function count(array $arguments = []): int
    {
        $query = QueryBuilder::select('COUNT(*)')
            ->from($this->getTable());

        foreach (array_keys($arguments) as $name) {
            $query->where(sprintf('%s = :%s', $name, $name));
        }
        $db = $this->manager->executeQuery($query, Parameter::createCollection($arguments));
        return $db->fetchColumn();
    }

    protected function createQueryBuilder()
    {
        $select = QueryBuilder::select(...[])->from($this->getTable(), $this->getTable());
        $this->resolveColumnsSelectSql($select);
        return $select;
    }

    protected function getHydrator(string $hydratorName): AbstractHydrator
    {
        $hydrator = (new \ReflectionClass($hydratorName))->newInstance($this->getEntity());
        if (!$hydrator instanceof AbstractHydrator) {
            throw new \InvalidArgumentException($hydratorName . ' must be an instance of ' . AbstractHydrator::class);
        }

        return $hydrator;
    }

    /**
     * @return array<MappingInterface>
     */
    protected function getEntityColumns(): array
    {
        $entity = $this->getEntity();
        return $entity::getColumns();
    }

    private function resolveColumnsSelectSql(Select $select): void
    {
        foreach ($this->getEntityColumns() as $column) {
            $select->select($this->getTable() . '.' . $column->getName() . ' AS ' . $this->getTable() . '_' . $column->getName());
            if ($column instanceof OneToOne && $column->getFetchMode() == 'EAGER') {

                $targetEntity = $column->getTargetEntity();
                $repository = call_user_func([$targetEntity, 'getRepository']);
                $table = call_user_func([$repository, 'getTable']);

                $select->innerJoin(sprintf('%s as %s ON %s = %s',
                    $table,
                    $table,
                    $this->getTable() . '.' . $column->getName(),
                    $table . '.id'
                ));
                $this->manager->getRepository($targetEntity)->resolveColumnsSelectSql($select);
            }
        }
    }

    private function resolveArguments(array $arguments): array
    {
        $argumentsResolved = [];
        foreach ($arguments as $name => $value) {
            foreach ($this->getEntityColumns() as $column) {
                if ($name === $column->getProperty()) {
                    $name = $column->getName();
                    break;
                }
            }
            $argumentsResolved[$name] = $value;
        }
        return $argumentsResolved;
    }
}
