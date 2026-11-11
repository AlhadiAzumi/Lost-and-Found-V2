<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case ClaimInProgress = 'claim_in_progress';
    case Returned = 'returned';
    case Closed = 'closed';
}
