<?php

namespace Limit0\ModlrAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration for ModlrAuthBundle.
 *
 * @author  Josh Worden <josh@limit0.io>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('modlr_auth_bundle')
            ->children()
                ->append($this->getJwtNode())
            ->end()
        ;
        return $treeBuilder;
    }

    /**
     * Gets the JWT configuration
     *
     * @return  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getJwtNode()
    {
        $node = new TreeBuilder();
        return $node->root('jwt')
            ->isRequired()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('issuer')->defaultValue('modlr-auth')->end()
                ->scalarNode('ttl')->defaultValue(86400)->end()
                ->scalarNode('secret')->cannotBeEmpty()->end()
            ->end()
        ;
    }
}
