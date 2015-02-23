<?php
namespace Igdr\Bundle\ManagerBundle\Event;

use Igdr\Bundle\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ManagerEvent
 */
class ManagerEvent extends Event
{
    /**
     * @var AbstractManager
     */
    private $manager;

    /**
     * @param AbstractManager $manager
     */
    public function __construct(AbstractManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \Igdr\Bundle\ManagerBundle\Manager\AbstractManager
     */
    public function getManager()
    {
        return $this->manager;
    }
}