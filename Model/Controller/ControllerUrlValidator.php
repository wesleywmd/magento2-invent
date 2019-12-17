<?php
namespace Wesleywmd\Invent\Model\Controller;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;

class ControllerUrlValidator extends AbstractValidator
{
    protected $key = 'controllerUrl';

    public function validate($controllerUrl)
    {
        $this->validateNotNull($controllerUrl);
        $this->validateNoWhitespace($controllerUrl);
        $this->validateAlphaNumericWithSpecial($controllerUrl,'\/');
        return $controllerUrl;
    }
}