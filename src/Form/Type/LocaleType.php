<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    public function __construct(private readonly HgabkaUtils $hgabkaUtils) {}

    public function configureOptions(OptionsResolver $resolver): void
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

    public function getParent(): string
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
