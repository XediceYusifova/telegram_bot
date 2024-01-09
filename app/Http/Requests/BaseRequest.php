<?php


namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class BaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors(), __("Validation Error"), Response::HTTP_BAD_REQUEST])
        );
    }

    protected function modify($text): string
    {
        return str(__($text))->title();
    }

    public function validationData()
    {
        // Add the Route parameters to you data under validation
        return array_merge($this->all(), $this->route()->parameters());
    }
}
