<?php
namespace Igdr\Bundle\ManagerBundle\Model;

use Igdr\Bundle\ManagerBundle\Manager\ManagerFactory;


/**
 * Class ManagerFactoryTrait
 */
trait ManagerFactoryTrait
{
    /**
     * @var ManagerFactory
     */
    protected $managerFactory;

    /**
     * @param ManagerFactory $managerFactory
     */
    public function setManagerFactory(ManagerFactory $managerFactory)
    {
        $this->managerFactory = $managerFactory;
    }
}
