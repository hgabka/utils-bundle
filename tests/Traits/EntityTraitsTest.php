<?php

namespace Hgabka\UtilsBundle\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\UtilsBundle\Traits\NestedTreeFieldsTrait;
use Hgabka\UtilsBundle\Traits\TimestampableEntity;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use PHPUnit\Framework\TestCase;

class EntityTraitsTest extends TestCase
{
    public function testTimestampableAccessors(): void
    {
        $entity = new class() {
            use TimestampableEntity;
        };
        $created = new \DateTime('2024-01-01');
        $updated = new \DateTime('2024-02-02');

        $this->assertSame($entity, $entity->setCreatedAt($created)->setUpdatedAt($updated));
        $this->assertSame([$created, $updated], [$entity->getCreatedAt(), $entity->getUpdatedAt()]);
        $this->assertNull($entity->setCreatedAt(null)->getCreatedAt());
    }

    public function testNestedTreeFields(): void
    {
        $node = new class() {
            use NestedTreeFieldsTrait;
        };
        $child = new $node();
        $children = new ArrayCollection([$child]);

        $this->assertSame([null, null, null, null, null], [$node->getLeft(), $node->getLevel(), $node->getRight(), $node->getRoot(), $node->getParent()]);
        $this->assertCount(0, $node->getChildren());

        $node->setLeft(1)->setLevel(0)->setRight(4)->setRoot($node)->setChildren($children);
        $child->setParent($node);

        $this->assertSame([1, 0, 4, $node, $children, $node], [$node->getLeft(), $node->getLevel(), $node->getRight(), $node->getRoot(), $node->getChildren(), $child->getParent()]);
    }

    private function translatable(): object
    {
        return new class() implements TranslatableInterface {
            use TranslatableTrait;

            #[Hgabka\Translations(targetEntity: TraitTranslation::class)]
            private Collection|array|null $translations;

            public function __construct()
            {
                $this->translations = new ArrayCollection();
            }

            public static function getTranslationEntityClass(): string
            {
                return TraitTranslation::class;
            }
        };
    }

    public function testTranslateCreatesTranslationsPerLocale(): void
    {
        $entity = $this->translatable();

        $hu = $entity->translate('hu');
        $en = $entity->translate('en');

        $this->assertInstanceOf(TraitTranslation::class, $hu);
        $this->assertSame('hu', $hu->getLocale());
        $this->assertNotSame($hu, $en);
        $this->assertSame($hu, $entity->translate('hu'));
        $this->assertCount(2, $entity->getTranslations());
        $this->assertSame($entity, $hu->getTranslatable());
    }

    public function testTranslateUsesTheCurrentLocaleByDefault(): void
    {
        $entity = $this->translatable();
        $entity->setCurrentLocale('de');

        $this->assertSame('de', $entity->translate()->getLocale());
        $this->assertSame('de', $entity->getCurrentLocale());
    }

    public function testTranslateWithoutAnyLocaleFails(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->translatable()->translate();
    }
}
