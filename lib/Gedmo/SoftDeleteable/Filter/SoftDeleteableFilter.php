<?php

namespace Gedmo\SoftDeleteable\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * The SoftDeleteableFilter adds the condition necessary to
 * filter entities which were deleted "softly"
 *
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @author Patrik Votoček <patrik@votocek.cz>
 * @author malc0mn <malc0mn@advalvas.be>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class SoftDeleteableFilter extends AbstractFilter
{
    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$config = $this->getConfig($targetEntity)) {
            return '';
        }

        $column = $this->getColumn($targetEntity, $config);

        $addCondSql = $this->platform->getIsNullExpression($targetTableAlias.'.'.$column);
        if (isset($config['timeAware']) && $config['timeAware']) {
            $now = $this->conn->quote(date($this->platform->getDateTimeFormatString())); // should use UTC in database and PHP
            $addCondSql = "({$addCondSql} OR {$targetTableAlias}.{$column} > {$now})";
        }

        return $addCondSql;
    }
}
