<?php

namespace Knp\Bundle\RadBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Knp\Bundle\RadBundle\EventListener\ViewListener;

class ViewListenerTest extends \PHPUnit_Framework_TestCase
{
    private $templating;
    private $event;
    private $request;
    private $attributes;

    protected function setUp()
    {
        $this->templating = $this->getMockBuilder(
            'Symfony\Bundle\FrameworkBundle\Templating\EngineInterface'
        )->disableOriginalConstructor()->getMock();

        $this->event = $this->getMockBuilder(
            'Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent'
        )->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder(
            'Symfony\Component\HttpFoundation\Request'
        )->disableOriginalConstructor()->getMock();

        $this->request->attributes = new Attributes();

        $this->event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
    }

    public function testDontTouchNonAppControllers()
    {
        $listener = new ViewListener('MyProject', $this->templating, 'twig');

        $this->request
            ->expects($this->never())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'));

        $this->request
            ->attributes->v['_controller'] = 'SomeBundle\\Controller\\MyController::index';

        $this->event
            ->expects($this->never())
            ->method('getControllerResult');

        $this->event
            ->expects($this->never())
            ->method('setResponse');

        $this->templating
            ->expects($this->never())
            ->method('renderResponse');

        $listener->onKernelView($this->event);
    }

    public function testSimpleHtmlTemplateRendering()
    {
        $listener = new ViewListener('MyProject', $this->templating, 'twig');

        $this->request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'));

        $this->request
            ->attributes->v['_controller'] = 'MyProject\\Controller\\MyController::index';

        $this->event
            ->expects($this->once())
            ->method('getControllerResult')
            ->will($this->returnValue(array('var' => 'controller result')));

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with(new Response('Hello, template'));

        $this->templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with('App:My:index.html.twig', array('var' => 'controller result'))
            ->will($this->returnValue(new Response('Hello, template')));

        $listener->onKernelView($this->event);
    }

    public function testNamespacedHtmlTemplateRendering()
    {
        $listener = new ViewListener('My\\Super\\Project', $this->templating, 'twig');

        $this->request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'));

        $this->request
            ->attributes->v['_controller'] = 'My\\Super\\Project\\Controller\\My\\NamespacedController::index';

        $this->event
            ->expects($this->once())
            ->method('getControllerResult')
            ->will($this->returnValue(array('var' => 'controller result')));

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with(new Response('Hello, template'));

        $this->templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with('App:My/Namespaced:index.html.twig', array('var' => 'controller result'))
            ->will($this->returnValue(new Response('Hello, template')));

        $listener->onKernelView($this->event);
    }

    public function testLongActionNameTemplateRendering()
    {
        $listener = new ViewListener('Controller', $this->templating, 'twig');

        $this->request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'));

        $this->request
            ->attributes->v['_controller'] = 'Controller\Controller\MyController::indexAction';

        $this->event
            ->expects($this->once())
            ->method('getControllerResult')
            ->will($this->returnValue(array('var' => 'controller result')));

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with(new Response('Hello, template'));

        $this->templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with('App:My:index.html.twig', array('var' => 'controller result'))
            ->will($this->returnValue(new Response('Hello, template')));

        $listener->onKernelView($this->event);
    }

    public function testJsonRequestTemplateRendering()
    {
        $listener = new ViewListener('JSON\\Controller', $this->templating, 'twig');

        $this->request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('json'));

        $this->request
            ->attributes->v['_controller'] = 'JSON\\Controller\\Controller\\MyController::indexAction';

        $this->event
            ->expects($this->once())
            ->method('getControllerResult')
            ->will($this->returnValue(array('var' => 'controller result')));

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with(new Response('Hello, template'));

        $this->templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with('App:My:index.json.twig', array('var' => 'controller result'))
            ->will($this->returnValue(new Response('Hello, template')));

        $listener->onKernelView($this->event);
    }
}

class Attributes
{
    public $v = array();

    public function get($key)
    {
        return $this->v[$key];
    }
}
