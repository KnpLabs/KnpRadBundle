<?php

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Snippet\Context\TurnipSnippetsFriendlyInterface;
use Behat\Behat\Snippet\Context\RegexSnippetsFriendlyInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\BehaviorException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Behat\MinkExtension\Context\MinkDictionary;
use Behat\MinkExtension\Context\MinkAwareInterface;
use Symfony\Component\Yaml\Yaml;

class FeatureContext implements ContextInterface, MinkAwareInterface, TurnipSnippetsFriendlyInterface, RegexSnippetsFriendlyInterface
{
    private $tmpDir;
    private $fs;
    private $app;
    private $lastResponse;

    use MinkDictionary;

    /**
     * Initializes context. Every scenario gets its own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->fs = new Filesystem;
        $this->tmpDir = __DIR__.'/fixtures/tmp/App';
        $this->app = new \App\AppKernel('test', true);
        $this->app->boot();
    }

    /**
     * @BeforeScenario
     **/
    public function removeCache()
    {
        $this->fs->remove($this->tmpDir.'/../cache');
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
        $this->app->getContainer()->get('form.factory')->create($alias);
    }

    /**
     * @Then :alias should not be a registered form type
     */
    public function shouldNotBeARegisteredFormType($alias)
    {
        try {
            $this->app->getContainer()->get('form.factory')->create($alias);
        } catch (\Exception $e) {
            // all good
            return;
        }

        throw new \LogicException(sprintf('Form type with alias %s was found', $alias));
    }

    /**
     * @Then :alias should be a registered twig extension
     */
    public function shouldBeARegisteredTwigExtension($alias)
    {
        $this->app->getContainer()->get(sprintf('app.twig.%s_extension', $alias));

        $twig = $this->app->getContainer()->get('twig');
        $twig->setLoader(new \Twig_Loader_String());
        $twig->render(sprintf('{{ %s() }}', $alias));
    }

    /**
     * @Then :alias should not be a registered twig extension
     */
    public function shouldNotBeARegisteredTwigExtension($alias)
    {
        if ($this->app->getContainer()->has(sprintf('app.twig.%s_extension', $alias))) {
            throw new \LogicException(sprintf('Twig extension with alias %s was found.', $alias));
        }
    }

    /**
     * @Then :alias should be a registered validator
     */
    public function shouldBeARegisteredValidator($alias)
    {
        $this->app->getContainer()->get(sprintf('app.constraints.validator.%s_validator', $alias));
    }

    /**
     * @Then :alias should not be a registered validator
     */
    public function shouldNotBeARegisteredValidator($alias)
    {
        if ($this->app->getContainer()->has(sprintf('app.constraints.validator.%s_validator', $alias))) {
            throw new \LogicException(sprintf('Valdiator with alias %s was found.', $alias));
        }
    }

    /**
     * @Given /I add route for "([^"]*)":?$/
     */
    public function iAddRouteForController($controller, TableNode $defaultParams = null)
    {
        $defaults = array('_controller' => $controller);
        if ($defaultParams) {
            $defaults = array_merge($defaults, $defaultParams->getRowsHash());
            array_walk($defaults, function($value) {
                if (in_array($value, array('false', 'true'))) {
                    $value = $value === 'true';
                }
            });
        }

        fwrite(fopen($this->tmpDir.'/Resources/config/routing.yml', 'a+'), Yaml::dump(array(
            $controller => array(
                'pattern' => str_replace(':', '/', strtolower($controller)),
                'defaults' => $defaults,
            )
        )));
    }

    /**
     * @When I visit :route page
     */
    public function visitRoute($route)
    {
        $url = $this
            ->app
            ->getContainer()
            ->get('router')
            ->generate($route)
        ;

        $this->visit($url);
    }

    private function writeContent($path, $content)
    {
        $this->fs->mkdir(dirname($path));
        file_put_contents($path, $content);
    }
}
