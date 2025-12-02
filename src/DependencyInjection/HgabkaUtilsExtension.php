<?php

namespace Hgabka\UtilsBundle\DependencyInjection;

use Hgabka\UtilsBundle\Doctrine\Hydrator\ColumnHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\CountHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\IndexedHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\KeyValueHydrator;
use Hgabka\UtilsBundle\Doctrine\Type\EnumTypeInterface;
use Hgabka\UtilsBundle\DQL\Cast;
use Hgabka\UtilsBundle\DQL\CharLength;
use Hgabka\UtilsBundle\DQL\Date;
use Hgabka\UtilsBundle\DQL\Day;
use Hgabka\UtilsBundle\DQL\First;
use Hgabka\UtilsBundle\DQL\Format;
use Hgabka\UtilsBundle\DQL\IfElse;
use Hgabka\UtilsBundle\DQL\IfNull;
use Hgabka\UtilsBundle\DQL\Instr;
use Hgabka\UtilsBundle\DQL\Left;
use Hgabka\UtilsBundle\DQL\Month;
use Hgabka\UtilsBundle\DQL\Rand;
use Hgabka\UtilsBundle\DQL\Regexp;
use Hgabka\UtilsBundle\DQL\Repeat;
use Hgabka\UtilsBundle\DQL\Round;
use Hgabka\UtilsBundle\DQL\Substr;
use Hgabka\UtilsBundle\DQL\Year;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HgabkaUtilsExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    /** @var string */
    protected $formTypeTemplate = '@HgabkaUtils/Form/fields.html.twig';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $requiredLocales = explode('|', $container->getParameter('requiredlocales'));
        $container->setParameter('hgabka_utils.available_locales', $requiredLocales);
        $container->setParameter('hgabka_utils.admin_locales', $requiredLocales);
        $container->setParameter('hgabka_utils.default_admin_locale', $container->getParameter('defaultlocale'));

        $container->setParameter('hgabka_utils.session_security.ip_check', false);
        $container->setParameter('hgabka_utils.session_security.user_agent_check', false);

        $container->setParameter('hgabka_utils.google_signin.enabled', false);
        $container->setParameter('hgabka_utils.google_signin.client_id', $config['google']['client_id'] ?? null);
        $container->setParameter('hgabka_utils.google_signin.client_secret', $config['google']['client_secret'] ?? null);
        $container->setParameter('hgabka_utils.google_api_key', $config['google']['api_key'] ?? null);
        $container->setParameter('hgabka_utils.google_signin.hosted_domains', []);

        $container->setParameter('hgabka_utils.backend_user_class', $config['backend_user_class']);
        $container->setParameter('hgabka_utils.public_access_role', $config['public_access_role']);
        $container->setParameter('hgabka_utils.admin_firewall_name', $config['admin_firewall_name']);

        $container->setParameter('hgabka_utils.recaptcha.site_key', $config['recaptcha']['site_key'] ?? null);
        $container->setParameter('hgabka_utils.recaptcha.secret', $config['recaptcha']['secret'] ?? null);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $recaptchaTypeDefinition = $container->getDefinition('hgabka_utils.form.recaptcha_type');
        $recaptchaTypeDefinition->replaceArgument(0, $config['recaptcha']['site_key'] ?? null);

        $invisibleRecaptchaTypeDefinition = $container->getDefinition('hgabka_utils.form.invisible_recaptcha_type');
        $invisibleRecaptchaTypeDefinition->replaceArgument(0, $config['recaptcha']['site_key'] ?? null);

        $recaptchaValidatorDefinition = $container->getDefinition('hgabka_utils.validator.recaptcha');
        $recaptchaValidatorDefinition->replaceArgument(2, $config['recaptcha']['secret'] ?? null);

        $googleDriveDownloaderDefinition = $container->getDefinition('hgabka_utils.google_drive_downloader');
        $googleDriveDownloaderDefinition->replaceArgument(1, $config['google']['api_key'] ?? null);
        $googleDriveDownloaderDefinition->replaceArgument(2, $config['google']['client_id'] ?? null);
        $googleDriveDownloaderDefinition->replaceArgument(3, $config['google']['client_secret'] ?? null);

        $container
            ->registerForAutoconfiguration(EnumTypeInterface::class)
            ->addTag('hg_utils.doctrine_enum_type')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $this->configureTwigBundle($container, $config);
    }

    public function process(ContainerBuilder $container): void
    {
        $keyValueHydrator = [KeyValueHydrator::HYDRATOR_NAME, KeyValueHydrator::class];
        $columnHydrator = [ColumnHydrator::HYDRATOR_NAME, ColumnHydrator::class];
        $countHydrator = [CountHydrator::HYDRATOR_NAME, CountHydrator::class];
        $indexedHydrator = [IndexedHydrator::HYDRATOR_NAME, IndexedHydrator::class];

        foreach ($container->getParameter('doctrine.entity_managers') as $name => $serviceName) {
            $definition = $container->getDefinition('doctrine.orm.' . $name . '_configuration');
            $definition->addMethodCall('addCustomHydrationMode', $keyValueHydrator);
            $definition->addMethodCall('addCustomHydrationMode', $columnHydrator);
            $definition->addMethodCall('addCustomHydrationMode', $countHydrator);
            $definition->addMethodCall('addCustomHydrationMode', $indexedHydrator);
            $definition->addMethodCall('addCustomStringFunction', [Cast::FUNCTION_NAME, Cast::class]);
            $definition->addMethodCall('addCustomNumericFunction', [Rand::FUNCTION_NAME, Rand::class]);
            $definition->addMethodCall('addCustomStringFunction', [IfElse::FUNCTION_NAME, IfElse::class]);
            $definition->addMethodCall('addCustomStringFunction', [IfNull::FUNCTION_NAME, IfNull::class]);
            $definition->addMethodCall('addCustomStringFunction', [Repeat::FUNCTION_NAME, Repeat::class]);
            $definition->addMethodCall('addCustomStringFunction', [Instr::FUNCTION_NAME, Instr::class]);
            $definition->addMethodCall('addCustomStringFunction', [Date::FUNCTION_NAME, Date::class]);
            $definition->addMethodCall('addCustomNumericFunction', [Round::FUNCTION_NAME, Round::class]);
            $definition->addMethodCall('addCustomNumericFunction', [Format::FUNCTION_NAME, Format::class]);
            $definition->addMethodCall('addCustomStringFunction', [Regexp::FUNCTION_NAME, Regexp::class]);
            $definition->addMethodCall('addCustomStringFunction', [Substr::FUNCTION_NAME, Substr::class]);
            $definition->addMethodCall('addCustomStringFunction', [Left::FUNCTION_NAME, Left::class]);
            $definition->addMethodCall('addCustomStringFunction', [CharLength::FUNCTION_NAME, CharLength::class]);
            $definition->addMethodCall('addCustomStringFunction', [First::FUNCTION_NAME, First::class]);
            $definition->addMethodCall('addCustomStringFunction', [Year::FUNCTION_NAME, Year::class]);
            $definition->addMethodCall('addCustomStringFunction', [Month::FUNCTION_NAME, Month::class]);
            $definition->addMethodCall('addCustomStringFunction', [Day::FUNCTION_NAME, Day::class]);
        }

        $filterSets = $container->getParameter('liip_imagine.filter_sets');
        $filterSets['hgabka_utils_slider_fill'] = [
            'quality' => 95,
            'format' => 'jpg',
            'filters' => [
                'hg_fill' => [
                    'size' => [600, 400],
                ],
            ],
        ];

        $container->setParameter('liip_imagine.filter_sets', $filterSets);

        $definition = $container->getDefinition(MenuBuilder::class);

        if ($definition) {
            foreach ($container->findTaggedServiceIds('hgabka_utils.menu.adaptor') as $id => $attributes) {
                $priority = $attributes[0]['priority'] ?? 0;

                $definition->addMethodCall('addAdaptMenu', [new Reference($id), $priority]);
            }
        }

        $typesDefinition = [];
        if ($container->hasParameter('doctrine.dbal.connection_factory.types')) {
            $typesDefinition = $container->getParameter('doctrine.dbal.connection_factory.types');
        }
        $taggedEnums = $container->findTaggedServiceIds('hg_utils.doctrine_enum_type');

        foreach ($taggedEnums as $enumType => $definition) {
            /** @var $enumType AbstractEnumType */
            $typesDefinition[$enumType::NAME] = ['class' => $enumType];
        }
        $container->setParameter('doctrine.dbal.connection_factory.types', $typesDefinition);
    }

    protected function configureTwigBundle(ContainerBuilder $container, $config)
    {
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        [
                            'form_themes' => [$this->formTypeTemplate],
                            'globals' => [
                                'recaptcha_sitekey' => $config['recaptcha']['site_key'],
                                'recaptcha_secret' => $config['recaptcha']['secret'],
                            ],
                        ]
                    );

                    break;
            }
        }
    }
}
