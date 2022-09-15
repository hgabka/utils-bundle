<?php

namespace Hgabka\UtilsBundle\Form\Type;

use BackedEnum;
use Closure;
use UnitEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatedEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['class'])
            ->setAllowedTypes('class', 'string')
            ->setAllowedValues('class', Closure::fromCallable('enum_exists'))
            ->setDefault('translation_prefix', '')
            ->setDefault('choices', static function (Options $options): array {
                if (empty($options['translation_prefix'])) {
                    return $options['class']::cases();
                }
                else {
                    return array_map(fn(string $value, string $key) => $options['translation_prefix'].$value, $options['class']::cases());
                }
            })
            ->setDefault('choice_label', static function (UnitEnum $choice, $key, $value): string {
                return (empty($options['translation_prefix']) ? '' : $options['translation_prefix']) . $choice->name;
            })
            ->setDefault('choice_value', static function (Options $options): ?\Closure {
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
