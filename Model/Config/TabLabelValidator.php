<?php
namespace Wesleywmd\Invent\Model\Config;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Model\Component\AbstractValidator;

class TabLabelValidator extends AbstractValidator
{
    protected $key = 'label';

    public function validate($label)
    {
        $this->validateAlphaWithSpecial($label,'\s');
        return $label;
    }
}