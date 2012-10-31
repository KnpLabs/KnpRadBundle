<?php

namespace Knp\RadBundle\Form;

interface FormCreatorInterface
{
    public function create($object, $purpose = null, array $options = array());
}
