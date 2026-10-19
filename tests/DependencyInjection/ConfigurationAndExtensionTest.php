<?php

namespace Hgabka\UtilsBundle\Tests\DependencyInjection;

use Hgabka\UtilsBundle\DependencyInjection\Configuration;
use Hgabka\UtilsBundle\DependencyInjection\HgabkaUtilsExtension;
use Hgabka\UtilsBundle\Doctrine\Type\EnumTypeInterface;
use Hgabka\UtilsBundle\Doctrine\Hydrator\ColumnHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\CountHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\IndexedHydrator;
use Hgabka\UtilsBundle\Doctrine\Hydrator\KeyValueHydrator;
use Hgabka\UtilsBundle\DQL\IfNull;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigurationAndExtensionTest extends TestCase
{
    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('requiredlocales', 'hu|en');
        $container->setParameter('defaultlocale', 'hu');

        return $container;
    }

    public function testDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertSame('PUBLIC_ACCESS', $config['public_access_role']);
        $this->assertSame('admin', $config['admin_firewall_name']);
        $this->assertSame(\Hgabka\Entity\AbstractUser::class, $config['backend_user_class']);
        $this->assertArrayNotHasKey('recaptcha', $config);
    }

    public function testLocaleParametersComeFromTheKernelParameters(): void
    {
        $container = $this->container();

        (new HgabkaUtilsExtension())->load([[]], $container);

        $this->assertSame(['hu', 'en'], $container->getParameter('hgabka_utils.available_locales'));
        $this->assertSame(['hu', 'en'], $container->getParameter('hgabka_utils.admin_locales'));
        $this->assertSame('hu', $container->getParameter('hgabka_utils.default_admin_locale'));
    }

    public function testConfigValuesBecomeParameters(): void
    {
        $container = $this->container();

        (new HgabkaUtilsExtension())->load([[
            'backend_user_class' => 'App\Entity\Admin',
            'public_access_role' => 'IS_AUTHENTICATED_ANONYMOUSLY',
            'admin_firewall_name' => 'backend',
            'recaptcha' => ['site_key' => 'SITE', 'secret' => 'SECRET'],
            'google' => ['api_key' => 'KEY', 'client_id' => 'ID', 'client_secret' => 'GSECRET'],
        ]], $container);

        $this->assertSame('App\Entity\Admin', $container->getParameter('hgabka_utils.backend_user_class'));
        $this->assertSame('IS_AUTHENTICATED_ANONYMOUSLY', $container->getParameter('hgabka_utils.public_access_role'));
        $this->assertSame('backend', $container->getParameter('hgabka_utils.admin_firewall_name'));
        $this->assertSame(['SITE', 'SECRET'], [$container->getParameter('hgabka_utils.recaptcha.site_key'), $container->getParameter('hgabka_utils.recaptcha.secret')]);
        $this->assertSame(['KEY', 'ID', 'GSECRET'], [$container->getParameter('hgabka_utils.google_api_key'), $container->getParameter('hgabka_utils.google_signin.client_id'), $container->getParameter('hgabka_utils.google_signin.client_secret')]);
        $this->assertFalse($container->getParameter('hgabka_utils.google_signin.enabled'));
    }

    public function testRecaptchaKeysAreInjectedIntoTheServices(): void
    {
        $container = $this->container();

        (new HgabkaUtilsExtension())->load([['recaptcha' => ['site_key' => 'SITE', 'secret' => 'SECRET']]], $container);

        $this->assertSame('SITE', $container->getDefinition('hgabka_utils.form.recaptcha_type')->getArgument(0));
        $this->assertSame('SITE', $container->getDefinition('hgabka_utils.form.invisible_recaptcha_type')->getArgument(0));
        $this->assertSame('SECRET', $container->getDefinition('hgabka_utils.validator.recaptcha')->getArgument(2));
    }

    public function testEnumTypesAreAutoconfigured(): void
    {
        $container = $this->container();

        (new HgabkaUtilsExtension())->load([[]], $container);

        $this->assertArrayHasKey('hg_utils.doctrine_enum_type', $container->getAutoconfiguredInstanceof()[EnumTypeInterface::class]->getTags());
    }

    public function testCompilerPassRegistersHydratorsAndDqlFunctions(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.entity_managers', ['default' => 'x', 'second' => 'y']);
        $container->setDefinition('doctrine.orm.default_configuration', new Definition('stdClass'));
        $container->setDefinition('doctrine.orm.second_configuration', new Definition('stdClass'));
        $container->setParameter('liip_imagine.filter_sets', ['existing' => []]);
        $container->setDefinition(MenuBuilder::class, new Definition(MenuBuilder::class));
        $container->setDefinition('menu.adaptor', (new Definition('stdClass'))->addTag('hgabka_utils.menu.adaptor', ['priority' => 5]));

        (new HgabkaUtilsExtension())->process($container);

        $this->assertArrayHasKey('hgabka_utils_slider_fill', $container->getParameter('liip_imagine.filter_sets'));
        $this->assertArrayHasKey('existing', $container->getParameter('liip_imagine.filter_sets'));
        $this->assertEquals([['addAdaptMenu', [new \Symfony\Component\DependencyInjection\Reference('menu.adaptor'), 5]]], $container->getDefinition(MenuBuilder::class)->getMethodCalls());

        foreach (['default', 'second'] as $name) {
            $calls = $container->getDefinition('doctrine.orm.' . $name . '_configuration')->getMethodCalls();
            $hydrators = array_column(array_filter($calls, static fn (array $c) => 'addCustomHydrationMode' === $c[0]), 1);
            $this->assertEqualsCanonicalizing(
                [['key_value', KeyValueHydrator::class], ['column', ColumnHydrator::class], ['count', CountHydrator::class], ['indexed', IndexedHydrator::class]],
                $hydrators
            );
            $this->assertContains(['IFNULL', IfNull::class], array_map(static fn (array $c) => $c[1], array_filter($calls, static fn (array $c) => 'addCustomStringFunction' === $c[0])));
            $this->assertCount(21, $calls);
        }
    }
}
