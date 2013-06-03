<?php

namespace Knp\RadBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AssistantController extends Controller
{
    public function missingViewAction($viewName, $viewParams)
    {
        $viewPath = $this->get('knp_rad.view.path_deducer')->deducePath($viewName);
        $viewLogicalName = $this->get('knp_rad.view.path_deducer')->deduceViewLogicalName($viewName);
        $viewBody = $this->renderView($viewLogicalName, array(
            'viewParams' => $viewParams
        ));

        return $this->render(
            'KnpRadBundle:Assistant:missingView.html.twig',
            array(
                'viewName'   => $viewName,
                'viewBody'   => $viewBody,
                'viewParams' => $viewParams,
                'viewPath'   => $viewPath,
            )
        );
    }

    public function createViewAction(Request $request)
    {
        $viewName   = $request->request->get('viewName');
        $viewBody   = $request->request->get('viewBody');
        $viewPath   = $this->get('knp_rad.view.path_deducer')->deducePath($viewName);
        $filesystem = $this->get('filesystem');

        if ($filesystem->exists($viewPath)) {
            return new Response(sprintf(
                'File "%s" already exists.', $viewPath
            ), 409);
        }

        $filesystem->mkdir(dirname($viewPath));
        file_put_contents($viewPath, $viewBody);

        return new Response(null, 201);
    }
}
