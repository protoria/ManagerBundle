<?php
namespace Igdr\Bundle\ManagerBundle;

/**
 * Class IgdrManagerBundle
 */
final class IgdrManagerEvents
{
    const SUFFIX_INITIALIZE = 'initialize';

    const SUFFIX_BEFORE_CREATE = 'before_create';
    const SUFFIX_AFTER_CREATE  = 'after_create';

    const SUFFIX_BEFORE_UPDATE = 'before_update';
    const SUFFIX_AFTER_UPDATE  = 'after_update';

    const SUFFIX_BEFORE_DELETE = 'before_delete';
    const SUFFIX_AFTER_DELETE  = 'after_delete';

    //global events fire for operations create, uodate, delete for any manager
    const EVENT_INITIALIZE = 'initialize';
    const EVENT_INIT_QUERY = 'before_find';

    const EVENT_BEFORE_CREATE = 'before_create';
    const EVENT_AFTER_CREATE  = 'after_create';

    const EVENT_BEFORE_UPDATE = 'before_update';
    const EVENT_AFTER_UPDATE  = 'after_update';

    const EVENT_BEFORE_DELETE = 'before_delete';
    const EVENT_AFTER_DELETE  = 'after_delete';
}
