<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Round extends FunctionNode
{
    public const FUNCTION_NAME = 'ROUND';

    private $arithmeticExpression;
    private $decimals;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();
        // parse second parameter if available
        if (Lexer::T_COMMA === $lexer->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->decimals = $parser->ArithmeticPrimary();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ROUND(' . $this->arithmeticExpression->dispatch($sqlWalker) . (null !== $this->decimals ? ', ' . $this->decimals->dispatch($sqlWalker) : '') . ')';
    }
}
