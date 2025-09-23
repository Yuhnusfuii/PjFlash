<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeckRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Chủ sở hữu được update: đã có Policy ở controller; ở đây chỉ cần logged-in
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes','required','string','max:255'],
            'description' => ['sometimes','nullable','string','max:1000'],
            'parent_id'   => ['sometimes','nullable','integer','exists:decks,id'],
        ];
    }
}
