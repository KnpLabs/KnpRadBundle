<?php

namespace Knp\RadBundle\Form\Extension\Upload;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\PropertyAccess\PropertyPath;

class FileExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'file';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'upload_directory',
            'target_path',
        ]);

        $resolver->setAllowedTypes([
            'upload_directory' => 'string',
            'target_path'      => ['string', 'Symfony\Component\PropertyAccess\PropertyPath'],
        ]);

        $resolver->setNormalizers([
            'target_path' => function(Options $options, $targetPath) {
                if (!$targetPath instanceof PropertyPath) {
                    return new PropertyPath($targetPath);
                }

                return $targetPath;
            },
        ]);
    }
}
