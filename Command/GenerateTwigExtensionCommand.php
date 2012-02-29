<?php

namespace Knp\Bundle\RadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class GenerateTwigExtensionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('rad:generate:twig-extension')
            ->setDescription('Generates a Twig extension with its service definition.')
            ->addArgument('name', InputArgument::REQUIRED, 'The extension name')
            ->addOption('class', null, InputOption::VALUE_OPTIONAL, 'The class name')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel    = $this->getApplication()->getKernel();
        $bundle    = $kernel->getBundle('App');
        $name      = $input->getArgument('name');
        $class     = $input->getOption('class') ?: sprintf('%sExtension', $this->classify($name));
        $namespace = sprintf('%s\Twig', $bundle->getNamespace());
        $fqcn      = sprintf('%s\%s', $namespace, $class);
        $twig      = $this->createTwig();
        $dialog    = $this->getHelperSet()->get('dialog');


        // generate the class
        $classPath = sprintf('%s/Twig/%s.php', $bundle->getPath(), $class);
        $classData = $twig->render('extension.php.twig', array(
            'namespace' => $namespace,
            'class'     => $class,
            'name'      => $name
        ));

        $output->writeLn(sprintf('- <comment>%s</comment> extension:', $fqcn));

        if (file_exists($classPath)) {
            $output->writeLn('  class <info>already exists</info>');
        } else {
            $this->writeFile($classPath, $classData);
            $output->writeLn('  class <info>generated</info>');
        }

        // generate the service definition
        $servicesPath = sprintf('%s/Resources/config/services.yml', $bundle->getPath());

        $output->writeLn(sprintf("\n".'- <comment>app.twig.%s_extension</comment> service:',
            strtolower($name)
        ));

        if (file_exists($servicesPath)) {
            $servicesData = $twig->render('services.yml.twig', array(
                'name'          => $name,
                'fqcn'          => $fqcn,
                'bundle_alias'  => Container::underscore($bundle->getName()),
                'newFile'       => false,
            ));
            $content = file_get_contents($servicesPath);

            if (!preg_match('/'.preg_quote('app.twig.'.strtolower($name).'_extension', '/').'/', $content)) {
                $this->writeFile($servicesPath, $servicesData, FILE_APPEND);
                $output->writeLn('  definition <info>added</info>');
            } else {
                $output->writeLn('  definition <info>already exists</info>');
            }
        } else {
            $output->writeLn(sprintf('  file <info>%s</info> does not exist.', $servicesPath));

            $servicesData = $twig->render('services.yml.twig', array(
                'name'         => $name,
                'fqcn'         => $fqcn,
                'bundle_alias' => $bundle->getName(),
                'newFile'      => true,
            ));

            if ($dialog->askConfirmation($output, '  do you want me to create it? [Y/n] ', 'y')) {
                $this->writeFile($servicesPath, $servicesData);

                $output->writeLn('  definition <info>added</info>');
            } else {
                $output->writeLn(sprintf(<<<EOT

<info>The service definition was not written.
You can manually create it:</info>

%s
EOT
                    ,
                    $servicesData
                ));
            }
        }
    }

    private function writeFile($path, $data, $flags = null)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $data, $flags);
    }

    private function createTwig()
    {
        $kernel    = $this->getApplication()->getKernel();
        $directory = $kernel->locateResource('@KnpRadBundle/Resources/skeleton/twig_extension');

        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($directory),
            array(
                'debug'            => true,
                'cache'            => false,
                'strict_variables' => true,
                'autoescape'       => false,
            )
        );
    }

    private function classify($word)
    {
        return str_replace(' ', '', ucwords(strtr($word, '_-', '  ')));
    }
}
