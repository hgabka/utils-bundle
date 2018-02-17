<?php

namespace Hgabka\UtilsBundle\Enums;

use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

abstract class ConstantsChoiceLoader extends CallbackChoiceLoader
{
    public function __construct()
    {
        $refl = new \ReflectionClass($this);
        $prefix = static::getI18nPrefix();

        parent::__construct(function () use ($refl, $prefix) {
            $constants = $refl->getConstants();
            if (!empty($prefix)) {
                $labels = [];
                foreach ($constants as $constant) {
                    $labels[] = $prefix.$constant;
                }
            } else {
                $labels = $constants;
            }

            return array_combine($labels, $constants);
        });
    }

    /**
     * Sonata admin számára formázva a tömb.
     *
     * @return array
     */
    public function loadSonataChoices()
    {
        return array_flip($this->loadChoiceList()->getStructuredValues());
    }

    /**
     * Egy elemhez css class csatolása.
     *
     * @param string $choice
     *
     * @return string
     */
    public function getCssClassForChoice($choice)
    {
        return '';
    }

    public static function getI18nPrefix()
    {
        return '';
    }
}
