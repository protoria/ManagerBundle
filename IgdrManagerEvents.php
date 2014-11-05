<?php
namespace Igdr\Bundle\ManagerBundle;

/**
 * Class IgdrManagerBundle
 */
final class IgdrManagerEvents
{
    const EVENT_INITIALIZE = 'initialize';

    const EVENT_BEFORE_CREATE = 'before_create';
    const EVENT_AFTER_CREATE  = 'after_create';

    const EVENT_BEFORE_UPDATE = 'before_update';
    const EVENT_AFTER_UPDATE  = 'after_update';

    const EVENT_BEFORE_DELETE = 'before_delete';
    const EVENT_AFTER_DELETE  = 'after_delete';
}
