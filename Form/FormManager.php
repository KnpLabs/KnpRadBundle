<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\HttpFoundation\Request;

class FormManager
{
    private $request;
    private $creators = array();

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createObjectForm($object, $purpose = null, array $options = array())
    {
        foreach ($this->creators as $creator) {
            if ($form = $creator->create($object, $purpose, $options)) {
                return $form;
            }
        }

        throw new \RuntimeException(sprintf('The form manager was unable to create the form. Please, make sure you have correctly registered one that fit your need.'));
    }

    public function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        $form = $this->createObjectForm($object, $purpose, $options);

        if (!$this->request->isMethodSafe()) {
            $form->bind($this->request);
        }

        return $form;
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
