<?php

namespace Knp\RadBundle\Twig;

use Twig_Node;
use Twig_NodeInterface;
use Twig_Node_Expression;
use Twig_Compiler;

class FlashesNode extends Twig_Node
{
    public function __construct(Twig_Node_Expression $types = null, Twig_Node_Expression $catalog = null, Twig_NodeInterface $body, $lineno)
    {
        $nodes = array('body' => $body);
        if (null !== $types) {
            $nodes['types'] = $types;
        }
        if (null !== $catalog) {
            $nodes['catalog'] = $catalog;
        }

        parent::__construct($nodes, array(), $lineno, 'flashes');
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler->write('$types   = ');
        if ($this->hasNode('types')) {
            $compiler->subcompile($this->getNode('types'));
        } else {
            $compiler->repr(null);
        }
        $compiler->raw(";\n");

        $compiler->write('$catalog = ');
        if ($this->hasNode('catalog')) {
            $compiler->subcompile($this->getNode('catalog'));
        } else {
            $compiler->repr(null);
        }
        $compiler->raw(";\n");

        $compiler
            ->write("\$savedContext   = null;\n")
            ->write("\$flashesByTypes = array();\n")
            ->write("\$numFlashes     = 0;\n")
            ->write("foreach (\$this->env->getExtension('flash')->getFlashes(\$types) as \$type => \$flashes) {\n")
            ->indent()
            ->write("\$flashesByTypes[\$type] = \$flashes;\n")
            ->write("\$numFlashes+= count(\$flashes);\n")
            ->outdent()
            ->write("}\n")
            ->write("\$index = 0;\n")
            ->write("foreach (\$flashesByTypes as \$type => \$flashes) {\n")
            ->indent()
            ->write("foreach (\$flashes as \$flash) {\n")
            ->indent()
            ->write("if (null === \$savedContext) {\n")
            ->indent()
            ->write("\$savedContext = \$context;\n")
            ->outdent()
            ->write("}\n")
            ->write('$context = array(')
            ->raw("\n")
            ->indent()
            ->write("'type'    => \$type,\n")
            ->write("'message' => \$this->env->getExtension('flash')->renderMessage(\$flash, \$catalog),\n")
            ->write("'loop'    => array(\n")
            ->indent()
            ->write("'first'  => 0 === \$index,\n")
            ->write("'last'   => \$numFlashes === \$index,\n")
            ->write("'length' => \$numFlashes,\n")
            ->write("'index0' => \$index,\n")
            ->write("'index'  => \$index + 1\n")
            ->outdent()
            ->write(")\n")
            ->outdent()
            ->write(");\n")
            ->subcompile($this->getNode('body'))
            ->write("\$index++;\n")
            ->outdent()
            ->write("}\n")
            ->outdent()
            ->write("}\n")
            ->write("if (null !== \$savedContext) {\n")
            ->indent()
            ->write("\$context = \$savedContext;\n")
            ->write("unset(\$savedContext);\n")
            ->outdent()
            ->write("}\n")
            ->write("unset(\$index, \$flashesByTypes, \$types, \$catalog);\n")
        ;
    }
}
