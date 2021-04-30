<?php

namespace DevCoder\DB\Query;

class Parameter
{
    /**
     * The parameter name.
     *
     * @var string
     */
    private $name;

    /**
     * The parameter value.
     *
     * @var mixed
     */
    private $value;

    /**
     * The parameter type.
     *
     * @var int
     */
    private $type;

    /**
     * Parameter constructor.
     * @param int|string $name
     * @param mixed $value
     * @param int $type
     */
    public function __construct(string $name, $value, int $type = \PDO::PARAM_STR)
    {
        $this->name = trim($name, ':');;
        $this->value = $value;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
