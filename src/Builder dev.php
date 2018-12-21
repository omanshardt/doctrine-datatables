<?php

/*
 * This file is part of omanshardt/doctrine-datatables package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Doctrine\DataTables;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class Builder
 * @package Doctrine\DataTables
 */
class Builder
{
    /**
     * @var array
     */
    protected $columnAliases = array();

    /**
     * @var string
     */
    protected $columnField = 'data'; // or 'name'

    /**
     * @var string
     */
    protected $indexColumn = '*';

    /**
     * @var QueryBuilder|ORMQueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $requestParams;

    /**
     * @return array
     */
    public function getData($hydrator = null)
    {
        $query = $this->getFilteredQuery();
        $columns = &$this->requestParams['columns'];
        // Order
        if (array_key_exists('order', $this->requestParams)) {
            $order = &$this->requestParams['order'];
            foreach ($order as $sort) {
                $column = &$columns[intval($sort['column'])];

                if (is_array($column[$this->columnField])) {
                    error_log('IISSAARRAAYY 1111');
                    error_log(print_r($column[$this->columnField], true));
                    $query->addOrderBy($column[$this->columnField][0], $sort['dir']);
                }
                else {
                    if (array_key_exists($column[$this->columnField], $this->columnAliases)) {
                        if (is_array($this->columnAliases[$column[$this->columnField]])) {
                            $column[$this->columnField] = $this->columnAliases[$column[$this->columnField]][0];
                        }
                        else {
                            $column[$this->columnField] = $this->columnAliases[$column[$this->columnField]];
                        }
                    }
                    $query->addOrderBy($column[$this->columnField], $sort['dir']);
                }
            }
        }
        // Offset
        if (array_key_exists('start', $this->requestParams)) {
            $query->setFirstResult(intval($this->requestParams['start']));
        }
        // Limit
        if (array_key_exists('length', $this->requestParams)) {
            $length = intval($this->requestParams['length']);
            if ($length > 0) {
                $query->setMaxResults($length);
            }
        }
        // Fetch
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $result = array();
        foreach($paginator as $obj) {
            $result[] = $obj;
        }
        return $result;

//         if ($query instanceof ORMQueryBuilder) {
//             return $query->getQuery()->getResult($hydrator);        }
//         else {
//             return $query->execute()->fetchAll();
//         }
    }

