<?php
namespace Wesleywmd\Invent\Model\Block;

use Magento\Setup\Console\InputValidationException;
use Magento\Setup\Console\Style\MagentoStyleInterface;
use Wesleywmd\Invent\Model\Component\AbstractValidator;

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