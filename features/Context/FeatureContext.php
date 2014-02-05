<?php

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\BehaviorException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Behat\MinkExtension\Context\MinkAwareInterface;
use Symfony\Component\Yaml\Yaml;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext extends RawMinkContext implements SnippetAcceptingContext
{
    private $tmpDir;
    private $fs;
    private $app;

    /**
     * Initializes context. Every scenario gets its own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct()
    {
        $this->fs = new Filesystem;
        $this->tmpDir = __DIR__.'/fixtures/tmp';
        $this->writeContent($this->tmpDir.'/App/Resources/config/routing.yml');
        $this->app = new \fixtures\AppKernel;
    }

    /**
     * @BeforeSuite
     **/
    public static function removeTmp()
    {
        $fs = new Filesystem;
        $fs->remove(__DIR__.'/fixtures/tmp');
    }

    /**
     * @Given I write in :path:
     */
    public function iWriteIn($path, PyStringNode $class)
    {
        $path = $this->tmpDir.'/'.$path;
        $this->writeContent($path, $class);
    }

    /**
     * @Then :alias should be a registered form type
     */
    public function shouldBeARegisteredFormType($alias)
    {
        $this->app->boot();
        $this->app->getContainer()->get('form.factory')->create($alias);
    }

    /**
     * @Then :alias should not be a registered form type
     */
    public function shouldNotBeARegisteredFormType($alias)
    {
        $this->app->boot();
        if ($this->app->getContainer()->get('form.registry')->hasType($alias)) {
            throw new \LogicException(sprintf('Form type with alias %s was found', $alias));
        }
    }

    /**
     * @Then :alias should be a registered twig extension
     */
    public function shouldBeARegisteredTwigExtension($alias)
    {
        $this->app->boot();
        $twig = $this->app->getContainer()->get('twig');
        if (!$twig->hasExtension($alias)) {
            throw new \LogicException(sprintf('Twig extension with alias %s was not found.', $alias));
        }
    }

    /**
     * @Then :alias should not be a registered twig extension
     */
    public function shouldNotBeARegisteredTwigExtension($alias)
    {
        $this->app->boot();
        $twig = $this->app->getContainer()->get('twig');
        if ($twig->hasExtension($alias)) {
            throw new \LogicException(sprintf('Twig extension with alias %s was found.', $alias));
        }
    }

    /**
     * @Then :alias should be a registered validator
     */
    public function shouldBeARegisteredValidator($alias)
    {
        $this->app->boot();
        $this->app->getContainer()->get(sprintf('app.validator.constraints.%s_validator', $alias));
    }

    /**
     * @Then :alias should not be a registered validator
     */
    public function shouldNotBeARegisteredValidator($alias)
    {
        $this->app->boot();
        if ($this->app->getContainer()->has(sprintf('app.validator.constraints.%s_validator', $alias))) {
            throw new \LogicException(sprintf('Valdiator with alias %s was found.', $alias));
        }
    }

    /**
     * @When I visit :route page
     */
    public function visitRoute($route)
    {
        $this->app->boot();
        $url = $this
            ->app
            ->getContainer()
            ->get('router')
            ->generate($route)
        ;

        $this->getMink()->getSession()->visit($this->locatePath($url));
    }

    private function writeContent($path, $content = '')
    {
        $this->fs->mkdir(dirname($path));
        file_put_contents($path, $content);
    }
}
