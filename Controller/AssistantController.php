<?php

namespace Knp\RadBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AssistantController extends Controller
{
    public function missingViewAction($viewName, $viewParams)
    {
        $viewPath = $this->get('knp_rad.view.path_deducer')->deducePath($viewName);
        $viewBody = $this->renderView(
            'KnpRadBundle:Assistant:_viewBody.twig.twig',
            array(
                'viewParams' => $viewParams
            )
        );

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
        $viewName = $request->request->get('viewName');
        $viewBody = $request->request->get('viewBody');
        $viewPath = $this->get('knp_rad.view.path_deducer')->deducePath($viewName);

        // in case directory does not exist
        $this->get('filesystem')->mkdir(dirname($viewPath));

        file_put_contents($viewPath, $viewBody);

        return new Response(null, 201);
    }
}
