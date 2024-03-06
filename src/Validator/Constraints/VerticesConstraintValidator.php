<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UnexpectedValueException;

class VerticesConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
          if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
          // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
          throw new UnexpectedValueException($value, 'string');

          // separate multiple types using pipes
          // throw new UnexpectedValueException($value, 'string|int');
      }
        if (!$this->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValid($value)
    {
        $points = explode(',',$value);
        if(count($points) < 6) return false; else return true;
    }
}
