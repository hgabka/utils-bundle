<?php

namespace Hgabka\UtilsBundle\Doctrine\Query;

use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\SqlWalker;
use Hgabka\UtilsBundle\Doctrine\Hydrator\CountHydrator;

class CountSqlWalker extends SqlWalker
{
    /**
     * {@inheritDoc}
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        return sprintf("SELECT COUNT(*) AS %s FROM (%s) AS t", CountHydrator::FIELD, parent::walkSelectStatement($AST));
    }
}