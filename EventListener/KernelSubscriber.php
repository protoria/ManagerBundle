<?php
namespace Igdr\Bundle\ManagerBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Igdr\Bundle\ManagerBundle\Model\ManagerFactoryTrait;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class KernelSubscriber
 */
class KernelSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::FINISH_REQUEST => 'flush',
            ConsoleEvents::TERMINATE     => 'flush',
        );
    }

    /**
     * flush all entities
     */
    public function flush()
    {
        if ($this->em->getUnitOfWork()->size() > 0) {
            $this->em->flush();
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

}