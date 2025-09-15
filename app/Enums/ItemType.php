<?php
declare(strict_types=1);

namespace App\Enums;

enum ItemType: string
{
    case FLASHCARD = 'flashcard';
    case MCQ       = 'mcq';
    case MATCHING  = 'matching';
}
