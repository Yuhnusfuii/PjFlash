<?php

namespace App\Services\Contracts;

use App\Models\{Deck, Item};

interface McqGeneratorServiceInterface
{
    /**
     * Generate MCQ data for a given item (or deck context).
     * Return structure suggestion (flexible, tuỳ bạn đang dùng):
     * [
     *   'question' => string,
     *   'options'  => string[],   // >= 2
     *   'answer'   => int,        // index in options
     *   'meta'     => array,      // optional
     * ]
     */
    public function generate(Item $item, ?Deck $context = null, int $numOptions = 4): array;
}
