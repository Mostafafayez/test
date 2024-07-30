<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Helpers\ValidationRuleHelper;
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'email_or_phone' =>  ValidationRuleHelper::stringRules(),

            'address' => ValidationRuleHelper::stringRules(),
            'phonenum1' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ];
    }
}
