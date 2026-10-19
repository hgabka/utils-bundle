<?php

namespace Hgabka\UtilsBundle\Tests\Twig;

use Hgabka\UtilsBundle\Helper\Formatter;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Hgabka\UtilsBundle\Helper\Slugifier;
use Hgabka\UtilsBundle\Twig\DateByLocaleExtension;
use Hgabka\UtilsBundle\Twig\Extension\UtilitiesTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigExtensionsTest extends TestCase
{
    private function twig(string $template): Environment
    {
        $twig = new Environment(new ArrayLoader(['t' => $template]), ['autoescape' => 'html']);
        $twig->addExtension(new UtilitiesTwigExtension(new Slugifier(), new Formatter($this->createStub(HgabkaUtils::class))));

        return $twig;
    }

    public function testFiltersAreRegistered(): void
    {
        $extension = new UtilitiesTwigExtension(new Slugifier(), new Formatter($this->createStub(HgabkaUtils::class)));

        $this->assertSame(['slugify', 'utils_format_number', 'utils_format_price', 'hg_spaceless'], array_map(static fn ($f) => $f->getName(), $extension->getFilters()));
    }

    public function testSlugifyFilter(): void
    {
        $this->assertSame('arvizturo-tukorfurogep', $this->twig('{{ text|slugify }}')->render('t', ['text' => 'Árvíztűrő tükörfúrógép']));
        $this->assertSame('', $this->twig('{{ text|slugify }}')->render('t', ['text' => null]));
    }

    public function testNumberAndPriceFilters(): void
    {
        $twig = $this->twig("{{ n|utils_format_number }}|{{ n|utils_format_number(1) }}|{{ n|utils_format_price(0, ',', ' ', 'Ft') }}");

        $this->assertSame('1 235|1 234,6|1 235 Ft', $twig->render('t', ['n' => 1234.56]));
    }

    public function testSpacelessRemovesWhitespaceBetweenTags(): void
    {
        $twig = $this->twig('{% apply hg_spaceless %}<ul>  <li>a</li>' . "\n" . '  <li>b</li> </ul>{% endapply %}');

        $this->assertSame('<ul><li>a</li><li>b</li></ul>', $twig->render('t'));
    }

    public function testLocaleDateFilter(): void
    {
        $date = new \DateTime('2024-03-05 13:14:00', new \DateTimeZone('Europe/Brussels'));

        $this->assertSame('2024. március 5.', DateByLocaleExtension::localeDateFilter($date, 'hu', 'long'));
        $this->assertSame('March 5, 2024', DateByLocaleExtension::localeDateFilter($date, 'en', 'long'));
        $this->assertSame('05/03/2024 13:14', DateByLocaleExtension::localeDateFilter($date, 'hu', 'none', 'none', 'dd/MM/yyyy HH:mm'));
    }
}
