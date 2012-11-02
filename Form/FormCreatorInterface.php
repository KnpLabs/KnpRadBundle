<?php

namespace Knp\RadBundle\Form;

interface FormCreatorInterface
{
    /**
     * @param object $object The initial object
     * @param string $purpose The purpose of the form
     * @param array  $options The options
     *
     * @return Symfony\Component\Form\Form
     */
    public function create($object, $purpose = null, array $options = array());
}
