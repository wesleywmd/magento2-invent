<?php
namespace Wesleywmd\Invent\Model\Command;

use Wesleywmd\Invent\Console\AbstractValidator;

class CommandNameValidator extends AbstractValidator
{
    protected $key = 'commandName';

    public function validate($commandName)
    {
        $this->validateNotNull($commandName);
        $this->validateNoWhitespace($commandName);
        $this->validateAlphaNumericWithSpecial($commandName,':');
        return $commandName;
    }
}