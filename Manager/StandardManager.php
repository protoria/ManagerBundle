<?php
namespace Igdr\Bundle\ManagerBundle\Manager;

/**
 * Class StandardManager
 *
 * @package Igdr\Bundle\ManagerBundle\Manager
 */
class StandardManager extends AbstractManager
{
    /**
     * @param string  $class
     * @param string  $repository
     * @param array   $where
     * @param array   $order
     * @param boolean $cacheResults
     */
    public function __construct($class, $repository, $where = array(), $order = array(), $cacheResults = false)
    {
        $this->class          = $class;
        $this->repositoryName = $repository;
        $this->order          = $order;
        $this->where          = $where;
        $this->cacheResults   = $cacheResults;
    }
}