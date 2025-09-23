<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'deck_id' => ['sometimes','required','integer','exists:decks,id'],
            'type'    => ['sometimes','required', Rule::in(['flashcard','mcq','matching'])],
            'front'   => ['sometimes','nullable','string'],
            'back'    => ['sometimes','nullable','string'],
            'data'    => ['sometimes','nullable','array'],

            // Khi đổi type -> áp ràng buộc tương ứng
            'data.choices' => ['required_if:type,mcq','array','min:2'],
            'data.answer'  => ['required_if:type,mcq','integer','min:0'],
            'data.pairs'   => ['required_if:type,matching','array','min:2'],
            'data.pairs.*.0' => ['required_if:type,matching','string'],
            'data.pairs.*.1' => ['required_if:type,matching','string'],
        ];
    }
}
