<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

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