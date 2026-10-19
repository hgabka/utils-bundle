<?php

namespace Hgabka\UtilsBundle\Tests\Helper;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class HgabkaUtilsTest extends TestCase
{
    private RequestStack $requests;

    private function utils(array $parameters = []): HgabkaUtils
    {
        $this->requests = new RequestStack();
        $router = $this->createStub(RouterInterface::class);
        $router->method('getContext')->willReturn(new RequestContext('', 'GET', 'router.test', 'https', 80, 8443));
        $container = new Container(new ParameterBag($parameters + [
            'requiredlocales' => 'hu|en|de',
            'defaultlocale' => 'hu',
            'kernel.project_dir' => '/project',
        ]));
        $container->set('request_stack', $this->requests);
        $container->set('router', $router);

        return new HgabkaUtils($container);
    }

    public function testLocales(): void
    {
        $utils = $this->utils();

        $this->assertSame(['hu', 'en', 'de'], $utils->getAvailableLocales());
        $this->assertSame('hu', $utils->getDefaultLocale());
    }

    public function testCurrentLocalePrefersAnAvailableArgument(): void
    {
        $utils = $this->utils();
        $this->requests->push($this->requestWithLocale('de'));

        $this->assertSame('en', $utils->getCurrentLocale('en'));
        $this->assertSame('de', $utils->getCurrentLocale());
        $this->assertSame('de', $utils->getCurrentLocale('fr') ?: 'x', 'an unknown argument falls through to the request locale');
    }

    public function testCurrentLocaleFallsBackToTheDefault(): void
    {
        $utils = $this->utils();

        $this->assertSame('hu', $utils->getCurrentLocale());

        $this->requests->push($this->requestWithLocale('fr'));
        $this->assertSame('hu', $utils->getCurrentLocale());
    }

    private function requestWithLocale(string $locale): Request
    {
        $request = Request::create('https://shop.test:8443/x');
        $request->setLocale($locale);

        return $request;
    }

    public function testRequestBasedUrlParts(): void
    {
        $utils = $this->utils();
        $this->assertSame(['https', 'router.test', 8443], [$utils->getScheme(), $utils->getHost(), $utils->getPort()]);

        $this->requests->push($this->requestWithLocale('hu'));
        $this->assertSame(['https', 'shop.test', 8443], [$utils->getScheme(), $utils->getHost(), $utils->getPort()]);
    }

    public function testProjectPaths(): void
    {
        $utils = $this->utils();

        $this->assertSame('/project', $utils->getProjectDir());
        $this->assertSame('/project/web', $utils->getWebDir());
    }

    #[DataProvider('slugs')]
    public function testSlugify(string $text, string $expected): void
    {
        $this->assertSame($expected, $this->utils()->slugify($text));
    }

    public static function slugs(): array
    {
        return [
            'accents' => ['Árvíztűrő tükörfúrógép', 'arvizturo-tukorfurogep'],
            'punctuation' => ['Hello, World!', 'hello-world'],
            'apostrophes are word breaks' => ["Rock'n'Roll", 'rock-n-roll'],
            'underscores become dashes' => ['some_slug_1', 'some-slug-1'],
            'trimmed' => ['  --Hello--  ', 'hello'],
        ];
    }

    public function testSlugifyDefaultAndDelimiter(): void
    {
        $utils = $this->utils();

        $this->assertSame('', $utils->slugify('!!!'));
        $this->assertSame('fallback', $utils->slugify('!!!', 'fallback'));
        $this->assertSame('hello_world', $utils->slugify('Hello World', '', ["'"], '_'));
    }

    #[DataProvider('inflections')]
    public function testInflections(string $method, string $input, string $expected): void
    {
        $this->assertSame($expected, $this->utils()->$method($input));
    }

    public static function inflections(): array
    {
        return [
            'underscore' => ['underscore', 'FooBarBaz', 'foo_bar_baz'],
            'underscore acronym' => ['underscore', 'HTMLParser', 'html_parser'],
            'underscore namespace' => ['underscore', 'App::UserGroup', 'app/user_group'],
            'tableize' => ['tableize', 'BlogPost', 'blog_post'],
            'demodulize' => ['demodulize', 'App::Models::User', 'User'],
            'humanize' => ['humanize', 'first_name', 'First name'],
            'humanize strips _id' => ['humanize', 'author_id', 'Author'],
            'camelize' => ['camelize', 'foo_bar', 'FooBar'],
            'classify' => ['classify', 'blog_post', 'BlogPost'],
        ];
    }

    public function testForeignKey(): void
    {
        $utils = $this->utils();

        $this->assertSame('blog_post_id', $utils->foreign_key('App::BlogPost'));
        $this->assertSame('blog_postid', $utils->foreign_key('BlogPost', false));
    }

