<?php

namespace App\Http\Requests;

class StoreBrandRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string'
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
            'title.required' => 'Başlıq bölməsi məcburidir.',
            'title.string'   => 'Başlıq mətn olmalıdır.',
        ];
    }
}
