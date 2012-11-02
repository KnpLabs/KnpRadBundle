<?php

namespace Knp\RadBundle\Form;

class FormManager
{
    private $creators = array();

    public function createObjectForm($object, $purpose = null, array $options = array())
    {
        foreach ($this->creators as $creator) {
            if ($form = $creator->create($object, $purpose, $options)) {
                return $form;
            }
        }

        throw new \RuntimeException(sprintf('The form manager was unable to create the form. Please, make sure you have correctly registered one that fit your need.'));
    }

    public function registerCreator(FormCreatorInterface $creator)
    {
        $this->creators[] = $creator;
    }

    public function getCreators()
    {
        return $this->creators;
    }
}
