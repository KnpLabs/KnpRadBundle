<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser as BaseNameParser;

/**
 * Extends Symfony2 ControllerNameParser with support
 * of application bundle short notation.
 */
class ControllerNameParser extends BaseNameParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($controller)
    {
        $parsed = parent::parse($controller);
        $parts  = explode(':', $controller);

        if (3 === count($parts) && 'App' === $parts[0]) {
            $parts = explode('::', $parsed);
            if (method_exists($parts[0], $parts[1])) {
                return $parsed;
            }
            if (method_exists($parts[0], $action = substr($parts[1], 0, -6))) {
                return $parts[0].'::'.$action;
            }
        }

        return $parsed;
    }
}
