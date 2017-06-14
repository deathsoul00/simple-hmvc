<?php
namespace Core\Template\Node;

use Twig_Compiler;
use Twig_Node_Block;
use Twig_NodeInterface;

class Hook extends Twig_Node_Block
{
    public function __construct($name, Twig_NodeInterface $body, $lineno, $tag = null)
    {
        parent::__construct($name, $body, $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("public function block_%s(\$context, array \$blocks = array())\n", $this->getAttribute('name')), "{\n")
            ->indent()
        ;

        $compiler
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n\n")
        ;
    }
}