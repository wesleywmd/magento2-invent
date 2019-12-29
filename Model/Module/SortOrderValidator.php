<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

class SortOrderValidator extends AbstractValidator
{
    protected $key = 'sortOrder';

    public function validate($sortOrder)
    {
        $this->validateNotNull($sortOrder);
        $this->validateNoWhitespace($sortOrder);
        $this->validateNumeric($sortOrder);
        return $sortOrder;
    }
}