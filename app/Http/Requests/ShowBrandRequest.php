<?php

namespace App\Http\Requests;

class ShowBrandRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => [
                'required',
                'exists:brands,uuid'
            ]
        ];
    }

    /**
     * Qaydalar üçün xəta mesajları
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'brand.required' => 'İd məcburidir.',
            'brand.exists'   => 'Marka tapılmadı.',
        ];
    }
}
