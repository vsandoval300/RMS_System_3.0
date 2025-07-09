<?php

namespace App\Enums;

enum BusinessLifecycleStatus: string
{
    case ON_HOLD = 'On Hold';
    case IN_FORCE = 'In Force';
    case TO_EXPIRE = 'To Expire';
    case EXPIRED = 'Expired';
    case CANCELLED = 'Cancelled';
}
