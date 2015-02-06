<?php
namespace Igdr\Bundle\ManagerBundle\Manager;

/**
 * The interface for manager
 */
interface ManagerInterface
{
    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns($columns = array());

    /**
     * @param array $where
     *
     * @return $this
     */
    public function where($where = array());

    /**
     * @param array $order
     *
     * @return $this
     */
    public function order($order = array());

    /**
     * @param Integer $count
     * @param Integer $offset
     *
     * @return $this
     */
    public function limit($count, $offset = 0);

    /**
     * @param string $field
     *
     * @return Integer
     */
    public function count($field = null);

    /**
     * @return string
     */
    public function getIdField();

    /**
     * @return array
     */
    public function findAll();

    /**
     * @return array
     */
    public function findOne();

    /**
     * Get list of all ids
     *
     * @return Array
     */
    public function getId();

    /**
     * @param Integer|Array|String $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Create new entity
     *
     * @return object
     */
    public function create();

    /**
     * @param object $entity
     * @param bool   $flush
     * @param bool   $fireEvents
     *
     * @return $this
     * @throws \Exception
     */
    public function save($entity, $flush = true, $fireEvents = true);

    /**
     * @param int|object $entity
     * @param bool       $flush
     * @param bool       $fireEvents
     *
     * @return $this
     * @throws \Exception
     */
    public function delete($entity, $flush = true, $fireEvents = true);
}

