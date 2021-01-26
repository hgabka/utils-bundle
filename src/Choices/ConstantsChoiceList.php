<?php

namespace Hgabka\UtilsBundle\Choices;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;

/**
 * Use the constants as choices.
 */
abstract class ConstantsChoiceList extends ArrayChoiceList implements \IteratorAggregate
{
    public function __construct()
    {
        $choices = $this->prefixElements($this->getAllConstants());

        parent::__construct($choices, function ($v) {
            // return the value or ArrayChoiceList will reindex with numerics
            return $v;
        });
    }

    public static function getI18nPrefix()
    {
        return null;
    }

    /**
     * i18n string.
     *
     * @param string $choice
     *
     * @return string
     */
    public static function getPrefixedChoice($choice)
    {
        return static::getI18nPrefix().$choice;
    }

    public function getIterator()
    {
        return new \ArrayIterator(array_flip($this->getStructuredValues()));
    }

    protected function getAllConstants()
    {
        $refl = new \ReflectionClass(static::class);

        return $refl->getConstants();
    }

    protected function prefixElements($array, $prefix = null)
    {
        $prefix = $prefix ?: static::getI18nPrefix();

        if ($prefix) {
            $ret = [];
            foreach ($array as $key => $el) {
                $ret[$el] = $prefix.$el;
            }

            return $ret;
        }

        return array_combine($array, $array);
    }
}
