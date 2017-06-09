<?php
namespace Core\Template\Node;

use Twig_Node;
use Twig_Compiler;

class Hook extends Twig_Node
{
    public function __construct($name, $lineno, $tag = null)
    {
        parent::__construct(array(), array('name' => $name), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("\\Core\\Module::hookTemplate('%s', \$context, \$blocks);\n", $this->getAttribute('name')))
        ;
    }
}