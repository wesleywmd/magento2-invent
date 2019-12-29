<?php
namespace Wesleywmd\Invent\Model\Config;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Model\Component\AbstractValidator;

class TabIdValidator extends AbstractValidator
{
    protected $key = 'tabId';

    public function validate($tabId)
    {
        $this->validateNotNull($tabId);
        $this->validateNoWhitespace($tabId);
        $this->validateAlphaWithSpecial($tabId,'_');
        return $tabId;
    }
}