<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class ProfilePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // đã có middleware auth, nhưng cứ trả true để test không fail Authorization
        return auth::check();
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
        ];
    }
}
