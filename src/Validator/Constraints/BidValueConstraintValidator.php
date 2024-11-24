<?php

namespace App\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UnexpectedValueException;

class BidValueConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
          if (null === $value || '' === $value) {
            return;
        }

        if (!is_numeric($value)) {
          // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
          throw new UnexpectedValueException($value, 'numeric');
      }
        $object = $this->context->getObject();
        $propertyAccessor = new PropertyAccessor();

        // Access the specified attribute using property path
        $attributeValue = $propertyAccessor->getValue($object, $constraint->attribute);
        if( empty($attributeValue)) return;
        foreach($attributeValue AS $bid){
            if($bid->isHighest()){
                if($bid->getValue() > $value)
                    $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
        return;

        
    }

}