<?php

namespace App\Enums;

enum ReviewRating:int {
    case AGAIN = 1;
    case HARD  = 2;
    case GOOD  = 3;
    case EASY  = 4;
}
