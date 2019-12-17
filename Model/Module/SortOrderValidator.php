<?php
namespace Wesleywmd\Invent\Model\Module;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;

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