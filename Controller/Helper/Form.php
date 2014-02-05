<?php

namespace Knp\RadBundle\Controller\Helper;

use Knp\RadBundle\Form\FormManager;

class Form
{
    private $manager;

    public function __construct(FormManager $manager)
    {
        $this->manager = $manager;
    }

    public function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->manager->createObjectForm($object, $purpose, $options);
    }

    public function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->manager->createBoundObjectForm($object, $purpose, $options);
    }
}
