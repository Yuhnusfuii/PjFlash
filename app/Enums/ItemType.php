<?php
declare(strict_types=1);
namespace App\Enums;
enum ItemType:string {
    case FLASHCARD = 'flashcard'; // thẻ học
    case MCQ       = 'mcq';       // trắc nghiệm
    case MATCHING  = 'matching';  // ghép đôi
}