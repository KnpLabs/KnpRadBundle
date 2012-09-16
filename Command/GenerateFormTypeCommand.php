<?php

namespace Knp\Bundle\RadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class GenerateFormTypeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('rad:generate:formtype')
            ->setDescription('Generates a form type.')
            ->addOption('fields', null, InputOption::VALUE_OPTIONAL, 'The form fields')
            ->addArgument('name', InputArgument::REQUIRED, 'The formtype name name')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getApplication()->getKernel();
        $bundle = $kernel->getBundle('App');
        $name   = $input->getArgument('name');
        $fields = $input->getOption('fields');
        $twig   = $this->createTwig();
        $dialog = $this->getHelperSet()->get('dialog');

        $formtype = ucfirst($name);
        $fields   = $fields !== null ? explode(',', $fields) : array();

        $class     = sprintf('%sType', str_replace('/', '\\', $formtype));
        $subns     = dirname(str_replace('\\', '/', $class));
        
        $namespace = sprintf('%s\Form%s',
            $bundle->getNamespace(), '.' !== $subns ? '\\'.$subns : ''
        );
        $classPath = sprintf('%s/Form/%s.php',
            $bundle->getPath(), str_replace('\\', '/', $class)
        );
        $class     = basename(str_replace('\\', '/', $class));
        $fqcn      = sprintf('%s\%s', $namespace, $class);
        $name      = basename(str_replace('\\', '/', $formtype));
        $name      = $this->underscore(strtolower($bundle->getNamespace()).$name); 

        $output->writeLn(sprintf('- <comment>App:%s</comment> form type:',
            str_replace('/', '\\', $formtype)
        ));

        if (!class_exists($fqcn)) {
            $this->writeFile($classPath, $twig->render('formtype.php.twig', array(
                'namespace' => $namespace,
                'class'     => $class,
                'fields'    => $fields,
                'name'      => $name
            )));

            $output->writeLn('  class <info>generated</info>');
        } else {
            $output->writeLn('  class <info>already exists</info>');
        }
    }

    protected function writeFile($path, $data, $flags = null)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $data, $flags);
    }

    protected function underscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    protected function appendToFile($path, $line = null, $data, $flags = null)
    {
        if (null !== $line) {
            $content = file($path, $flags);
            $content = array_replace($content, array($line => $content[$line].$data));
            $content = implode('', $content);
        } else {
            $content = file_get_contents($path);
            $content = (!empty($content) ? $content."\n" : '').$data;
        }

        file_put_contents($path, $content, $flags);
    }

    protected function createTwig()
    {
        $kernel    = $this->getApplication()->getKernel();
        $directory = $kernel->locateResource('@KnpRadBundle/Resources/skeleton/controller');

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
}
