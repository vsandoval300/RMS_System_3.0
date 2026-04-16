<?php

namespace App\Enums;

enum TransactionLifecycleStatus: string
{
    case PENDING    = '1';
    case IN_PROCESS = '2';
    case COMPLETED  = '3';
}