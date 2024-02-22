<?php

namespace App\Validator\Constraints;

use Location\Coordinate;
use Location\Polygon;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UnexpectedValueException;

class AreaConstraintValidator extends ConstraintValidator
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
      $parcedCoords = explode(',', $value);
       
      $polygon = new Polygon();
      for($i = 0; $i < count($parcedCoords); $i+=2){
          $polygon->addPoint(new Coordinate(floatval($parcedCoords[$i]),floatval($parcedCoords[$i + 1])));
      }
      return $polygon->getArea() * 1110000 > 10000;
    }
}
