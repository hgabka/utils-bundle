<?php

namespace HG\UtilsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hg_utils');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        
        $rootNode
          ->children()
                ->booleanNode('session_from_request')->defaultFalse()->end()
                ->scalarNode('session_param_name')->defaultValue('symfony')->end()
                ->scalarNode('web_dir')->defaultValue('../web')->end()
                ->scalarNode('upload_dir')->defaultValue('uploads')->end()
                ->arrayNode('available_locales')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('hu'))
                ->end()
          ->end()      
        ;
        
        return $treeBuilder;
    }
}
