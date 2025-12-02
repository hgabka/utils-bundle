<?php

namespace Hgabka\UtilsBundle\Form\Type;

class EntityAutocompleteType extends ObjectAutocompleteType
{
    public function getBlockPrefix(): string
    {
        return 'entity_autocomplete';
    }
}
