<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\HttpFoundation\Request;

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

    public function createBoundObjectForm($object, Request $request, $purpose = null, array $options = array())
    {
        return $this->createObjectForm($object, $purpose, $options)->bind($request);
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
