<?php
namespace Igdr\Bundle\ManagerBundle\Event;

use Igdr\Bundle\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class EntityEvent
 */
class EntityEvent extends Event
{
    /**
     * @var object
     */
    private $entity;

    /**
     * @var AbstractManager
     */
    private $manager;

    /**
     * @param AbstractManager $manager
     * @param object          $entity
     */
    public function __construct(AbstractManager $manager, $entity)
    {
        $this->manager = $manager;
        $this->entity  = $entity;
    }

    /**
     * @return \Igdr\Bundle\ManagerBundle\Manager\AbstractManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}