<?php

declare(strict_types=1);

namespace GustavoFabiane\Hydrator;

interface HydratorInterface
{
    /**
     * Hydrates the target with the data in given array
     *
     * @param string|object $target
     * @param array $data
     * @return object
     */
    public function hydrate($target, array $data): object;
}