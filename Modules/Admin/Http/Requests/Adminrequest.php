<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HttpResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class adminrequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        return [
            // $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admin',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'password' => 'required|string|min:6',
            // ]);
        ];
    }

     /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