    public function testEntityFromArrayUsesSettersOnly(): void
    {
        $entity = new class() {
            public ?string $name = null;
            public ?int $age = 1;

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $this->utils()->entityFromArray($entity, ['name' => 'Ann', 'age' => 30, 'unknown' => 'x']);

        $this->assertSame(['Ann', 1], [$entity->name, $entity->age]);
    }

    public function testArrayValueForPath(): void
    {
        $utils = $this->utils();
        $values = ['a' => 1, 'user' => ['name' => 'Ann', 'tags' => ['x', 'y']]];

        $this->assertSame(1, $utils->getArrayValueForPath($values, 'a'));
        $this->assertSame('Ann', $utils->getArrayValueForPath($values, 'user[name]'));
        $this->assertSame(['x', 'y'], $utils->getArrayValueForPath($values, 'user[tags]'));
        $this->assertSame(['x', 'y'], $utils->getArrayValueForPath($values, 'user[tags][]'));
        $this->assertSame('dflt', $utils->getArrayValueForPath($values, 'missing', 'dflt'));
        $this->assertSame('dflt', $utils->getArrayValueForPath($values, 'user[nope]', 'dflt'));
        $this->assertSame('dflt', $utils->getArrayValueForPath($values, 'a[]', 'dflt'));
    }

    public function testIsUtf8(): void
    {
        $utils = $this->utils();

        $this->assertTrue($utils->isUTF8('plain ascii'));
        $this->assertTrue($utils->isUTF8('árvíztűrő €'));
        $this->assertFalse($utils->isUTF8("\xE1rv\xEDz"));
        $this->assertFalse($utils->isUTF8("\xC3"));
    }

    public function testArrayDeepMerge(): void
    {
        $utils = $this->utils();

        $merged = $utils->arrayDeepMerge(['a' => 1, 'n' => ['x' => 1, 'y' => 2]], ['a' => 2, 'n' => ['y' => 3, 'z' => 4], 'b' => 5]);

        $this->assertSame(['a' => 2, 'n' => ['x' => 1, 'y' => 3, 'z' => 4], 'b' => 5], $merged);
        $this->assertFalse($utils->arrayDeepMerge());
        $this->assertSame(['a' => 3], $utils->arrayDeepMerge(['a' => 1], ['a' => 2], ['a' => 3]));
    }

    public function testStripslashesDeep(): void
    {
        $this->assertSame(['a' => "it's", 'b' => ['c' => 'x"y']], $this->utils()->stripslashesDeep(['a' => "it\\'s", 'b' => ['c' => 'x\\"y']]));
    }

    public function testStripComments(): void
    {
        $source = "<?php\n// line comment\n\$a = 1; /* block */ \$b = 2; /** doc */\n";

        $stripped = $this->utils()->stripComments($source);

        $this->assertStringNotContainsString('comment', $stripped);
        $this->assertStringNotContainsString('block', $stripped);
        $this->assertStringContainsString('$a = 1;', $stripped);
    }

    #[DataProvider('paths')]
    public function testIsPathAbsolute(string $path, bool $expected): void
    {
        $this->assertSame($expected, $this->utils()->isPathAbsolute($path));
    }

    public static function paths(): array
    {
        return [['/usr/bin', true], ['\\share\\x', true], ['C:\\dir', true], ['c:/dir', true], ['relative/path', false], ['C:x', false]];
    }

    public function testExtractClassName(): void
    {
        $utils = $this->utils();

        $this->assertSame('Foo', $utils->extractClassName('Foo.class.php'));
        $this->assertSame('Bar', $utils->extractClassName('/some/dir/Bar.interface.php'));
        $this->assertNull($utils->extractClassName('plain.php'));
    }

    public function testRomanNumbers(): void
    {
        $utils = $this->utils();

        $this->assertTrue($utils->isRomanNumber('MCMXCIV'));
        $this->assertFalse($utils->isRomanNumber('IIII'));
        $this->assertSame(1994, $utils->Roman2Int('MCMXCIV'));
        $this->assertSame(4, $utils->Roman2Int('IV'));
        $this->assertSame(0, $utils->Roman2Int('N'));
        $this->assertFalse($utils->Roman2Int('ABC'));
        $this->assertSame('MCMXCIV', $utils->Int2Roman(1994));
        $this->assertSame('', $utils->Int2Roman(0));
    }

    public function testIsSlug(): void
    {
        $utils = $this->utils();

        $this->assertTrue((bool) $utils->isSlug('my-slug_1'));
        $this->assertFalse((bool) $utils->isSlug('ab'));
        $this->assertFalse((bool) $utils->isSlug('has space'));
    }

    public function testMbUcfirst(): void
    {
        $utils = $this->utils();

        $this->assertSame('Évike', $utils->mbUcfirst('évike'));
        $this->assertSame('Évike BÉLA', $utils->mbUcfirst('évike BÉLA'));
        $this->assertSame('Évike béla', $utils->mbUcfirst('évike BÉLA', 'UTF-8', true));
    }

    public function testStringHelpers(): void
    {
        $utils = $this->utils();

        $this->assertSame('ab', $utils->stripNewlines("a\r\nb"));
        $this->assertSame(5, $utils->stripNewlines(5));
        $this->assertSame('Arvizturo TukorfUrogep', $utils->removeAccents('Árvíztűrő TükörfÚrógép'));
        $this->assertSame('&lt;a href=&quot;x&quot;&gt;it&apos;s &amp;&lt;/a&gt;', $utils->xmlEscape('<a href="x">it\'s &</a>'));
        $this->assertSame(['a' => 'p_a', 'b' => 'p_b'], $utils->prefixArrayElements(['a', 'b'], 'p_'));
        $this->assertSame('Hello world', $utils->htmlToText('<p>Hello <b>world</b></p>'));
        $this->assertSame('a & b', $utils->htmlToText('a &amp; b'));
    }

    public function testPermuteUnique(): void
    {
        $result = $this->utils()->permuteUnique([1, 1, 2]);

        $this->assertCount(3, $result);
        $this->assertEqualsCanonicalizing([[1, 1, 2], [1, 2, 1], [2, 1, 1]], $result);
    }

    public function testDates(): void
    {
        $utils = $this->utils();

        $this->assertSame(['2024-01-01', '2024-01-02', '2024-01-03'], $utils->getDatePeriod('2024-01-01', '2024-01-04', null, true));
        $this->assertSame(['2024-01-01', '2024-01-08'], $utils->getDatePeriod('2024-01-01', '2024-01-15', 'P1W', true));
        $this->assertSame(30, $utils->getDateDiff('2024-01-01', '2024-01-31'));
        $this->assertSame('+30 days', $utils->getDateDiff('2024-01-01', '2024-01-31', '%R%a days'));
    }

    public function testCreateDateTime(): void
    {
        $utils = $this->utils();
        $date = new \DateTime('2024-05-06');

        $this->assertSame($date, $utils->createDateTime($date));
        $this->assertNull($utils->createDateTime(null));
        $this->assertSame('2024-05-06', $utils->createDateTime('2024-05-06')->format('Y-m-d'));
        $this->assertSame(86400, $utils->createDateTime('86400')->getTimestamp());
        $this->assertNull($utils->createDateTime('definitely not a date'));
    }

    public function testCreateDateTimeCanThrow(): void
    {
        $this->expectException(\Exception::class);
        $this->utils()->createDateTime('definitely not a date', true);
    }

    public function testMergeClassesReplacesConflictingBootstrapClasses(): void
    {
        $utils = $this->utils();

        $this->assertSame('btn btn-danger', $utils->mergeClasses('btn btn-primary', 'btn-danger', true));
        $this->assertSame('col-sm-12 col-md-6', $utils->mergeClasses('col-md-4 col-sm-12', 'col-md-6', true));
        $this->assertSame('a b', $utils->mergeClasses(['a'], ['b', 'a'], true));
        $this->assertSame(['a', 'b'], $utils->mergeClasses('a b', null));
    }

    public function testTextShortening(): void
    {
        $utils = $this->utils();

        $this->assertSame('short', $utils->truncateToNearestWord('short', 10));
        $this->assertSame('The quick', $utils->truncateToNearestWord('The quick brown fox', 12));
        $this->assertSame('The quick', $utils->truncateToNearestWord('The quick brown fox', 10));
        $this->assertSame('Árvíz', $utils->truncateToNearestWord('Árvíz tűrő tükör', 8));

        $this->assertSame('short', $utils->shortenText('short', 100));
        $this->assertSame('First sentence.', $utils->shortenText('First sentence. Second sentence.', 5));
        $this->assertSame('First&hellip;', $utils->shortenText('First sentence. Second sentence.', 8, true));
    }

    public function testReplaceExtension(): void
    {
        $utils = $this->utils();

        $this->assertSame('/a/b/file.yml', $utils->replaceExtension('/a/b/file.yaml', 'yml'));
        $this->assertSame('/a/b/file.yml', $utils->replaceExtension('/a/b/file.yaml', '.yml'));
        $this->assertSame('/a/b/file', $utils->replaceExtension('/a/b/file.yaml', ''));
    }

    public function testTimezones(): void
    {
        $utils = $this->utils();

        $this->assertArrayHasKey('Europe/Budapest', $utils->getTimezoneChoices());
        $this->assertContains('Europe/Budapest', $utils->getTimezones());
        $this->assertSame('Europe/Budapest', $utils->getTimezoneChoices()['Europe/Budapest']);
    }
}
