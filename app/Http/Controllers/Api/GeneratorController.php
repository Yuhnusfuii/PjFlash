<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class GeneratorController extends Controller
{
    /**
     * Generate MCQ for a given item.
     */
    public function generateMcq(Item $item)
    {
        // Dummy logic, replace with real generator later
        return response()->json([
            'question' => $item->front,
            'options'  => [$item->back, 'Option A', 'Option B', 'Option C'],
            'answer'   => $item->back,
        ]);
    }

    /**
     * Generate Matching exercise for a given deck.
     */
    public function generateMatching(Item $item)
    {
        // Dummy placeholder
        return response()->json([
            'pairs' => [
                ['left' => $item->front, 'right' => $item->back],
            ]
        ]);
    }
}
