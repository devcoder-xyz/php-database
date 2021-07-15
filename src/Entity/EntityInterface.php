<?php

namespace DevCoder\DB\Entity;

use DevCoder\DB\Mapping\MappingInterface;

interface EntityInterface {
    public function getId(): ?int;

    /**
     * @return array<MappingInterface>
     */
    public static function getColumns(): array;
    public static function getRepository(): string;
}
