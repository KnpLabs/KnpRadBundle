<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\HttpKernel;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Doctrine\Common\Annotations\AnnotationRegistry;

use Composer\Autoload\ClassLoader;

use Knp\Bundle\RadBundle\DependencyInjection\Loader\ArrayLoader;
use Knp\Bundle\RadBundle\HttpKernel\Bundle\AppBundle;
use Knp\Bundle\RadBundle\KnpRadBundle;

/**
 * RadBundle custom kernel with support for application bundles
 * and new configuration system.
 */
class RadKernel extends Kernel
{
    static public $projectRootDir;
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct($environment, $debug)
    {
        if ('Knp\\Bundle\\RadBundle\\HttpKernel\\RadKernel' === get_class($this)) {
            throw new \RuntimeException(
                "You can not use Knp\\Bundle\\RadBundle\\HttpKernel\\RadKernel as your application kernel.\n".
                "Call RadKernel::createAppKernel(\$loader, '$environment', $debug) to create specific application kernel."
            );
        }

        parent::__construct($environment, $debug);

        $this->configuration = $this->initConfiguration();
        $this->configuration->load();
    }

    /**
     * Creates RAD app kernel, which you can use to manage your app.
     *
     * Loads intl, swift and then requires/initializes/returns custom
     * app kernel.
     *
     * @param ClassLoader $loader      Composer class loader
     * @param string      $environment Environment name
     * @param Boolean     $debug       Debug mode?
     *
     * @return RadAppKernel
     */
    static public function createAppKernel(ClassLoader $loader, $environment, $debug)
    {
        require_once __DIR__.'/RadAppKernel.php';
        if (null === self::$projectRootDir) {
            self::$projectRootDir = realpath(__DIR__.'/../../../../../../..');
        }

        $autoloadIntl = function($rootDir) use($loader) {
            if (!function_exists('intl_get_error_code')) {
                require_once $rootDir.
                    '/vendor/symfony/symfony/src/'.
                    'Symfony/Component/Locale/Resources/stubs/functions.php';
                $loader->add(null, $rootDir.
                    '/vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs'
                );
            }
        };
        $autoloadSwift = function($rootDir) use($loader) {
            require_once $rootDir.'/vendor/swiftmailer/swiftmailer/lib/classes/Swift.php';
            \Swift::registerAutoload($rootDir.
                '/vendor/swiftmailer/swiftmailer/lib/swift_init.php'
            );
        };

        $loader->add(null, self::$projectRootDir.'/src');
        if (file_exists($custom = self::$projectRootDir.'/app/autoload.php')) {
            require($custom);
        } else {
            $autoloadIntl(self::$projectRootDir);
            $autoloadSwift(self::$projectRootDir);
        }

        return new \RadAppKernel($environment, $debug);
    }

    /**
     * Returns project root directory.
     *
     * @return string
     */
    public function getProjectRootDir()
    {
        return self::$projectRootDir;
    }

    /**
     * Returns configuration root directory (app folder).
     *
     * @return string
     */
    public function getRootDir()
    {
        return $this->getProjectRootDir().'/app';
    }

    /**
     * Returns configuration directory.
     *
     * @return string
     */
    public function getConfigDir()
    {
        return $this->getRootDir();
    }

    /**
     * Returns project directory.
     *
     * @return string
     */
    public function getProjectDir()
    {
        return sprintf('%s/src/%s',
            $this->getProjectRootDir(),
            str_replace('\\', '/', $this->configuration->getProjectName())
        );
    }

    /**
     * Returns web directory.
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->getProjectRootDir().'/web';
    }

    /**
     * Returns logs directory.
     *
     * @return string
     */
    public function getLogDir()
    {
        return $this->getProjectRootDir().'/logs';
    }

    /**
     * Returns cache directory.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->getProjectRootDir().'/cache/'.$this->environment;
    }

    /**
     * Returns configuration instance.
     *
     * @return KernelConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = $this->configuration->getBundles($this);

        // Add KnpRadBundle and AppBundle automatically
        $bundles[] = new KnpRadBundle($this);
        $bundles[] = new AppBundle(
            $this->configuration->getProjectName(), $this->getProjectRootDir().'/src'
        );

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $configs = Finder::create()
            ->name('*.yml')
            ->in($this->getConfigDir().'/config');

        foreach ($configs as $file) {
            $this->loadConfigFile($file, basename($file, '.yml'), $loader);
        }

        foreach ($this->configuration->getImports() as $file) {
            if (file_exists($file = $this->getConfigDir().'/'.$file)) {
                $loader->load($file);
            }
        }
    }

    /**
     * Returns a loader for the container.
     *
     * @param ContainerInterface $container The service container
     *
     * @return DelegatingLoader The loader
     */
    protected function getContainerLoader(ContainerInterface $container)
    {
        foreach ($this->configuration->getParameters() as $key => $val) {
            $container->setParameter($key, $val);
        }

        $locator = new FileLocator($this);
        $resolver = new LoaderResolver(array(
            new ArrayLoader($container),
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        foreach ($this->getConfiguration()->getResources() as $resource) {
            $container->addResource($resource);
        }

        parent::dumpContainer($cache, $container, $class, $baseClass);
    }

    /**
     * Initializes kernel configuration instance.
     *
     * @return KernelConfiguration
     */
    protected function initConfiguration()
    {
        return new KernelConfiguration(
            $this->getEnvironment(),
            $this->getConfigDir(),
            $this->getCacheDir(),
            $this->isDebug()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernelParameters()
    {
        return array_merge(
            array(
                'kernel.project_root' => $this->getProjectRootDir(),
                'kernel.project_name' => $this->getConfiguration()->getProjectName(),
                'kernel.project_dir'  => $this->getProjectDir(),
                'kernel.config_dir'   => $this->getProjectRootDir(),
                'kernel.web_dir'      => $this->getWebDir(),
            ),
            parent::getKernelParameters()
        );
    }

    /**
     * Loads DIC configuration file.
     *
     * @param string          $file   File path
     * @param string          $name   Config name
     * @param LoaderInterface $loader Container loader
     */
    protected function loadConfigFile($file, $name = null, LoaderInterface $loader)
    {
        $configs = Yaml::parse($file);
        $env     = $this->getEnvironment();

        // try to catch a common error, where you forget to put config under an env key
        if (isset($configs[$name])) {
            throw new InvalidConfigurationException(sprintf(
                'The key "%s" appears at the root of "%s", but this should be an environment name or "all". You can probably just replace "%s" with "all" to fix the problem. Cheers!',
                $name,
                basename($file),
                $name
            ));
        }

        if (isset($configs['all'])) {
            $config = $name ? array($name => $configs['all']) : $configs['all'];
            $loader->load($config, $file);
        }
        if (isset($configs[$env])) {
            $config = $name ? array($name => $configs[$env]) : $configs[$env];
            $loader->load($config, $file);
        }
    }
}
