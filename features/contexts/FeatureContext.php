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

/**
 * Behat context class.
 */
class FeatureContext implements ContextInterface, SnippetsFriendlyInterface
{
    private $tmpDir;
    private $lastResponse;

    /**
     * Initializes context. Every scenario gets it's own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->tmpDir = __DIR__.'/fixtures/tmp/App';
        require_once $this->tmpDir.'/../../AppKernel.php';

        $this->app = new AppKernel('test', false);
        $this->app->boot();
        $this->fs = new Filesystem;
    }

    /**
     * @BeforeScenario
     */
    public function clearFixtures()
    {
        $this->fs->remove($this->tmpDir.'/Form');
        $this->fs->remove($this->tmpDir.'/Controller');
        $this->fs->remove($this->tmpDir.'/Resources');
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
        file_put_contents($path, $content);
        if ('.php' === substr($path, -4)) {
            require $path;
        }
    }
}
