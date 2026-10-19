<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Format extends FunctionNode
{
    public const FUNCTION_NAME = 'FORMAT';

    private $arithmeticExpression;
    private $decimals;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();
        // parse second parameter if available
        if (TokenType::T_COMMA === $lexer->lookahead->type) {
            $parser->match(TokenType::T_COMMA);
            $this->decimals = $parser->ArithmeticPrimary();
        }
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'FORMAT(' . $this->arithmeticExpression->dispatch($sqlWalker) . (null !== $this->decimals ? ', ' . $this->decimals->dispatch($sqlWalker) : '') . ')';
    }
}
