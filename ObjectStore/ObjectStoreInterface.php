<?php

namespace Knp\RadBundle\ObjectStore;

interface ObjectStoreInterface
{
    /**
     * Returns all the stored objects
     *
     * @return array
     */
    public function findAll();

    /**
     * Stores the given object
     *
     * @param mixed $object
     */
    public function store($object);

    /**
     * Clears the store
     */
    public function clear();
}
