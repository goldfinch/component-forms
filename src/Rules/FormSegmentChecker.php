<?php

namespace Goldfinch\Component\Forms\Rules;

use Closure;
use Goldfinch\Component\Forms\Models\FormSegment;
use Illuminate\Contracts\Validation\ValidationRule;

class FormSegmentChecker implements ValidationRule
{
    private $segment_type;

    public function __construct($segment_type = null)
    {
        $this->segment_type = $segment_type;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value))
        {
            $fail('The :attribute invalid.');
        }

        $id = (int) $value;

        $segment = FormSegment::get()->byID($id);

        if (!$segment || !$segment->exists())
        {
            $fail('The :attribute invalid.');
        }
        else if ($this->segment_type)
        {
            if ($segment->Type != $this->segment_type)
            {
                $fail('The :attribute invalid.');
            }
        }
    }
}
