<?php

namespace App\Services\Contracts;

use App\Models\{Deck, Item};

interface MatchingGeneratorServiceInterface
{
    /**
     * Generate matching pairs for a given item (or deck context).
     * Return structure suggestion:
     * [
     *   'pairs' => [ ['left' => 'A', 'right' => '1'], ... ],
     *   'meta'  => array,
     * ]
     */
    public function generate(Item $item, ?Deck $context = null, int $numPairs = 4): array;
}
