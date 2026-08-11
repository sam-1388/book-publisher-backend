<?php

namespace App\Http\Requests;

use App\Models\Occupation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Override;


class StoreEmployeeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validator = Validator::make(
            $this->all(),
            [
                'name' => ['required'],
                'age' => ['required', 'integer', 'Between:18,80'],
                'rating' => ['nullable', Rule::in([1, 2, 3, 4, 5], 'integer')],
                'image' => ['nullable', 'image'],
                'occupations.*'=>[Rule::exists('occupation','name')],
            ]
        );

        return $validator->validate();
    }

    
}
