<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    /** @var HgabkaUtils */
    protected $hgabkaUtils;

    /**
     * LocaleType constructor.
     */
    public function __construct(HgabkaUtils $hgabkaUtils)
    {
        $this->hgabkaUtils = $hgabkaUtils;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $cultures = $this->hgabkaUtils->getLocaleChoices();

        $resolver
            ->setDefaults([
                'locales' => null,
                'choices' => array_flip($cultures),
            ]);

        $resolver->setNormalizer('choices', function ($resolver, $data) {
            $locales = $resolver->offsetGet('locales');
            if (!empty($locales)) {
                $result = [];
                foreach ($data as $label => $key) {
                    if (\in_array($key, $locales, true)) {
                        $result[$label] = $key;
                    }
                }

                return $result;
            }

            return $data;
        });
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'locale';
    }
}
