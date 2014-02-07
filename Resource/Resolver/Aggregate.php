<?php

namespace Knp\RadBundle\Resource\Resolver;

use Knp\RadBundle\Resource\Resolver\ResourceResolver;
use Symfony\Component\HttpFoundation\Request;

class Aggregate implements ResourceResolver
{
    public function __construct(OptionsBased $optionsBased, ExpressionLanguageBased $expressionLanguageBased)
    {
        $this->optionsBased = $optionsBased;
        $this->expressionLanguageBased = $expressionLanguageBased;
    }

    public function resolveResource(Request $request, array $options)
    {
        // TODO improve this detection maybe?
        if (isset($options['expr'])) {
            return $this->expressionLanguageBased->resolveResource($request, $options);
        }

        return $this->optionsBased->resolveResource($request, $options);
    }
}
