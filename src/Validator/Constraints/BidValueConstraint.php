<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]

class BidValueConstraint extends Constraint
{
    public string $message = 'Bid Value Should Be Greater Than The Highest Bid Value';
    public $attribute;
    public function __construct(string $attribute, $options = null)
    {
        parent::__construct($options);
        $this->attribute = $attribute;
    }
}