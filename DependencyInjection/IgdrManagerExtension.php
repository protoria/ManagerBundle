<?php
namespace Igdr\Bundle\ManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class IgdrManagerExtension
 */
class IgdrManagerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        //load configuration
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('igdr_manager.config.cache_provider', isset($config['cache_provider']) ? $config['cache_provider'] : null);

        return $configs;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'igdr_manager';
    }
}
