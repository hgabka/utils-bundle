<?php

namespace Hgabka\UtilsBundle\Traits;

use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslatableTrait as BaseTranslatableTrait;

trait TranslatableTrait
{
    use BaseTranslatableTrait;

    /**
     * @Hgabka\CurrentLocale
     */
    private $currentLocale;

    /**
     * Cache current translation. Useful in Doctrine 2.4+.
     */
    private $currentTranslation;

    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Translation helper method.
     *
     * @param null|mixed $locale
     */
    public function translate($locale = null)
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
