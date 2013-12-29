<?php

namespace Knp\RadBundle\Controller;

use App\Doctrine\Paginator;
use Doctrine\ORM\Tools\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Knp\RadBundle\Controller\Controller;

class CrudController extends Controller
{
    public function indexAction(Request $request, QueryBuilder $qb)
    {
        $paginator = $this->getPaginator($qb);

        return [
            'objects' => $paginator,
        ];
    }

    public function newAction($newObject, Form $newForm)
    {
        if ($newForm->isValid()) {
            $this->persist($newForm->getData(), true);
            $this->addFlash('success');

            return $this->redirect($request->headers->get('Referer'));
        }

        return [
            'form' => $newForm->createView(),
        ];
    }

    public function editAction($object, $form)
    {
        return $this->newAction($object, $form);
    }

    public function showAction($object)
    {
        return ['object' => $object];
    }

    public function deleteAction(Request $request, $object)
    {
        $this->remove($object, true);
        $this->addFlash('success');

        return $this->redirect($request->headers->get('Referer'));
    }
}
