<?php

namespace Hgabka\UtilsBundle\Form\Type;

class EntityAutocompleteType extends ObjectAutocompleteType
{
    public function getBlockPrefix()
    {
        return 'entity_autocomplete';
    }
}
