<?php
namespace Igdr\Bundle\ManagerBundle\Event;

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
     * @param object $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}