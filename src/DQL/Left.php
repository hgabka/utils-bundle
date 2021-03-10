<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Left extends FunctionNode
{
    const FUNCTION_NAME = 'REPEAT';

    /** @var string */
    public $string;

    /** @var int */
    public $offset;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->repeatString = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->repetition = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return
            'REPEAT('.
            $this->string->dispatch($sqlWalker).', '.
            $this->offset->dispatch($sqlWalker).
            ')'
            ;
    }

}
