<?php

namespace spec\Knp\RadBundle\Twig;

use PHPSpec2\ObjectBehavior;

class FlashesTokenParser extends ObjectBehavior
{
    /**
     * @param  Twig_Parser                           $parser
     * @param  Twig_ExpressionParser                 $exprParser
     * @param  Twig_TokenStream                      $stream
     * @param  Twig_Token                            $token
     * @param  Twig_NodeInterface                    $body
     * @param  Knp\RadBundle\Twig\FlashesNodeFactory $nodeFactory
     * @param  Knp\RadBundle\Twig\FlashesNode        $node
     */
    function let($parser, $exprParser, $stream, $nodeFactory, $token)
    {
        $this->beConstructedWith($nodeFactory);

        $parser->getStream()->willReturn($stream);
        $parser->getExpressionParser()->willReturn($exprParser);

        $token->getLine()->willReturn(123);

        $this->setParser($parser);
    }

    function it_should_be_a_twig_token_parser()
    {
        $this->shouldBeAnInstanceOf('Twig_TokenParserInterface');
    }

    function it_should_handle_flashes_tags()
    {
        $this->getTag()->shouldReturn('flashes');
    }

    /**
     * {% flashes {expr:types} using catalog {expr:catalog} %}
     *      <div class="flash {{ type }}">{{ message }}</div>
     * {% endflashes %}
     *
     * @param  Twig_Node_Expression $types
     * @param  Twig_Node_Expression $catalog
     */
    function it_should_parse_complete_flashes_tag(
        $parser, $exprParser, $stream, $body, $nodeFactory, $node, $token, $types, $catalog
    )
    {
        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(false);
        $stream->test(\Twig_Token::BLOCK_END_TYPE)->willReturn(false);
        $exprParser->parseExpression()->willReturn($types);

        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(true);
        $stream->expect(\Twig_Token::NAME_TYPE, 'using');
        $stream->expect(\Twig_Token::NAME_TYPE, 'catalog');

        $exprParser->parseExpression()->willReturn($catalog);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $parser->subparse(array($this, 'isEndTag'), true)->willReturn($body);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $nodeFactory->createFlashesNode($types, $catalog, $body, 123)->willReturn($node);

        $this->parse($token)->shouldReturn($node);
    }

    /**
     * {% flashes {expr:types} %}
     *      <div class="flash {{ type }}">{{ message }}</div>
     * {% endflashes %}
     *
     * @param  Twig_Node_Expression $types
     */
    function it_should_parse_flashes_tag_with_no_specified_catalog(
        $parser, $exprParser, $stream, $body, $nodeFactory, $node, $token, $types
    )
    {
        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(false);
        $stream->test(\Twig_Token::BLOCK_END_TYPE)->willReturn(false);
        $exprParser->parseExpression()->willReturn($types);

        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(false);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $parser->subparse(array($this, 'isEndTag'), true)->willReturn($body);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $nodeFactory->createFlashesNode($types, null, $body, 123)->willReturn($node);

        $this->parse($token)->shouldReturn($node);
    }

    /**
     * {% flashes using catalog {expr:catalog} %}
     *      <div class="flash {{ type }}">{{ message }}</div>
     * {% endflashes %}
     *
     * @param  Twig_Node_Expression $catalog
     */
    function it_should_parse_flashes_tag_with_no_specified_types(
        $parser, $exprParser, $stream, $body, $nodeFactory, $node, $token, $catalog
    )
    {
        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(true);

        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(true);
        $stream->expect(\Twig_Token::NAME_TYPE, 'using');
        $stream->expect(\Twig_Token::NAME_TYPE, 'catalog');

        $exprParser->parseExpression()->willReturn($catalog);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $parser->subparse(array($this, 'isEndTag'), true)->willReturn($body);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $nodeFactory->createFlashesNode(null, $catalog, $body, 123)->willReturn($node);

        $this->parse($token)->shouldReturn($node);
    }

    /**
     * {% flashes using catalog {expr:catalog} %}
     *      <div class="flash {{ type }}">{{ message }}</div>
     * {% endflashes %}
     */
    function it_should_parse_flashes_tag_with_no_specified_types_nor_catalog(
        $parser, $exprParser, $stream, $body, $nodeFactory, $node, $token
    )
    {
        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(false);
        $stream->test(\Twig_Token::BLOCK_END_TYPE)->willReturn(true);

        $stream->test(\Twig_Token::NAME_TYPE, 'using')->willReturn(false);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $parser->subparse(array($this, 'isEndTag'), true)->willReturn($body);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $nodeFactory->createFlashesNode(null, null, $body, 123)->willReturn($node);

        $this->parse($token)->shouldReturn($node);
    }

    function its_isEndTag_should_return_true_recognize_end_tag_token($token)
    {
        $token->test('endflashes')->willReturn(true);

        $this->isEndTag($token)->shouldReturn(true);
    }

    function its_isEndTag_should_return_false_otherwise($token)
    {
        $token->test('endflashes')->willReturn(false);

        $this->isEndTag($token)->shouldReturn(false);
    }
}
