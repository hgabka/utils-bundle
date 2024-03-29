<?php

namespace Hgabka\UtilsBundle\Event;

class Events
{
    /**
     * The onDeepClone event occurs for a given entity while it's being deep cloned. here it's possible to set
     * certain fields of the cloned entity before it's being saved.
     *
     * @var string
     */
    public const DEEP_CLONE_AND_SAVE = 'hgabka_utils.onDeepCloneAndSave';

    /**
     * The postDeepClone event occurs for a given entity after it has been deep cloned.
     *
     * @var string
     */
    public const POST_DEEP_CLONE_AND_SAVE = 'hgabka_utils.postDeepCloneAndSave';

    /**
     * The adapt_simple_form event occurs after a simple form is created, here it's possible to add a tabPane to a form without
     * the need for the form to be connected to a node.
     *
     * @var string
     */
    public const ADAPT_SIMPLE_FORM = 'hgabka_utils.adaptSimpleForm';
}
