<?php
namespace Igdr\Bundle\ManagerBundle\Manager;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractManager
 */
abstract class AbstractManager
{
    /**
     * @var string
     */
    protected $idField;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var Array
     */
    protected $order = array();

    /**
     * @var Array
     */
    protected $where = array();

    /**
     * @var EntityRepository
     */
    protected $repository = null;

    /**
     * @var string
     */
    protected $repositoryName = null;

    /**
     * @var EntityManager
     */
    protected $em = null;

    /**
     * @var QueryBuilder
     */
    protected $query = null;

    /**
     * @var boolean
     */
    protected $cacheResults;

    /**
     * @var CacheProvider
     */
    protected $cacheProvider;

    /**
     * @return $this
     */
    public function resetQuery()
    {
        $this->query = $this->getRepository()->createQueryBuilder('e');

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $this->query = $this->getRepository()->createQueryBuilder('e');
        }

        return $this->query;
    }

    /**
     * @param EntityRepository $repository
     *
     * @return $this
     */
    public function setRepository(EntityRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        if (empty($this->repository)) {
            $this->setRepository($this->em->getRepository($this->repositoryName));
        }

        return $this->repository;
    }

    /**
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setEm($em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param string $repositoryName
     *
     * @return $this
     */
    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * Get list of all ids
     *
     * @return Array
     */
    public function getId()
    {
        return $this->getValue($this->getIdField());
    }

    /**
     * Get records from database
     *
     * @return array The objects.
     */
    public function findAll()
    {
        return $this->find();
    }

    /**
     * Get one record
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOne()
    {
        $result = $this->limit(1)->find();

        return !empty($result) ? current($result) : null;
    }

    /**
     * @return String
     */
    public function getIdField()
    {
        if (empty($this->idField)) {
            $this->idField = $this->em->getClassMetadata($this->class)->identifier[0];
        }

        return $this->idField;
    }

    /**
     * @param Array $order
     *
     * @return $this
     */
    public function order($order = array())
    {
        empty($order) && $order = $this->order;
        foreach ((array) $order as $name => $dir) {
            $this->getQuery()->addOrderBy('e.' . $name, $dir);
        }

        return $this;
    }

    /**
     * @param Array $where
     *
     * @return $this
     */
    public function where($where = array())
    {
        empty($where) && $where = $this->where;
        foreach ((array) $where as $name => $value) {
            if (is_numeric($name)) {
                $this->getQuery()->andWhere($value);
            } else {
                $this->getQuery()->andWhere($name);
                $this->getQuery()->setParameter(key($value), current($value));
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function find()
    {
        $query = $this->getQuery()->getQuery();
        if ($this->cacheResults) {
            $query->useResultCache(true);
            $query->setResultCacheId($this->getCacheId($query));
        }

        return $query->getResult();
    }

    /**
     * @param String $field
     *
     * @return Integer
     */
    public function count($field = null)
    {
        $query = clone $this->getQuery();
        $query = $query->select($field ? 'count(DISTINCT e.' . $field . ')' : 'count(DISTINCT e)')->getQuery();
        /* @var $query Query */
        $count = $query->getSingleScalarResult();

        return $count;
    }

    /**
     * Get list of values
     *
     * @param string $field
     * @param bool   $unique
     *
     * @return array
     */
    public function getValue($field, $unique = true)
    {
        $query = clone $this->getQuery();

        /* @var $query Query */
        if (strpos($field, 'e.') === false) {
            $query = $query->select('DISTINCT e.' . $field)->getQuery();
        } else {
            $query = $query->select($field)->getQuery();
        }

        $queryResult = $query->getScalarResult();
        $result      = array();
        foreach ($queryResult as $row) {
            $result[] = array_shift($row);
        }

        return $unique ? array_unique($result) : $result;
    }

    /**
     * @param Integer $count
     * @param Integer $offset
     *
     * @return $this
     */
    public function limit($count, $offset = 0)
    {
        $this->getQuery()->setFirstResult($offset);
        $this->getQuery()->setMaxResults($count);

        return $this;
    }

    /**
     * @param Integer|Array|String $id
     *
     * @return $this
     */
    public function setId($id)
    {
        empty($id) && $id = 0;
        $this->where(array('e.' . $this->getIdField() . ' IN (:id)' => array('id' => $id)));

        return $this;
    }

    /**
     * Create new entity
     *
     * @return object
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * @param object $data
     * @param bool   $andFlush
     *
     * @return $this
     */
    public function save($data, $andFlush = true)
    {
        $this->em->persist($data);

        if ($andFlush) {
            $this->em->flush();
        }

        //clean cache
        if ($this->cacheResults) {
            //@todo tags
            $this->cacheProvider->deleteAll();
        }

        return $this;
    }

    /**
     * @param integer|object $data
     * @param bool           $andFlush
     *
     * @return $this
     * @throws \Exception
     */
    public function delete($data, $andFlush = true)
    {
        if (is_numeric($data)) {
            $this->setId($data);
            $entity = $this->findOne();
        } elseif (is_object($data)) {
            $entity = $data;
        } else {
            throw new \Exception('Unknown format of input parameter');
        }

        $this->em->remove($entity);

        if ($andFlush) {
            $this->em->flush();
        }

        //clean cache
        if ($this->cacheResults) {
            //@todo tags
            $this->cacheProvider->deleteAll();
        }

        return $this;
    }

    /**
     * @param boolean $cache
     *
     * @return $this
     */
    public function setCacheResults($cache)
    {
        $this->cacheResults = $cache;

        return $this;
    }

    /**
     * @param CacheProvider $cacheProvider
     *
     * @return $this
     */
    public function setCacheProvider(CacheProvider $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;

        return $this;
    }

    /**
     * clone
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * @param Query $query
     *
     * @return string
     */
    private function getCacheId($query)
    {
        return $this->getCachePrefix() . md5($query->getSQL());
    }

    /**
     * @return string
     */
    private function getCachePrefix()
    {
        return strtolower(preg_replace('#[^\w]+#', '', $this->getRepositoryName()));
    }
}
