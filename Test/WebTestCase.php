<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;

/**
 * Base test class for your RAD applications.
 */
abstract class WebTestCase extends BaseTestCase
{
    static protected function getKernelClass()
    {
        return isset($_SERVER['KERNEL_CLASS'])
            ? $_SERVER['KERNEL_CLASS']
            : 'RadAppKernel';
    }
}
