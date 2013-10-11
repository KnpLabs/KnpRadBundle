<?php

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Snippet\Context\SnippetsFriendlyInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Behat context class.
 */
class FeatureContext implements ContextInterface, SnippetsFriendlyInterface
{
    /**
     * Initializes context. Every scenario gets it's own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        require_once __DIR__.'/fixtures/AppKernel.php';
        $this->app = new AppKernel('test', true);
        $this->fs = new Filesystem;
    }

    /**
     * @Given I write in :path:
     */
    public function iWriteIn($path, PyStringNode $class)
    {
        $path = __DIR__.'/fixtures/'.$path;
        $this->fs->mkdir(dirname($path));
        file_put_contents($path, $class);
        require $path;
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
        try {
            $this->app->getContainer()->get('form.factory')->create($alias);
        } catch (\Exception $e) {
            // all good
            return;
        }

        throw new \LogicException(sprintf('Form type with alias %s was found', $alias));
    }
}
