<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('data_object_sort_index');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('sort_index')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('folder')->isRequired()->end()
                            ->scalarNode('object_class')->isRequired()->end()
                            ->scalarNode('data_object_field')->isRequired()->end()
                            ->booleanNode('recursive')->defaultValue(false)->end()
                            ->scalarNode('type')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
