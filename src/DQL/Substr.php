<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Substr extends FunctionNode
{
    public const FUNCTION_NAME = 'SUBSTR';

    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $str;
    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $from;
    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $for;

    /**
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'SUBSTR(%s FROM %s FOR %s)',
            $this->str->dispatch($sqlWalker),
            $this->from->dispatch($sqlWalker),
            $this->for->dispatch($sqlWalker)
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->str = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->from = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->for = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
