<?php

namespace Knp\RadBundle\Twig;

use Twig_Token;
use Twig_TokenParser;

/**
 * Token parser for "flashes" tags
 */
class FlashesTokenParser extends Twig_TokenParser
{
    private $nodeFactory;

    public function __construct(FlashesNodeFactory $nodeFactory = null)
    {
        $this->nodeFactory = $nodeFactory ?: new FlashesNodeFactory;
    }

    public function parse(Twig_Token $token)
    {
        $stream     = $this->parser->getStream();
        $exprParser = $this->parser->getExpressionParser();

        $typesExpr = null;
        if (!$stream->test(Twig_Token::NAME_TYPE, 'using') && !$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            $typesExpr = $exprParser->parseExpression();
        }

        $catalogExpr = null;
        if ($stream->test(Twig_Token::NAME_TYPE, 'using')) {
            $stream->expect(Twig_Token::NAME_TYPE, 'using');
            $stream->expect(Twig_Token::NAME_TYPE, 'catalog');

            $catalogExpr = $exprParser->parseExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'isEndTag'), true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return $this->nodeFactory->createFlashesNode($typesExpr, $catalogExpr, $body, $token->getLine());
    }

    public function getTag()
    {
        return 'flashes';
    }

    public function isEndTag(Twig_Token $token)
    {
        return $token->test('endflashes');
    }
}
