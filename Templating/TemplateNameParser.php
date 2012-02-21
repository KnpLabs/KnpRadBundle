<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser as BaseNameParser;

use Knp\Bundle\RadBundle\Bundle\ApplicationBundle;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Extends basic name parser with application bundle short notation.
 */
class TemplateNameParser extends BaseNameParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $parsed = parent::parse($name);

        // if application bundle - use custom structure reference
        if ('App' === $parsed->get('bundle')) {
            return $this->cache[$name] = new TemplateReference(
                $parsed->get('bundle'),
                $parsed->get('controller'),
                $parsed->get('name'),
                $parsed->get('format'),
                $parsed->get('engine')
            );
        }

        return $parsed;
    }
}
