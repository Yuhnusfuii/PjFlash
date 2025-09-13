<?php
namespace App\Enums;

enum ItemType: string
{
    case FLASHCARD = 'flashcard';
    case MCQ       = 'mcq';
    case MATCHING  = 'matching';
}
