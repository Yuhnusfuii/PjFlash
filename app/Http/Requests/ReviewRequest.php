<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // rating string: again|hard|good|easy (controller sẽ convert sang enum)
        return [
            'rating'      => ['required','string','in:again,hard,good,easy'],
            'duration_ms' => ['nullable','integer','min:0'],
            'meta'        => ['nullable','array'],
        ];
    }
}
