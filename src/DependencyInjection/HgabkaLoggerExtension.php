<?php

namespace Hgabka\LoggerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HgabkaLoggerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $loggerDefinition = $container->getDefinition('hgabka_logger.exception_logger');
        $loggerDefinition->replaceArgument(1, $config['notifier']['logging']['log_path']);

        $columnLoggerDefinition = $container->getDefinition('hgabka_logger.column_logger');
        $columnLoggerDefinition->replaceArgument(2, $config['logger']['common_identifier']);

        $actionLoggerDefinition = $container->getDefinition('hgabka_logger.action_logger');
        $actionLoggerDefinition->replaceArgument(5, $config['logger']['common_identifier']);
        $actionLoggerDefinition->replaceArgument(6, $config['logger']['translation_domain']);

        $notifierDefinition = $container->getDefinition('hgabka_logger.exception_notifier');
        $notifierDefinition->addMethodCall('setConfig', [$config['notifier']]);
    }
}
