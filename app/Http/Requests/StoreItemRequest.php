<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'deck_id' => ['required','integer','exists:decks,id'],
            'type'    => ['required', Rule::in(['flashcard','mcq','matching'])],
            'front'   => ['nullable','string'],
            'back'    => ['nullable','string'],
            'data'    => ['nullable','array'],

            // Ràng buộc theo type
            // flashcard: cần ít nhất front hoặc back
            'front'   => ['required_if:type,flashcard','nullable','string'],
            'back'    => ['required_if:type,flashcard','nullable','string'],

            // mcq: cần data.choices (array >=2) và data.answer (int index hợp lệ)
            'data.choices' => ['required_if:type,mcq','array','min:2'],
            'data.answer'  => ['required_if:type,mcq','integer','min:0'],

            // matching: cần data.pairs (array các cặp [left,right]) với min 2
            'data.pairs' => ['required_if:type,matching','array','min:2'],
            'data.pairs.*.0' => ['required_if:type,matching','string'],
            'data.pairs.*.1' => ['required_if:type,matching','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'front.required_if' => 'Flashcard cần front hoặc back.',
            'back.required_if'  => 'Flashcard cần front hoặc back.',
            'data.choices.required_if' => 'MCQ cần mảng choices.',
            'data.answer.required_if'  => 'MCQ cần chỉ mục đáp án (answer).',
            'data.pairs.required_if'   => 'Matching cần mảng các cặp pairs.',
        ];
    }
}
