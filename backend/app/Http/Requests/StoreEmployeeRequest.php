<?php

namespace App\Http\Requests;

use App\Models\Occupation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
    
    return
      [
        'name' => ['required'],
        'age' => ['required', 'integer', 'Between:18,80'],
        'rating' => ['nullable', Rule::in([1, 2, 3, 4, 5]), 'integer'],
        'image' => ['nullable', 'image'],
        'selectedOccupations'=>['required'],
        'selectedOccupations.*' => [Rule::exists('occupations', 'id')],
      ];
  }

  #[Override]
  public function failedValidation(Validator $validator)
  {
    throw new HttpResponseException(
      response($validator->errors(),422)
    );
  }

  #[Override]
  public function messages()
  {
    return[
      'rating.in'=>'the rating must be between 1 and 5',
      'selectedOccupations'=>'please select atleast one occupation or more'
    ];
  }

}
