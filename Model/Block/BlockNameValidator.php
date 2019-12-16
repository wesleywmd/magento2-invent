<?php
namespace Wesleywmd\Invent\Model\Block;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;

class BlockNameValidator extends AbstractValidator
{
    protected $key = 'blockName';

    public function validate($blockName)
    {
        $this->validateNotNull($blockName);
        $this->validateNoWhitespace($blockName);
        $this->validateAlphaNumericWithSpecial($blockName,'\/');
        return $blockName;
    }
}