<?php

namespace App\Rules;

use App\Models\Employee;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class OneEmployeeOneTask implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {   
        $employee=Employee::find($value);
        if ($employee==null) {
            $fail('employee does not exist.');
        }
        $activeTask=$employee->tasks()->where('finished',false)->exists();
        if ($activeTask==true) {
            $fail('the employee is already busy with another task');
        }
    }
}
