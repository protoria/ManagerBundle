<?php
namespace Igdr\Bundle\ManagerBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ManagerFactory
 */
class ManagerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return AbstractManager
     * @throws \Exception
     */
    public function get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        } else {
            throw new \Exception(sprintf('Manager with name "%s" not found', $name));
        }
    }
}