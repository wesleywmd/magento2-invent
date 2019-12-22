<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

class RouterValidator extends AbstractValidator
{
    protected $key = 'router';

    public function validate($router)
    {
        $this->validateNotNull($router);
        $this->validateNoWhitespace($router);
        $this->validateAlpha($router);
        return $router;
    }
}