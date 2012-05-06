<?php

namespace Knp\Bundle\RadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class GenerateControllerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('rad:generate:controller')
            ->setDescription('Generates a controller (or action) with its service routing.')
            ->addArgument('name', InputArgument::REQUIRED, 'The controller name name')
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
        $twig   = $this->createTwig();
        $dialog = $this->getHelperSet()->get('dialog');

        $controller = $name;
        $action     = null;
        if (2 === count($parts = explode(':', $name))) {
            list($controller, $action) = $parts;
        }

        $class     = sprintf('%sController', str_replace('/', '\\', $controller));
        $subns     = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));
        $namespace = sprintf('%s\Controller%s',
            $bundle->getNamespace(), '.' !== $subns ? '\\'.$subns : ''
        );
        $classPath = sprintf('%s/Controller/%s.php',
            $bundle->getPath(), str_replace('\\', '/', $class)
        );
        $class     = basename(str_replace('\\', '/', $class));
        $fqcn      = sprintf('%s\%s', $namespace, $class);
        $name      = basename(str_replace('\\', '/', $controller));

        $output->writeLn(sprintf('- <comment>App:%s</comment> controller:',
            str_replace('/', '\\', $controller)
        ));

        if (!class_exists($fqcn)) {
            $this->writeFile($classPath, $twig->render('controller.php.twig', array(
                'namespace' => $namespace,
                'class'     => $class,
                'name'      => $name
            )));

            $output->writeLn('  class <info>generated</info>');
        } else {
            $output->writeLn('  class <info>already exists</info>');
        }

        if (null === $action) {
            return;
        }

        $output->writeLn(sprintf("\n".'- <comment>App:%s:%s</comment> action:',
            str_replace('/', '\\', $controller), $action
        ));

        $refl = new \ReflectionClass($fqcn);
        if (!$refl->hasMethod($action)) {
            $prefix = '';
            $lineToAppend = $refl->getEndLine();
            foreach ($refl->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($refl->getName() !== $method->getDeclaringClass()->getName()) {
                    continue;
                }
                $prefix = "\n";
                $lineToAppend = $method->getEndLine() + 1;
            }

            $this->appendToFile($refl->getFileName(), $lineToAppend - 2,
                $prefix.$twig->render('action.php.twig', array('action' => $action))
            );

            $output->writeLn('  method <info>generated</info>');
        } else {
            $output->writeLn('  method <info>already exists</info>');
        }

        $viewPath = sprintf('%s/Resources/views/%s/%s.html.twig',
            $bundle->getPath(), str_replace('\\', '/', $controller), $action
        );

        $output->writeLn(sprintf("\n".'- <comment>App:%s:%s.html.twig</comment> view:',
            str_replace('\\', '/', $controller), $action
        ));

        if (!file_exists($viewPath)) {
            $this->writeFile($viewPath, $twig->render('view.html.twig'));

            $output->writeLn('  view <info>generated</info>');
        } else {
            $output->writeLn('  view <info>already exists</info>');
        }

        $routingPath = sprintf('%s/Resources/config/routing.yml', $bundle->getPath());
        $routeName   = $this->underscore($name).'_'.$this->underscore($action);
        $routePath   = '/'.$this->underscore($name).'/'.$this->underscore($action);
        $controller  = 'App:'.str_replace('/', '\\', $controller).':'.$action;

        $output->writeLn(sprintf("\n".'- <comment>%s</comment> route:', $routePath));

        if (!file_exists($routingPath)) {
            $output->writeLn(sprintf('  file <info>%s</info> does not exist.', $routingPath));

            if (!$dialog->askConfirmation($output, '  do you want me to create it? [Y/n] ', 'y')) {
                return;
            }

            $this->writeFile($routingPath, '');
        }

        $content = file_get_contents($routingPath);
        if (!preg_match('/'.preg_quote($controller, '/').'[^a-zA-Z0-9]/', $content)) {
            $this->appendToFile($routingPath, null, $twig->render('routing.yml.twig', array(
                'routeName'  => $routeName,
                'routePath'  => $routePath,
                'controller' => $controller
            )));

            $output->writeLn('  route <info>added</info>');
        } else {
            $output->writeLn('  route <info>already exists</info>');
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
