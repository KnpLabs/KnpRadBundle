<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\DataFixtures\ReferenceRepository;

class ReferenceManipulator
{
    public function __construct(ReferenceRepository $referenceRepository)
    {
        $this->referenceRepository = $referenceRepository;
    }

    public function createReferenceName($className, array $attributes = array())
    {
         $className     = join('', array_slice(explode('\\', $className), -1));
         $referenceId   = reset($attributes);
         $referenceName = $referenceId ? sprintf('%s:%s', $className, $referenceId) : $className;

         return $this->generateUniqueReferenceName($referenceName);
    }

    private function generateUniqueReferenceName($referenceName)
    {
        if ($this->referenceRepository->hasReference($referenceName)) {
            $referenceName = $this->incrementReferenceName($referenceName);

            return $this->generateUniqueReferenceName($referenceName);
        }

        return $referenceName;
    }

    private function incrementReferenceName($referenceName)
    {
        if (0 === preg_match('#^(.*)(\d+)$#', $referenceName)) {
            if (1 === preg_match('#^(\w+):(\w+)#', $referenceName)) {
                return sprintf('%s-1', $referenceName);
            } else {
                return sprintf('%s:1', $referenceName);
            }
        }

        return preg_replace_callback(
            '#^(.*)(\d+)$#',
            function ($matches) { return $matches[1].intval($matches[2]+1); },
            $referenceName
        );
    }
}
