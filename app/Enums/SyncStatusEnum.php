<?php
namespace App\Enums;

enum SyncStatusEnum: int
{
    case INCOMPLETE = 0;
    case COMPLETED = 1;
    case CANCELED = 2;

}