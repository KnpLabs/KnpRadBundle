<?php

namespace Knp\RadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AssistantController extends Controller
{
    public function missingViewAction($viewName, $viewParams)
    {
        $viewPath = $this->deduceViewPath($viewName);
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
        $viewPath = $this->deduceViewPath($viewName);

        // in case directory does not exist
        $this->get('filesystem')->touch($viewPath);

        file_put_contents($viewPath, $viewBody);

        return new Response(null, 201);
    }

    private function deduceViewPath($viewName)
    {
        $logicalName = $this->get('templating.name_parser')->parse($viewName)->getPath();

        if (!preg_match('#^@([^/]+)/(.*)$#', $logicalName, $match)) {
            throw new \RuntimeException(sprintf(
                'Unable to deduce path from logical name "%s".',
                $logicalName
            ));
        }

        $bundle = $this->get('kernel')->getBundle($match[1]);

        return sprintf('%s/%s', $bundle->getPath(), $match[2]);
    }
}
