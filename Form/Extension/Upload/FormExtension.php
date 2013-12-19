<?php

namespace Knp\RadBundle\Form\Extension\Upload;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Knp\RadBundle\Form\RecursiveChildrenIterator;

class FormExtension extends AbstractTypeExtension
{
    private $webDir;

    public function __construct($webDir)
    {
        $this->webDir = $webDir;
    }

    public function getExtendedType()
    {
        return 'form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'moveUploadedFiles']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'listens_uploads' => false,
        ]);
    }

    public function moveUploadedFiles(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->isRoot()) {
            return;
        }

        if (!$form->isValid()) {
            return;
        }

        $childrenIterator = new RecursiveChildrenIterator($form);
        foreach (new \RecursiveIteratorIterator($childrenIterator) as $child) {
            if (!$child->getData() instanceof UploadedFile) {
                continue;
            }
            $this->moveUploadedFile($child);
        }
    }

    private function moveUploadedFile(FormInterface $form)
    {
        $uploadedFile = $form->getData();

        $targetDirectory = $form->getConfig()->getOption('upload_directory');
        $targetPath = $form->getConfig()->getOption('target_path');

        $file = $uploadedFile->move($this->webDir.'/'.$targetDirectory, $uploadedFile->getClientOriginalName());

        $accessor = new PropertyAccessor;
        $parentData = $this->findParentReference($form)->getData();
        $accessor->setValue($parentData, $targetPath, $targetDirectory.'/'.$file->getBasename());
        $accessor->setValue($parentData, $form->getPropertyPath(), null); // TODO option ?
    }

    private function findParentReference(FormInterface $form)
    {
        do {
            $parent = $form->getParent();
        }
        while (!$parent->getOption('listens_uploads') || $parent->isRoot());

        return $parent;
    }
}
