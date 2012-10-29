<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('KnpRadBundle:Default:index.html.twig', array('name' => $name));
    }
}
