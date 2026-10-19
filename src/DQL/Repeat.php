<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class Repeat.
 *
 * A REPEAT SQL függvény DQL-es megvalósítása
 */
class Repeat extends FunctionNode
{
    public const FUNCTION_NAME = 'REPEAT';

    /** @var string */
    public $repeatString;

    /** @var int */
    public $repetition;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->repeatString = $parser->StringPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->repetition = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return
            'REPEAT(' .
            $this->repeatString->dispatch($sqlWalker) . ', ' .
            $this->repetition->dispatch($sqlWalker) .
            ')'
            ;
    }
}
