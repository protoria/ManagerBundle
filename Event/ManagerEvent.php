<?php
namespace Igdr\Bundle\ManagerBundle\Event;

use Igdr\Bundle\ManagerBundle\Manager\AbstractManager;

/**
 * Class ManagerEvent
 */
class ManagerEvent extends EntityEvent
{
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

        parent::__construct($entity);
    }

    /**
     * @return \Igdr\Bundle\ManagerBundle\Manager\AbstractManager
     */
    public function getManager()
    {
        return $this->manager;
    }
}