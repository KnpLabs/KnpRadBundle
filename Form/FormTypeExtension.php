<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

use Knp\RadBundle\Form\DefaultFormCreator;

class FormTypeExtension extends AbstractTypeExtension
{
    private $formCreator;

    public function __construct(DefaultFormCreator $formCreator)
    {
        $this->formCreator = $formCreator;
    }

    public function getExtendedType()
    {
        return 'form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->formCreator);
    }
}

