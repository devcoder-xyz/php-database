<?php

namespace Fad;

/**
 * Class QueryBuilder
 */
class QueryBuilder
{

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $conditions = [];

    /**
     * @var array
     */
    private $from = [];

    /**
     * @param string ...$select
     * @return $this
     */
    public function select(string ...$select): self
    {
        $this->fields = $select;
        return $this;
    }

    /**
     * @param string ...$where
     * @return QueryBuilder
     */
    public function where(string ...$where): self
    {
        foreach ($where as $arg) {
            $this->conditions[] = $arg;
        }
        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return QueryBuilder
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias === null) {
            $this->from[] = $table;
        } else {
            $this->from[] = "$table AS $alias";
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $where = empty($this->conditions) ? '' : ' WHERE ' . implode(' AND ', $this->conditions);
        return 'SELECT ' . implode(', ', $this->fields)
            . ' FROM ' . implode(', ', $this->from)
            . $where;
    }

}