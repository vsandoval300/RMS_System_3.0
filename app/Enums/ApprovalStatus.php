<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case DRAFT     = 'DFT';
    case PENDING   = 'PND';
    case APPROVED  = 'APR';
    case REJECTED  = 'REJ';
    case CANCELLED = 'CAN';
}
