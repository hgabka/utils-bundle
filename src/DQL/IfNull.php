<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class IfNull extends FunctionNode
{
    public const FUNCTION_NAME = 'IFNULL';

    private $expr1;
    private $expr2;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr1 = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->expr2 = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'IFNULL('
            . $sqlWalker->walkArithmeticPrimary($this->expr1) . ', '
            . $sqlWalker->walkArithmeticPrimary($this->expr2) . ')';
    }
}
