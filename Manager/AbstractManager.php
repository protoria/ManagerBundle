<?php
namespace Igdr\Bundle\ManagerBundle\Manager;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Igdr\Bundle\ManagerBundle\Event\EntityEvent;
use Igdr\Bundle\ManagerBundle\Event\ManagerEvent;
use Igdr\Bundle\ManagerBundle\IgdrManagerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractManager
 */
abstract class AbstractManager implements ManagerInterface
{
    /**
     * @var string
     */
    protected $managerId;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

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
            $this->query = $this->createQuery();

            $this->eventDispatcher->dispatch(IgdrManagerEvents::EVENT_INIT_QUERY, new ManagerEvent($this));
        }

        return $this->query;
    }

    /**
     * @return QueryBuilder
     */
    public function createQuery()
    {
        return $this->getRepository()->createQueryBuilder('e');
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
                if (!empty($value)) {
                    $this->getQuery()->setParameter(key($value), current($value));
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function find()
    {
        $this->getQuery()->distinct(true);
        $query = $this->getQuery()->getQuery();
        if ($this->cacheResults) {
            $query->useResultCache(true);
            $query->setResultCacheId($this->getCacheId($query));
            if (is_numeric($this->cacheResults)) {
                $query->setResultCacheLifetime($this->cacheResults);
            }
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
        if (strpos($field, '.') === false) {
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
        $entity = new $this->class;

        //fire event
        $this->eventDispatcher->dispatch(IgdrManagerEvents::EVENT_INITIALIZE, new EntityEvent($this, $entity));
        $this->eventDispatcher->dispatch($this->getEventName(IgdrManagerEvents::SUFFIX_INITIALIZE), new EntityEvent($this, $entity));

        return $entity;
    }

    /**
     * @param object $entity
     * @param bool   $flush
     * @param bool   $fireEvents
     *
     * @return $this
     * @throws \Exception
     */
    public function save($entity, $flush = true, $fireEvents = true)
    {
        if (!($entity instanceof $this->class)) {
            throw new \Exception(sprintf('Entity should be instance of %s', $this->class));
        }

        $exists = $this->em->getUnitOfWork()->isInIdentityMap($entity);

        //fire event
        if ($fireEvents) {
            $this->eventDispatcher->dispatch($this->getEventName($exists ? IgdrManagerEvents::SUFFIX_BEFORE_UPDATE : IgdrManagerEvents::SUFFIX_BEFORE_CREATE), new EntityEvent($this, $entity));
            $this->eventDispatcher->dispatch($exists ? IgdrManagerEvents::EVENT_BEFORE_UPDATE : IgdrManagerEvents::EVENT_BEFORE_CREATE, new EntityEvent($this, $entity));
        }

        //persist and flush
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }

        //clean cache
        if ($this->cacheResults) {
            //@todo tags
            $this->cacheProvider->deleteAll();
        }

        //fire event
        if ($fireEvents) {
            $this->eventDispatcher->dispatch($this->getEventName($exists ? IgdrManagerEvents::SUFFIX_AFTER_UPDATE : IgdrManagerEvents::SUFFIX_AFTER_CREATE), new EntityEvent($this, $entity));
            $this->eventDispatcher->dispatch($exists ? IgdrManagerEvents::EVENT_AFTER_UPDATE : IgdrManagerEvents::EVENT_AFTER_CREATE, new EntityEvent($this, $entity));
        }

        return $this;
    }

    /**
     * @param int|object $entity
     * @param bool       $flush
     * @param bool       $fireEvents
     *
     * @return $this
     * @throws \Exception
     */
    public function delete($entity, $flush = true, $fireEvents = true)
    {
        if (is_numeric($entity)) {
            $this->setId($entity);
            $entity = $this->findOne();
        }

        if (empty($entity)) {
            return $this;
        }

        if (!($entity instanceof $this->class)) {
            throw new \Exception(sprintf('Entity should be instance of %s', $this->class));
        }

        //fire event
        if ($fireEvents) {
            $this->eventDispatcher->dispatch(IgdrManagerEvents::EVENT_BEFORE_DELETE, new EntityEvent($this, $entity));
            $this->eventDispatcher->dispatch($this->getEventName(IgdrManagerEvents::SUFFIX_BEFORE_DELETE), new EntityEvent($this, $entity));
        }

        //remove
        $this->em->remove($entity);
        if ($flush) {
            $this->em->flush();
        }

        //clean cache
        if ($this->cacheResults) {
            //@todo tags
            $this->cacheProvider->deleteAll();
        }

        //fire event
        if ($fireEvents) {
            $this->eventDispatcher->dispatch(IgdrManagerEvents::EVENT_AFTER_DELETE, new EntityEvent($this, $entity));
            $this->eventDispatcher->dispatch($this->getEventName(IgdrManagerEvents::SUFFIX_AFTER_DELETE), new EntityEvent($this, $entity));
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

    /**
     * @param string $managerId
     *
     * @return $this
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * @param string $eventName
     *
     * @return string
     */
    private function getEventName($eventName)
    {
        return sprintf('%s.%s', $this->managerId, $eventName);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
