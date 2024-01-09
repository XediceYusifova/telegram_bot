<?php

namespace App\Http\Requests;

class UpdateBrandRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['uuid' => $this->route("brand")]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid'  => 'required|exists:brands,uuid',
            'title'  => 'required|string',
            'status' => 'required|boolean'
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
            'uuid.required' => 'UuID xanası məcburidir.',
            'uuid.exists'   => 'Marka tapılmadı.',
            'title.required' => 'Başlıq bölməsi məcburidir.',
            'title.string'   => 'Başlıq mətn olmalıdır.',
            'status.required' => 'Status bölməsi olmalıdır.',
            'status.boolean' => 'Status 1 və ya 0 olmalıdır.'
        ];
    }
}
