<?php

namespace Hgabka\UtilsBundle\DependencyInjection;

use Hgabka\Entity\AbstractUser;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hgabka_utils');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('recaptcha')
                ->children()
                    ->scalarNode('site_key')->end()
                    ->scalarNode('secret')->end()
                ->end()
                ->end()
                ->arrayNode('google')
                ->children()
                    ->scalarNode('api_key')->end()
                    ->scalarNode('client_id')->end()
                    ->scalarNode('client_secret')->end()
                ->end()
                ->end()
            ->scalarNode('backend_user_class')->defaultValue(AbstractUser::class)->end()
            ->scalarNode('public_access_role')->defaultValue('PUBLIC_ACCESS')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
