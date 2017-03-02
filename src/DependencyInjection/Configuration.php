<?php

namespace Nassau\KunstmaanImportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_import');

        $this->configureImportTypes($rootNode->useAttributeAsKey('type')->prototype('array'));

        return $treeBuilder;
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     */
    private function configureImportTypes(NodeDefinition $node)
    {
        $node->children()->scalarNode('entity')->isRequired();
        $node->children()->scalarNode('handler_id')->defaultNull();
        $node->children()->arrayNode('post_processors')->prototype('scalar');

        $node->children()->arrayNode('excel')
            ->children()->scalarNode('format')->defaultValue('rows');

        /** @noinspection PhpUndefinedMethodInspection */
        $node->children()
            ->arrayNode('zip')
                ->children()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->arrayNode('file_attributes')->requiresAtLeastOneElement()->prototype('scalar')->end()->end()
                    ->arrayNode('data_file_extension')
                        ->beforeNormalization()
                            ->ifString()->then(function ($value) { return [$value]; })
                        ->end()
                        ->defaultValue(['xls', 'xlsx'])
                        ->prototype('scalar');


        $this->configureAttributes($node->children()->arrayNode('attributes')->useAttributeAsKey('name')->prototype('array'));

        $node->children()->arrayNode('default_attributes')->children()
            ->booleanNode('ignore_empty')->defaultFalse();

    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     */
    private function configureAttributes(NodeDefinition $node)
    {
        $node->children()->scalarNode('label')->isRequired();
        $node->children()->scalarNode('type')->defaultNull();
        $node->children()->booleanNode('ignore_empty');
    }

}
