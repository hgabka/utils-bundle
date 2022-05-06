<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Instr extends FunctionNode
{
    public const FUNCTION_NAME = 'INSTR';

    public $str;
    public $substr;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->str = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->substr = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'INSTR(' .
            $this->str->dispatch($sqlWalker) . ', ' .
            $this->substr->dispatch($sqlWalker) .
            ')';
    }
}
