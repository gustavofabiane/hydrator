<?php

declare(strict_types=1);

namespace GustavoFabiane\Hydrator;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class Hydrator implements HydratorInterface
{
    /**
     * In memory cache of hydrated class properties
     */
    private array $propertyMemory = [];

    /**
     * Hydrates the target with the data in given array
     *
     * @param string|object $target
     * @param array $data
     * @return object
     */
    public function hydrate($target, array $data): object
    {
        if (!is_object($target)) {
            $target = $this->newInstance($target);
        }

        $properties = $this->getObjectProperties($target);
        foreach ($properties as $property) {
            if (!array_key_exists($propertyName = $property->getName(), $data)) {
                continue;
            }
            
            $this->assertType($property, $data[$propertyName]);

            $property->setAccessible(true);
            $property->setValue($target, $data[$propertyName]);
        }

        return $target;
    }

    /**
     * Asserts that the given value matches the type of the property
     *
     * @param mixed $value
     * @param ReflectionProperty $property
     * @return void
     */
    private function assertType($value, ReflectionProperty $property): void
    {
        if (!$property->hasType()) {
            return;
        }

        /** @var ReflectionNamedType */
        $propertyType = $property->getType();

        $valueType = gettype($value);
        if(is_object($value)) {
            $valueType = get_class($value);
        }

        if ($valueType !== $propertyType->getName()) {
            throw new RuntimeException(sprintf(
                'Property %s::%s must be an \'%s\'', 
                $property->getDeclaringClass()->getName(),
                $property->getName(), 
                $propertyType->getName()
            ));
        }
    }

    /**
     * Initializes an object with no constructor
     *
     * @param string $class
     * @return object
     */
    private function newInstance(string $class): object
    {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
    }

    /**
     * Return an array of the object properties
     *
     * @param object $target
     * @return ReflectionProperty[]
     */
    private function getObjectProperties(object $target): array
    {
        if (!array_key_exists($class = get_class($target), $this->propertyMemory)) {
            $this->propertyMemory[$class] = (new ReflectionClass($class))->getProperties();
        }
        return $this->propertyMemory[$class];
    }
}
