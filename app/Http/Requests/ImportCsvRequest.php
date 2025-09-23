<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => ['required','file','mimes:csv,txt','max:5120'], // <=5MB
            // (Optional) mapping preview:
            'commit' => ['sometimes','boolean'],
        ];
    }
}
