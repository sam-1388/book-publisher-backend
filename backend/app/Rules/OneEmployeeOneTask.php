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
        if (Employee::find($value)==null) {
            $fail('employee does not exist.');
        }
        $employee=Employee::find($value);
        $activeTask=$employee->tasks()->where('finished',false)->exists();
        if ($activeTask) {
            $fail('the employee is already busy with another task');
        }
    }
}
