<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\Guess;

class DataTypeGuesser implements FormTypeGuesserInterface
{
    private $data;

    public function setData($data = null)
    {
        $this->data = $data;
    }

    public function guessType($class, $property)
    {
        if ($property === '_id') {
            return new TypeGuess('hidden', array(), Guess::LOW_CONFIDENCE);
        }

        if (!isset($this->data->$property)) {
            return;
        }
        $data = $this->data->$property;

        $type = $this->getType($data);

        return new TypeGuess($type, array(), Guess::LOW_CONFIDENCE);
    }

    public function guessRequired($class, $property)
    {
    }

    public function guessPattern($class, $property)
    {
    }

    public function guessMaxLength($class, $property)
    {
    }

    public function guessMinLength($class, $property)
    {
    }

    private function getType($value)
    {
        if (is_object($value)) {
            switch (true) {
                case $value instanceof \DateTime:
                    return 'date';
                case $value instanceof \ArrayIterator:
                    return 'collection';
                default:
                    return 'text';
            }
        }

        $type = gettype($value);

        switch ($type) {
            case 'boolean':
                return 'checkbox';
            case 'array':
                return 'collection';
            case 'string':
            default:
                return 'text';
        }
    }
}
