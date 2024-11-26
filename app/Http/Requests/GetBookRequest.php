<?php

namespace App\Http\Requests;

use App\Rules\MultipleOfTwenty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetBookRequest extends FormRequest
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
            'author' => 'string',
            'isbn' => 'array',
            'title' => 'string',
            'offset' => ['integer','gte:0', new MultipleOfTwenty]
        ];
    }

//    protected function failedValidation(Validator $validator)
//    {
//        $errors = $validator->errors();
//
//        return response()->json([
//            'message' => 'Invalid data send',
//            'details' => $errors->messages(),
//        ], 422);
//    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }


}
