<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ComplexPattern extends Constraint
{
    public $message = 'This value doesn\'t match the required pattern';
    public $regexValid = [];
    public $regexInvalid = [];
}
