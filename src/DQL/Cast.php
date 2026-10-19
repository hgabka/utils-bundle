<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Cast extends FunctionNode
{
    public const FUNCTION_NAME = 'CAST';

    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $first;
    /** @var string */
    protected $second;

    /**
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'CAST(%s AS %s)',
            $this->first->dispatch($sqlWalker),
            $this->second
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->first = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_AS);
        $parser->match(TokenType::T_IDENTIFIER);
        $this->second = $parser->getLexer()->token->value;
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
