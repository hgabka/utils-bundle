<?php

namespace Hgabka\UtilsBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * FirstFunction ::=
 *     "FIRST" "(" Subselect ")"
 */
class First extends FunctionNode
{
    const FUNCTION_NAME = 'FIRST';

    /**
     * @var Subselect
     */
    private $subselect;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->subselect = $parser->Subselect();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return '(' . $this->subselect->dispatch($sqlWalker) . ' LIMIT 1)';
    }
}
