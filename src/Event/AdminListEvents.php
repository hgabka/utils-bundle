<?php

namespace Hgabka\UtilsBundleBundle\Event;

class AdminListEvents
{
    /**
     * When adding, triggered after the form is validated,
     * but before the entity is persisted.
     */
    public const PRE_ADD = 'hgabka_utils.preAdd';

    /**
     * When adding, triggered after the entity is flushed.
     */
    public const POST_ADD = 'hgabka_utils.postAdd';

    /**
     * When editing, triggered after the form is validated,
     * but before the entity is persisted.
     */
    public const PRE_EDIT = 'hgabka_utils.preEdit';

    /**
     * When editing, triggered after the entity is flushed.
     */
    public const POST_EDIT = 'hgabka_utils.postEdit';

    /**
     * When deleting, triggered before the entity is removed.
     */
    public const PRE_DELETE = 'hgabka_utils.preDelete';

    /**
     * When deleting, triggered after the remove is flushed.
     */
    public const POST_DELETE = 'hgabka_utils.postDelete';
}
