<?php

namespace Hgabka\UtilsBundle\Form\Type;

use BackedEnum;
use Closure;
use Hgabka\UtilsBundle\Enums\TranslatableEnumInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UnitEnum;

class TranslatedEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['class'])
            ->setAllowedTypes('class', 'string')
            ->setAllowedValues('class', Closure::fromCallable('enum_exists'))
            ->setDefault('exclude_cases', [])
            ->setAllowedTypes('exclude_cases', 'array')
            ->setDefault('choices', static function (Options $options): array {
                $cases = $options['class']::cases();

                if (!empty($options['exclude_cases'])) {
                    foreach ($options['exclude_cases'] as $excludeCase) {
                        if (false !== ($key = array_search($excludeCase, $cases))) {
                            unset($cases[$key]);
                        }
                    }
                }

                return $cases;
            })
            ->setDefault('choice_label', static function (UnitEnum $choice, $key, $value): string {
                return $choice instanceof TranslatableEnumInterface ? $choice->getTranslationPrefix() . $choice->value : $choice->name;
            })
            ->setDefault('choice_value', static function (Options $options): ?Closure {
                if (!is_a($options['class'], BackedEnum::class, true)) {
                    return null;
                }

                return static function (?BackedEnum $choice): ?string {
                    if (null === $choice) {
                        return null;
                    }

                    return (string) $choice->value;
                };
            })
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
