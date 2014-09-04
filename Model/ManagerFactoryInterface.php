<?php
namespace Igdr\Bundle\ManagerBundle\Model;

use Igdr\Bundle\ManagerBundle\Manager\ManagerFactory;

/**
 * Class ManagerFactoryTrait
 */
interface ManagerFactoryInterface
{
    /**
     * @param ManagerFactory $managerFactory
     */
    public function setManagerFactory(ManagerFactory $managerFactory);
}
