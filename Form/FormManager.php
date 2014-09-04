<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\HttpFoundation\RequestStack;

class FormManager
{
    private $requestStack;
    private $creators;

    public function __construct(RequestStack $requestStack)
    {
        $this->creators     = new \SplPriorityQueue;
        $this->requestStack = $requestStack;
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
        if (!$this->getRequest()->isMethodSafe()) {
            $options = array_merge(array('method' => $this->getRequest()->getMethod()), $options);
        }
        $form = $this->createObjectForm($object, $purpose, $options);
        $form->handleRequest($this->getRequest());

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

    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
