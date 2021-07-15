<?php

namespace DevCoder\DB\Mapping;

interface MappingInterface
{
    public function getProperty(): string;
    public function getName(): string;
}