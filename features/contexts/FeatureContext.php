<?php

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Snippet\Context\SnippetsFriendlyInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Exception\BehaviorException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class FeatureContext implements ContextInterface, SnippetsFriendlyInterface
{
    private $tmpDir;
    private $wroteContents = array();
    private $fs;
    private $app;
    private $lastResponse;

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
     */
    public function clearCache()
    {
        $this
            ->app
            ->getContainer()
            ->get('cache_clearer')
            ->clear($this->tmpDir.'/../cache/test')
        ;
        // $this->fs->remove($this->tmpDir.'/../cache/test');
    }

    /**
     * @AfterScenario
     */
    public function clearFixtures()
    {
        foreach ($this->wroteContents as $path) {
            $this->fs->remove($path);
        }
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
     * @Given I add route for :controller
     */
    public function iAddRouteForController($controller)
    {
        $this
            ->app
            ->getContainer()
            ->get('router')
            ->getRouteCollection()
            ->add($controller, new Route(
                str_replace(':', '/', strtolower($controller)),
                array('_controller' => $controller)
            ))
        ;
    }

    /**
     * @Given I write in :controller controller:
     */
    public function iWriteInController($controller, PyStringNode $code)
    {
        $path = $this->tmpDir.'/Controller/'.$controller.'Controller.php';
        $code = <<<CONTROLLER
<?php

namespace App\Controller;

use Knp\RadBundle\Controller\Controller;

class {$controller}Controller extends Controller
{
{$code}
}
CONTROLLER;

        $this->writeContent($path, $code);
    }

    /**
     * @When I visit :route page
     */
    public function iVisitRoute($route)
    {
        $url = $this
            ->app
            ->getContainer()
            ->get('router')
            ->generate($route)
        ;
        $request = Request::create($url);
        $this->lastResponse = $this->app->handle($request);
    }

    /**
     * @Then I should see :text
     */
    public function iShouldSee($text)
    {
        if (false === strpos($this->lastResponse->getContent(), $text)) {
            throw new BehaviorException(sprintf(
                '"%s" not found in "%s".',
                $text,
                $this->lastResponse->getContent()
            ));
        }
    }

    private function writeContent($path, $content)
    {
        $this->fs->mkdir(dirname($path));
        if (file_put_contents($path, $content)) {
            $this->wroteContents[] = $path;
        }
    }
}
