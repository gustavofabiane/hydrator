<?php

declare(strict_types=1);

namespace GustavoFabiane\Hydrator;

use GustavoFabiane\Hydrator\Hydrator;
use GustavoFabiane\Hydrator\HydratorInterface;

final class Hydrate
{
    /**
     * Hydrator implementation
     */
    private HydratorInterface $hydrator;

    /**
     * Global singleton instance
     */
    private static self $instance;

    /**
     * Hydrate constructor
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator = null)
    {
        $this->hydrator = $hydrator ?: new Hydrator();
    }

    /**
     * Set this instance as a global singleton
     *
     * @return void
     */
    public function setGlobal(): void
    {
        static::$instance = $this;
    }

    /**
     * Initializes and hydrate an object of the given class name
     *
     * @param string $target
     * @param array $data
     * @return object
     */
    public function create($target, array $data): object
    {
        return $this->hydrator->hydrate($target, $data);
    }

    /**
     * Hydrate an object
     *
     * @param object $target
     * @param array $data
     * @return object
     */
    public function instance(object $target, array $data): object
    {
        return $this->hydrator->hydrate($target, $data);
    }

    /**
     * Use the global instance to handle calls to object methods
     *
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function __callStatic(string $name, array $parameters)
    {
        return static::$instance->{$name}(...$parameters);
    }
}
