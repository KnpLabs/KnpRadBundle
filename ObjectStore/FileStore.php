<?php

namespace Knp\RadBundle\ObjectStore;

class FileStore implements ObjectStoreInterface
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function findAll()
    {
        if (!file_exists($this->filename)) {
            return array();
        }

        $contents = unserialize(file_get_contents($this->filename));

        return is_array($contents) ? $contents : array();
    }

    public function store($object)
    {
        $contents = $this->findAll();
        $contents[] = $object;

        file_put_contents($this->filename, serialize($contents));
    }

    public function clear()
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
