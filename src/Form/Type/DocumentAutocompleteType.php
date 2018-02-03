<?php

namespace Hgabka\UtilsBundle\Form\Type;

class DocumentAutocompleteType extends ObjectAutocompleteType
{
    public function getBlockPrefix()
    {
        return 'document_autocomplete';
    }
}
