<?php

namespace Hgabka\LoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /** @var ContainerBuilder */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hgabka_logger');

        $rootNode
            ->children()
                ->arrayNode('notifier')
                    ->addDefaultsIfNotSet()
                    ->children()
                          ->arrayNode('mails')
                          ->addDefaultsIfNotSet()
                              ->children()
                                  ->enumNode('enabled')->values(['always', 'prod', 'debug', 'never'])->defaultValue('prod')->end()
                                  ->scalarNode('from_mail')->defaultValue('info@hgabka.eu')->end()
                                  ->scalarNode('from_name')->defaultValue('hgLoggerBundle')->end()
                                  ->scalarNode('subject')->defaultValue('Exception a(z) [host] oldalon')->end()
                                  ->arrayNode('recipients')
                                      ->prototype('scalar')->end()
                                  ->end()
                                  ->booleanNode('send_only_if_new')->defaultFalse()->end()
                                  ->scalarNode('send_404')->defaultTrue()->end()
                              ->end()
                          ->end()
                          ->arrayNode('logging')
                          ->addDefaultsIfNotSet()
                              ->children()
                                  ->enumNode('enabled')->values(['always', 'prod', 'debug', 'never'])->defaultValue('prod')->end()
                                  ->arrayNode('type')
                                  ->addDefaultsIfNotSet()
                                  ->beforeNormalization()
                                      ->ifString()
                                      ->then(function ($v) { return ['debug' => $v, 'prod' => $v]; })
                                  ->end()
                                      ->children()
                                          ->enumNode('debug')->values(['file', 'database', 'both', 'none'])->defaultValue('none')->end()
                                          ->enumNode('prod')->values(['file', 'database', 'both', 'none'])->defaultValue('both')->end()
                                      ->end()
                                  ->end()
                                  ->scalarNode('log_path')->defaultValue($this->container->getParameter('kernel.logs_dir').'/exception')->end()
                              ->end()
                          ->end()
                    ->end()
                ->end()

                ->arrayNode('logger')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('common_identifier')->defaultValue('symfony')->end()
                        ->scalarNode('translation_domain')->defaultValue('logger')->end()
                    ->end()
                ->end()
             ->end()
        ;

        return $treeBuilder;
    }
}