    /**
     * @return QueryBuilder|ORMQueryBuilder
     */
    public function getFilteredQuery()
    {
        $query = clone $this->queryBuilder;
        $columns = &$this->requestParams['columns'];
        $c = count($columns);
        // Search
        if (array_key_exists('search', $this->requestParams)) {
            if ($value = trim($this->requestParams['search']['value'])) {
                $orX = $query->expr()->orX();
                for ($i = 0; $i < $c; $i++) {
                    $column = &$columns[$i];
                    if ($column['searchable'] == 'true') {
                        if (array_key_exists($column[$this->columnField], $this->columnAliases)) {
                            $column[$this->columnField] = $this->columnAliases[$column[$this->columnField]];
                        }
                        $orX->add($query->expr()->like($column[$this->columnField], ':search'));
                    }
                }
                if ($orX->count() >= 1) {
                    $query->andWhere($orX)
                        ->setParameter('search', "%{$value}%");
                }
            }
        }
        // Filter
        for ($i = 0; $i < $c; $i++) {
            $column = &$columns[$i];
            $andX = $query->expr()->andX();
            if (($column['searchable'] == 'true') && ($value = trim($column['search']['value']))) {
                if (!is_array($column[$this->columnField])) {
                    if (array_key_exists($column[$this->columnField], $this->columnAliases)) {
                        $column[$this->columnField] = $this->columnAliases[$column[$this->columnField]];
                    }
                }
                $operator = preg_match('~^\[(?<operator>[IN=!%<>•]+)\].*$~', $value, $matches) ? $matches['operator'] : '%•';
                $value    = preg_match('~^\[(?<operator>[IN=!%<>•]+)\](?<term>.*)$~', $value, $matches) ? $matches['term'] : $value;
                switch ($operator) {
                    case '!=': // Not equals; usage: [!=]search_term
                        $andX->add($query->expr()->neq($column[$this->columnField], ":filter_{$i}"));
                        $query->setParameter("filter_{$i}", $value);
                        break;
                    case '%%': // Like; usage: [%]search_term
                        $andX->add($query->expr()->like($column[$this->columnField], ":filter_{$i}"));
                        $value = "%{$value}%";
                        $query->setParameter("filter_{$i}", $value);
                        break;
                    case '<': // Less than; usage: [>]search_term
                        $andX->add($query->expr()->lt($column[$this->columnField], ":filter_{$i}"));
                        $query->setParameter("filter_{$i}", $value);
                        break;
                    case '>': // Greater than; usage: [<]search_term
                        $andX->add($query->expr()->gt($column[$this->columnField], ":filter_{$i}"));
                        $query->setParameter("filter_{$i}", $value);
                        break;
                    case 'IN': // Greater than; usage: [<]search_term
                        $value = explode(',', $value);
                        $params = array();
                        for ($j = 0; $j < count($value); $j++) {
                            $params[] = ":filter_{$i}_{$j}";
                        }
                        $andX->add($query->expr()->in($column[$this->columnField], implode(',', $params)));
                        for ($j = 0; $j < count($value); $j++) {
                            $query->setParameter("filter_{$i}_{$j}", $value[$j]);
                        }
                        break;
                    case '><': // Greater than; usage: [<]search_term
                        $value = explode(',', $value);
                        $params = array();
                        for ($j = 0; $j < count($value); $j++) {
                            $params[] = ":filter_{$i}_{$j}";
                        }
                        $andX->add($query->expr()->between($column[$this->columnField], $params[0], $params[1]));
                        for ($j = 0; $j < count($value); $j++) {
                            $query->setParameter("filter_{$i}_{$j}", $value[$j]);
                        }
                        break;
                    case '=': // Equals (default); usage: [=]search_term
                        $andX->add($query->expr()->eq($column[$this->columnField], ":filter_{$i}"));
                        $query->setParameter("filter_{$i}", $value);
                        break;
                    case '%•': // Like; usage: [%]search_term
                    default:
                        if (is_array($column[$this->columnField])) {
                            $orX = $query->expr()->orX();

                            foreach($column[$this->columnField] as $arr) {
                                error_log('if '.$arr);
                                $orX->add($query->expr()->like($arr, ":filter_{$i}"));
                            }

                            $value = "{$value}%";
                            $andX->add($orX);
                            $query->setParameter("filter_{$i}", $value);
                        }
                        else {
                            $andX->add($query->expr()->like($column[$this->columnField], ":filter_{$i}"));
                            $value = "{$value}%";
                            $query->setParameter("filter_{$i}", $value);
                        }
                        break;
                }
            }
            if ($andX->count() >= 1) {
                $query->andWhere($andX);
            }
        }
        // Done
        return $query;
    }

    /**
     * @return int
     */
    public function getRecordsFiltered()
    {
        $query = $this->getFilteredQuery();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return count($paginator);

//         if ($query instanceof ORMQueryBuilder) {
//             return $query->resetDQLPart('select')
//                 ->select("COUNT({$this->indexColumn})")
//                 ->getQuery()
//                 ->getSingleScalarResult();
//         } else {
//             return $query->resetQueryPart('select')
//                 ->select("COUNT({$this->indexColumn})")
//                 ->execute()
//                 ->fetchColumn(0);
//         }
    }

    /**
     * @return int
     */
    public function getRecordsTotal()
    {
        $query = clone $this->queryBuilder;
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return count($paginator);
//         if ($query instanceof ORMQueryBuilder) {
//             return $query->resetDQLPart('select')
//                 ->select("COUNT({$this->indexColumn})")
//                 ->getQuery()
//                 ->getSingleScalarResult();
//         } else {
//             return $query->resetQueryPart('select')
//                 ->select("COUNT({$this->indexColumn})")
//                 ->execute()
//                 ->fetchColumn(0);
//         }
    }

    /**
     * @return array
     */
    public function getResponse($hydrator = null)
    {
        return array(
            'data' => $this->getData($hydrator),
            'draw' => $this->requestParams['draw'],
            'recordsFiltered' => $this->getRecordsFiltered(),
            'recordsTotal' => $this->getRecordsTotal(),
        );
    }

    /**
     * @param string $indexColumn
     * @return static
     */
    public function withIndexColumn($indexColumn)
    {
        $this->indexColumn = $indexColumn;
        return $this;
    }

    /**
     * @param array $columnAliases
     * @return static
     */
    public function withColumnAliases($columnAliases)
    {
        $this->columnAliases = $columnAliases;
        return $this;
    }

    /**
     * @param string $columnField
     * @return static
     */
    public function withColumnField($columnField)
    {
        $this->columnField = $columnField;
        return $this;
    }

    /**
     * @param QueryBuilder|ORMQueryBuilder $queryBuilder
     * @return static
     */
    public function withQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    /**
     * @param array $requestParams
     * @return static
     */
    public function withRequestParams($requestParams)
    {
        $this->requestParams = $requestParams;
        return $this;
    }
}
