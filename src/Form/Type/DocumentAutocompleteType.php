<?php

namespace Hgabka\UtilsBundle\Form\Type;

class DocumentAutocompleteType extends ObjectAutocompleteType
{
    public function getBlockPrefix(): string
    {
        return 'document_autocomplete';
    }
}
