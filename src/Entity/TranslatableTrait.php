<?php

namespace Hgabka\UtilsBundle\Entity;

use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslatableTrait as BaseTranslatableTrait;
use Hgabka\Doctrine\Translatable\TranslationInterface;

trait TranslatableTrait
{
    use BaseTranslatableTrait;

    /**
     * @Hgabka\CurrentLocale
     */
    #[Hgabka\CurrentLocale]
    private ?string $currentLocale = null;

    /**
     * Cache current translation. Useful in Doctrine 2.4+.
     */
    private ?TranslationInterface $currentTranslation;

    public function getCurrentLocale(): ?string
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale(?string $locale): self
    {
        $this->currentLocale = $locale;

        return $this;
    }

    public function translate(?string $locale = null): ?TranslationInterface
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $class = self::getTranslationEntityClass();
            $translation = new $class();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;

        return $translation;
    }
}
