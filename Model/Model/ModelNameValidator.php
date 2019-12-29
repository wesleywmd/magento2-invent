<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

class ModelNameValidator extends AbstractValidator
{
    protected $key = 'modelName';

    public function validate($modelName)
    {
        $this->validateNotNull($modelName);
        $this->validateNoWhitespace($modelName);
        $this->validateAlpha($modelName);
        return $modelName;
    }
}