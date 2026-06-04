<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255', 
                Rule::unique(User::class)->ignore($this->user()->id)
            ],
            
            // PERBAIKAN: Tambahkan Rule::unique pada NIP
            'nip' => [
                'nullable', 'string', 'max:50', 
                Rule::unique(User::class)->ignore($this->user()->id)
            ],
            
            'jabatan' => ['nullable', 'string', 'max:255'],
            'regu' => ['nullable', 'string', 'max:50'],
            'regu_pengamanan' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];
    }

    // (Opsional) Tambahkan pesan error bahasa Indonesia
    public function messages(): array
    {
        return [
            'nip.unique' => 'NIP ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ];
    }
}