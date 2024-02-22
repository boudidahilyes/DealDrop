<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]

class VerticesConstraint extends Constraint
{
    public string $message = 'Area should have a minimun of 3 points';
    public string $mode = 'strict';
    
    public function __construct(string $mode = null, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}

