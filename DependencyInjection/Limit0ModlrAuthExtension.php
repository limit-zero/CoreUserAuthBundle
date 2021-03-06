<?php

namespace Limit0\ModlrAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads and manages the bundle configuration for Modlr.
 *
 * @author  Josh Worden <josh@limit0.io>
 */
class Limit0ModlrAuthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Process the config.
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('limit0_modlr_auth.jwt.secret', $config['jwt']['secret']);
        $container->setParameter('limit0_modlr_auth.jwt.issuer', $config['jwt']['issuer']);
        $container->setParameter('limit0_modlr_auth.jwt.ttl',    $config['jwt']['ttl']);

        // Load bundle services.
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

    }
}
