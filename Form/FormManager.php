<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\HttpFoundation\Request;

class FormManager
{
    private $request;
    private $creators;

    public function __construct(Request $request)
    {
        $this->creators = new \SplPriorityQueue;
        $this->request  = $request;
    }

    public function createObjectForm($object, $purpose = null, array $options = array())
    {
        foreach ($this->getCreators() as $creator) {
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
            $form->handleRequest($this->request);
        }

        return $form;
    }

    public function registerCreator(FormCreatorInterface $creator, $priority = 0)
    {
        $this->creators->insert($creator, $priority);
    }

    public function getCreators()
    {
        return array_values(iterator_to_array(clone $this->creators));
    }
}
