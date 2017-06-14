<?php
namespace Core\Template\TokenParser;

use Twig_Node;
use Twig_Token;
use Twig_Node_Block;
use Twig_Node_Print;
use Twig_Error_Syntax;
use Twig_TokenParser_Block;
use Core\Template\Node\Hook as HookBlock;

class Hook extends Twig_TokenParser_Block
{
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $name = sprintf('hook_%s', $name);
        if ($this->parser->hasBlock($name)) {
            throw new Twig_Error_Syntax(sprintf("The hook '%s' has already been defined line %d.", $name, $this->parser->getBlock($name)->getTemplateLine()), $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }

        $this->parser->setBlock($name, $block = new HookBlock($name, new Twig_Node(array()), $lineno));
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        if ($stream->nextIf(Twig_Token::BLOCK_END_TYPE)) {
            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            if ($token = $stream->nextIf(Twig_Token::NAME_TYPE)) {
                $value = $token->getValue();

                if ($value != $name) {
                    throw new Twig_Error_Syntax(sprintf('Expected endhook for hook "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
                }
            }
        } else {
            $body = new Twig_Node(array(
                new Twig_Node_Print($this->parser->getExpressionParser()->parseExpression(), $lineno),
            ));
        }
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $block->setNode('body', $body);
        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new \Core\Template\Node\HookReference($name, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Twig_Token $token)
    {
        return $token->test('endhook');
    }

    public function getTag()
    {
        return 'hook';
    }
}