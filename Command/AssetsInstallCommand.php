<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand as BaseCommand;

use Knp\Bundle\RadBundle\HttpKernel\Bundle\AppBundle;

/**
 * Redefines Symfony2 install command to support application bundles.
 */
class AssetsInstallCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory (usually "web")', 'web'),
            ))
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
            ->setDescription('Installs bundles web assets under a public web directory')
            ->setHelp(<<<EOT
The <info>assets:install</info> command installs bundle assets into a web
directory.

<info>php app/console assets:install</info>

A "bundles" directory will be created inside the target directory, and the
"Resources/public" directory of each bundle will be copied into it.

To create a symlink to each bundle instead of copying its assets, use the
<info>--symlink</info> option:

<info>php app/console assets:install --symlink</info>

To make symlink relative, add the <info>--relative</info> option:

<info>php app/console assets:install --symlink --relative</info>

EOT
            )
            ->setName('assets:install')
        ;
    }
}
